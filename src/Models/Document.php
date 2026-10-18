<?php
namespace Models;

use Config\Database;
use PDO;
use Exception;

class Document {
    private $conn;
    private $movedLetters = []; // [from, to] letter file moves to undo if create() fails

    // 🔥 CICS document categories (key stored in documents.category)
    // series + code build the tracking ID: CICS-<series>-<code>-<YYYY>-<###>
    // Categories without a series are not officially coded: CICS-<code>-<YYYY>-<###>
    public const CATEGORIES = [
        'EIN'   => ['label' => 'External Incoming Communication',           'series' => null,   'code' => 'EIN'],
        'EC'    => ['label' => 'External Outgoing Communication',           'series' => '3330', 'code' => 'EC'],
        'IIN'   => ['label' => 'Internal Communication - Incoming',         'series' => null,   'code' => 'IIN'],
        'IC'    => ['label' => 'Internal Communication - Outgoing',         'series' => '3334', 'code' => 'IC'],
        'OO'    => ['label' => 'Office Order',                              'series' => '3336', 'code' => '00'],
        'MOM'   => ['label' => 'MOM',                                       'series' => '3337', 'code' => 'MOM'],
        'REP'   => ['label' => 'Reports (Monthly / Quarterly / Annual)',    'series' => '3338', 'code' => 'REP'],
        'TRANS' => ['label' => 'Transmittal',                               'series' => '3339', 'code' => 'TRANS'],
    ];

    public static function isValidCategory($category) {
        return isset(self::CATEGORIES[$category]);
    }

    public static function categoryLabel($category) {
        if (isset(self::CATEGORIES[$category])) {
            return self::CATEGORIES[$category]['label'];
        }
        return ($category !== null && $category !== '') ? $category : 'Uncategorized';
    }

    // Coded (outgoing) categories are written from public/Template.docx
    public static function usesTemplate($category) {
        return isset(self::CATEGORIES[$category]) && self::CATEGORIES[$category]['series'] !== null;
    }

    // Letters are kept outside public/ and served through index.php?action=letter_file
    // .docx = the generated letter, .json = the editor content used to reopen it
    public static function letterPath($tracking_id, $ext = 'docx') {
        if (!preg_match('/^[A-Za-z0-9-]+$/', (string) $tracking_id) || !in_array($ext, ['docx', 'json'], true)) {
            throw new Exception("Invalid letter path.");
        }
        return dirname(__DIR__, 2) . '/storage/letters/' . $tracking_id . '.' . $ext;
    }

    // SQL list of category keys for "category IN (...)" filters (built from constants only)
    private static function categorySqlList() {
        return "'" . implode("', '", array_keys(self::CATEGORIES)) . "'";
    }

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        // 🔥 CICS ONLY: Filter to only CICS documents with known categories
        $query = "SELECT d.*, u.name AS creator_name FROM documents d
                  LEFT JOIN users u ON u.id = d.creator_id
                  WHERE d.category IN (" . self::categorySqlList() . ")
                  AND (d.origin = 'CICS' OR d.current_location = 'CICS' OR d.target_department = 'CICS')
                  ORDER BY d.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $documents = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $documents[$row['tracking_id']] = $row;
        }
        return $documents;
    }

    public function findByTrackingId($id) {
        // 1. Get the document
        $query = "SELECT * FROM documents WHERE tracking_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $document = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($document) {
            // 2. Get its history logs (FIXED: Changed back to recorded_at)
            $historyQuery = "SELECT * FROM document_history WHERE tracking_id = :id ORDER BY recorded_at ASC";
            $historyStmt = $this->conn->prepare($historyQuery);
            $historyStmt->bindParam(':id', $id);
            $historyStmt->execute();
            
            $document['history'] = [];
            while ($log = $historyStmt->fetch(PDO::FETCH_ASSOC)) {
                $document['history'][] = [
                    'date' => date('M d, Y h:i A', strtotime($log['recorded_at'])),
                    'location' => $log['location'],
                    'action' => $log['action']
                ];
            }
        }

        return $document;
    }

    public function create($title, $category, $origin, $target_department, $file_path = null, $creator_id = null) {
        
        // 🔥 CICS ONLY: Force target department to CICS
        $target_department = 'CICS';
        
        // 🔥 CICS ONLY: Validate category
        if (!self::isValidCategory($category)) {
            throw new \Exception("Invalid category selected.");
        }

        // Build the prefix, e.g. CICS-3330-EC-2026- or CICS-EIN-2026-
        $cat = self::CATEGORIES[$category];
        $prefix = 'CICS-' . ($cat['series'] !== null ? $cat['series'] . '-' : '') . $cat['code'] . '-' . date('Y') . '-';
        // Matches only real codes (prefix + digits), not archived "-VOID-n" copies
        $codePattern = '^' . $prefix . '[0-9]+$';

        // Number reuse + insert must succeed or fail together
        $ownTransaction = !$this->conn->inTransaction();
        if ($ownTransaction) { $this->conn->beginTransaction(); }

        try {
            // 1. Reuse the lowest code freed up by a voided letter (same category & year)
            $stmt = $this->conn->prepare("SELECT tracking_id FROM documents WHERE tracking_id REGEXP :pattern AND status = 'Void'
                                          ORDER BY LENGTH(tracking_id), tracking_id LIMIT 1 FOR UPDATE");
            $stmt->bindValue(':pattern', $codePattern);
            $stmt->execute();
            $voided_id = $stmt->fetchColumn();

            if ($voided_id) {
                $this->archiveVoided($voided_id);
                $id = $voided_id;
            } else {
                // 2. Otherwise take the next number (sequence restarts every year per category; LENGTH first so 1000 sorts after 999)
                $stmt = $this->conn->prepare("SELECT tracking_id FROM documents WHERE tracking_id REGEXP :pattern
                                              ORDER BY LENGTH(tracking_id) DESC, tracking_id DESC LIMIT 1");
                $stmt->bindValue(':pattern', $codePattern);
                $stmt->execute();
                $last_id = $stmt->fetchColumn();

                $next_number = $last_id ? (int) substr($last_id, strlen($prefix)) + 1 : 1;
                $id = $prefix . str_pad($next_number, 3, '0', STR_PAD_LEFT);
            }

            $this->insertDocument($id, $title, $category, $origin, $target_department, $file_path, $creator_id);

            // Add history log with clear intent
            $this->addHistory($id, $origin, "Document created. Target destination: " . $target_department);

            if ($ownTransaction) { $this->conn->commit(); }
        } catch (\Exception $e) {
            if ($ownTransaction) {
                $this->conn->rollBack();
                // Put back any letter files moved by archiveVoided()
                foreach ($this->movedLetters as [$from, $to]) { @rename($to, $from); }
            }
            throw $e;
        } finally {
            $this->movedLetters = [];
        }

        return $id;
    }

    // Move a voided document to "<id>-VOID-<n>" (with its history & notifications) so its code can be reused
    private function archiveVoided($id) {
        $n = 1;
        $check = $this->conn->prepare("SELECT COUNT(*) FROM documents WHERE tracking_id = :id");
        do {
            $archive_id = $id . '-VOID-' . $n++;
            $check->execute([':id' => $archive_id]);
        } while ($check->fetchColumn() > 0);

        // Copy the row under the archive ID (document_history FK blocks renaming in place)
        $stmt = $this->conn->prepare("SELECT * FROM documents WHERE tracking_id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $row['tracking_id'] = $archive_id;

        $columns = array_keys($row);
        $this->conn->prepare("INSERT INTO documents (`" . implode('`, `', $columns) . "`) VALUES (:" . implode(', :', $columns) . ")")
                   ->execute(array_combine(array_map(fn($c) => ':' . $c, $columns), array_values($row)));

        $params = [':new' => $archive_id, ':old' => $id];
        $this->conn->prepare("UPDATE document_history SET tracking_id = :new WHERE tracking_id = :old")->execute($params);
        $this->conn->prepare("UPDATE notifications SET tracking_id = :new WHERE tracking_id = :old")->execute($params);
        $this->conn->prepare("DELETE FROM documents WHERE tracking_id = :old")->execute([':old' => $id]);

        $this->conn->prepare("INSERT INTO document_history (tracking_id, location, action) VALUES (:id, :location, :action)")
                   ->execute([':id' => $archive_id, ':location' => $row['current_location'], ':action' => "Tracking code $id released for reuse. This voided document is now $archive_id."]);

        // The voided letter files follow their document, so the reused code starts from a fresh template
        foreach (['docx', 'json'] as $ext) {
            $from = self::letterPath($id, $ext);
            $to = self::letterPath($archive_id, $ext);
            if (file_exists($from)) {
                if (!rename($from, $to)) {
                    throw new Exception("Could not archive the letter file for $id.");
                }
                $this->movedLetters[] = [$from, $to];
            }
        }
    }

    private function insertDocument($id, $title, $category, $origin, $target_department, $file_path, $creator_id) {
        // 🔥 UPDATED: Now inserting the category as well!
        $query = "INSERT INTO documents (tracking_id, creator_id, title, category, origin, target_department, current_location, status, file_path) 
                  VALUES (:id, :creator_id, :title, :category, :origin, :target_department, :current_location, 'Routing', :file_path)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':creator_id', $creator_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':origin', $origin);
        $stmt->bindParam(':target_department', $target_department); 
        
        // The document starts at the origin!
        $stmt->bindParam(':current_location', $origin); 
        
        $stmt->bindParam(':file_path', $file_path);
        
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new \Exception("Database failed to create document! SQL Error: " . $error[2]);
        }
    }

    // NEW: Fetch the specific email of the user who created the document
    public function getCreatorEmail($tracking_id) {
        $query = "SELECT users.email 
                  FROM documents 
                  JOIN users ON documents.creator_id = users.id 
                  WHERE documents.tracking_id = :id AND users.email IS NOT NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $tracking_id);
        $stmt->execute();
        
        return $stmt->fetchColumn(); // Returns the email address string, or false if not found
    }

    public function addHistory($id, $location, $action) {
        $updateQuery = "UPDATE documents SET current_location = :location WHERE tracking_id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':location', $location);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        $query = "INSERT INTO document_history (tracking_id, location, action) 
                  VALUES (:id, :location, :action)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':action', $action);
        
        // 🔥 STRICT DB CHECK 🔥
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database failed to save history log! SQL Error: " . $error[2]);
        }
        
        return true;
    }

    public function getDepartments() {
        // 🔥 CICS ONLY: Only CICS department exists
        return ['CICS'];
    }

    public function getPaginated($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // 🔥 CICS ONLY: Filter to CICS + known categories
        $stmt = $this->conn->query("SELECT COUNT(*) FROM documents 
                                    WHERE category IN (" . self::categorySqlList() . ")
                                    AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')");
        $total = $stmt->fetchColumn();
        
        $query = "SELECT * FROM documents 
                  WHERE category IN (" . self::categorySqlList() . ")
                  AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                  ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $docs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $docs[$row['tracking_id']] = $row;
        }
        
        return [
            'data' => $docs,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current' => $page
        ];
    }

    // Update just the file path for an existing document
    public function updateAttachment($tracking_id, $file_path) {
        $query = "UPDATE documents SET file_path = :file_path WHERE tracking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_path', $file_path);
        $stmt->bindParam(':id', $tracking_id);
        
        // 🔥 STRICT DB CHECK 🔥
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database failed to update attachment! SQL Error: " . $error[2]);
        }
        
        return true;
    }

    // Fetch all email addresses for a specific department
    public function getDepartmentEmails($department) {
        $query = "SELECT email FROM users WHERE department = :department AND email IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        
        // Fetch it as a simple array of email strings
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Update the main status of the document (e.g., 'Routing', 'Received', 'Returned')
    public function updateStatus($tracking_id, $status) {
        $query = "UPDATE documents SET status = :status WHERE tracking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $tracking_id);
        
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database failed to update status! SQL Error: " . $error[2]);
        }
        return true;
    }

    public function getRelevantToDepartment($department) {
        // 🔥 CICS ONLY: Filter to CICS + known categories
        $query = "SELECT d.*, u.name AS creator_name FROM documents d
                  LEFT JOIN users u ON u.id = d.creator_id
                  WHERE d.category IN (" . self::categorySqlList() . ")
                  AND (d.origin = 'CICS' OR d.current_location = 'CICS' OR d.target_department = 'CICS')
                  ORDER BY d.created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $documents = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $documents[$row['tracking_id']] = $row;
        }
        return $documents;
    }

    public function search($keyword) {
        // 🔥 CICS ONLY: Restrict search to CICS + known categories
        $query = "SELECT * FROM documents 
                  WHERE (tracking_id LIKE :keyword 
                  OR title LIKE :keyword 
                  OR origin LIKE :keyword 
                  OR target_department LIKE :keyword 
                  OR current_location LIKE :keyword
                  OR category LIKE :keyword)
                  AND category IN (" . self::categorySqlList() . ")
                  AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                  ORDER BY created_at DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        $docs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $docs[$row['tracking_id']] = $row;
        }
        return $docs;
    }

    public function getPaginatedForDepartment($department, $page = 1, $limit = 10, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        // 🔥 CICS ONLY: Always filter to CICS + known categories
        $whereClause = "WHERE (d.origin = 'CICS' OR d.current_location = 'CICS' OR d.target_department = 'CICS')
                        AND d.category IN (" . self::categorySqlList() . ")";
        $params = [];
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $whereClause .= " AND (d.tracking_id LIKE :search OR d.title LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $whereClause .= " AND d.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $whereClause .= " AND d.category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Apply origin/department filter
        if (!empty($filters['department'])) {
            $whereClause .= " AND (d.origin = :dept_filter OR d.target_department = :dept_filter OR d.current_location = :dept_filter)";
            $params[':dept_filter'] = $filters['department'];
        }
        
        // Count Query
        $countQuery = "SELECT COUNT(*) FROM documents d " . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        // Data Query (JOIN users to include the creator's name)
        $query = "SELECT d.*, u.name AS creator_name FROM documents d
                  LEFT JOIN users u ON u.id = d.creator_id " . $whereClause . "
                  ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $docs = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $docs[$row['tracking_id']] = $row;
        }
        
        return [
            'data' => $docs,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current' => $page
        ];
    }

    public function getPaginatedWithFilters($page = 1, $limit = 10, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        // 🔥 CICS ONLY: Always filter to CICS + known categories
        $whereClause = "WHERE (d.origin = 'CICS' OR d.current_location = 'CICS' OR d.target_department = 'CICS')
                        AND d.category IN (" . self::categorySqlList() . ")";
        $params = [];
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $whereClause .= " AND (d.tracking_id LIKE :search OR d.title LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $whereClause .= " AND d.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $whereClause .= " AND d.category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Apply origin/department filter
        if (!empty($filters['department'])) {
            $whereClause .= " AND (d.origin = :dept_filter OR d.target_department = :dept_filter OR d.current_location = :dept_filter)";
            $params[':dept_filter'] = $filters['department'];
        }
        
        // Count Query
        $countQuery = "SELECT COUNT(*) FROM documents d " . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        // Data Query (JOIN users to include the creator's name)
        $query = "SELECT d.*, u.name AS creator_name FROM documents d
                  LEFT JOIN users u ON u.id = d.creator_id " . $whereClause . "
                  ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $docs = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $docs[$row['tracking_id']] = $row;
        }
        
        return [
            'data' => $docs,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current' => $page
        ];
    }
}
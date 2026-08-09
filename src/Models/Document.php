<?php
namespace Models;

use Config\Database;
use PDO;
use Exception;

class Document {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        // 🔥 CICS ONLY: Filter to only CICS documents with IC/EC categories
        $query = "SELECT * FROM documents 
                  WHERE category IN ('IC', 'EC')
                  AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                  ORDER BY created_at DESC";
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
        
        // 🔥 CICS ONLY: Validate category is IC/EC only
        if (!in_array($category, ['IC', 'EC'])) {
            throw new \Exception("Invalid category. Only 'IC' (Internal Communication) and 'EC' (External Communication) are allowed.");
        }
        
        $year = date('y');  
        $month = date('m'); 
        $prefix = 'IC' . $year . $month; 
        
        $query = "SELECT tracking_id FROM documents WHERE tracking_id LIKE :prefix ORDER BY tracking_id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':prefix', $prefix . '%');
        $stmt->execute();
        $last_id = $stmt->fetchColumn();
        
        if ($last_id) {
            $last_number = (int) substr($last_id, 6);
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        $id = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
        
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

        // Add history log with clear intent
        $this->addHistory($id, $origin, "Document created. Target destination: " . $target_department);

        return $id;
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
        
        // 🔥 CICS ONLY: Filter to CICS + IC/EC
        $stmt = $this->conn->query("SELECT COUNT(*) FROM documents 
                                    WHERE category IN ('IC', 'EC')
                                    AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')");
        $total = $stmt->fetchColumn();
        
        $query = "SELECT * FROM documents 
                  WHERE category IN ('IC', 'EC')
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
        // 🔥 CICS ONLY: Filter to CICS + IC/EC
        $query = "SELECT * FROM documents 
                  WHERE category IN ('IC', 'EC')
                  AND (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                  ORDER BY created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $documents = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $documents[$row['tracking_id']] = $row;
        }
        return $documents;
    }

    public function search($keyword) {
        // 🔥 CICS ONLY: Restrict search to CICS + IC/EC
        $query = "SELECT * FROM documents 
                  WHERE (tracking_id LIKE :keyword 
                  OR title LIKE :keyword 
                  OR origin LIKE :keyword 
                  OR target_department LIKE :keyword 
                  OR current_location LIKE :keyword
                  OR category LIKE :keyword)
                  AND category IN ('IC', 'EC')
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
        
        // 🔥 CICS ONLY: Always filter to CICS + IC/EC
        $whereClause = "WHERE (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                        AND category IN ('IC', 'EC')";
        $params = [];
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $whereClause .= " AND (tracking_id LIKE :search OR title LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $whereClause .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $whereClause .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Apply origin/department filter
        if (!empty($filters['department'])) {
            $whereClause .= " AND (origin = :dept_filter OR target_department = :dept_filter OR current_location = :dept_filter)";
            $params[':dept_filter'] = $filters['department'];
        }
        
        // Count Query
        $countQuery = "SELECT COUNT(*) FROM documents " . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        // Data Query
        $query = "SELECT * FROM documents " . $whereClause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
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
        
        // 🔥 CICS ONLY: Always filter to CICS + IC/EC
        $whereClause = "WHERE (origin = 'CICS' OR current_location = 'CICS' OR target_department = 'CICS')
                        AND category IN ('IC', 'EC')";
        $params = [];
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $whereClause .= " AND (tracking_id LIKE :search OR title LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $whereClause .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Apply category filter
        if (!empty($filters['category'])) {
            $whereClause .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }
        
        // Apply origin/department filter
        if (!empty($filters['department'])) {
            $whereClause .= " AND (origin = :dept_filter OR target_department = :dept_filter OR current_location = :dept_filter)";
            $params[':dept_filter'] = $filters['department'];
        }
        
        // Count Query
        $countQuery = "SELECT COUNT(*) FROM documents " . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        // Data Query
        $query = "SELECT * FROM documents " . $whereClause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
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
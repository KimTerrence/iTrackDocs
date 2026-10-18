<?php
namespace Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DocumentController extends Controller {
    
    // 🔥 FIXED: Added the missing security helper! 🔥
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?error=access_denied");
            exit;
        }
    }
    
    // Helper to fire a notification
    private function notify($userId, $type, $title, $message, $trackingId = null) {
        $model = new \Models\Notification();
        $model->create($userId, $type, $title, $message, $trackingId);
    }

    // Helper to notify an entire department
    private function notifyDepartment($department, $type, $title, $message, $trackingId = null, $excludeUserId = null) {
        $model = new \Models\Notification();
        $model->createForDepartment($department, $type, $title, $message, $trackingId, $excludeUserId);
    }

    // 1. Show the Dashboard
    public function dashboard() {
        $this->requireAuth();
        
        // Grab the user's department and role from their login session
        $user_dept = $_SESSION['user_department'] ?? '';
        $user_role = strtolower($_SESSION['user_role'] ?? '');
        
        $model = new \Models\Document();
        
        // Admin sees ALL documents; regular users see only their department's documents
        if ($user_role === 'admin') {
            $documents = $model->getAll();
        } else {
            $documents = $model->getRelevantToDepartment($user_dept);
        }
        
        $this->render('dashboard', ['documents' => $documents]);
    }

    // 2. Show the Create Form
    public function create() {
        $this->requireAuth();
        $this->render('create');
    }

    // 3. Process the Create Form
   // 3. Process the Create Form
    public function store() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            
            // 🔥 CICS ONLY: Force origin to CICS
            $origin = 'CICS';
            
            // 🔥 CICS ONLY: Target department is always CICS
            $target_department = 'CICS';
            
            // 🔥 CICS ONLY: Validate category server-side
            if (!\Models\Document::isValidCategory($category)) {
                die("<div style='padding:30px; background:#fef08a; color:#854d0e; border: 2px solid #facc15; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>⚠️ Invalid Category!</h2>
                        <p>Please select one of the listed document categories.</p>
                     </div>");
            }
            
            $file_path = null; 

            try {
                // 1. Check if the user ACTUALLY selected a file
                if (isset($_FILES['scanned_file']) && $_FILES['scanned_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    
                    if ($_FILES['scanned_file']['error'] !== UPLOAD_ERR_OK) {
                        throw new \Exception("PHP Upload Error Code: " . $_FILES['scanned_file']['error'] . " (Code 1 means file is too big for your server settings).");
                    }
                    
                    $upload_dir = __DIR__ . '/../../public/uploads/';
                    
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0777, true)) {
                            throw new \Exception("Could not create the uploads directory at: " . $upload_dir . ". Check your folder permissions.");
                        }
                    }

                    $file_extension = $this->allowedAttachmentExtension($_FILES['scanned_file']);
                    $safe_filename = uniqid('doc_') . '.' . $file_extension;
                    $target_path = $upload_dir . $safe_filename;

                    if (move_uploaded_file($_FILES['scanned_file']['tmp_name'], $target_path)) {
                        $file_path = 'uploads/' . $safe_filename; 
                    } else {
                        throw new \Exception("The file was received, but PHP does not have permission to save it to: " . $target_path);
                    }
                }

                // 2. Try to save to the database 
                $model = new \Models\Document();
                
                // 🔥 UPDATED: Passing category and target_department exactly where the Model expects it!
                $tracking_id = $model->create($title, $category, $origin, $target_department, $file_path, $_SESSION['user_id']);
                $this->logActivity('document_create', "Created document '$title' ($tracking_id) targeting '$target_department'.");
                
                // 🔔 Notify the target department that a new document is on its way
                $this->notifyDepartment($target_department, 'info', 'New Document Incoming', 
                    "A new document '$title' ($tracking_id) has been routed to your department from $origin.",
                    $tracking_id, $_SESSION['user_id']);
                
                // If successful: stamp the code on the upload first, then open the letter editor (coded categories) or the view page
                $next = \Models\Document::usesTemplate($category) ? 'edit_letter' : 'view';
                if ($file_path) {
                    header("Location: index.php?action=stamp_attachment&tracking_id=" . urlencode($tracking_id) . "&next=" . $next);
                } else {
                    header("Location: index.php?action=" . $next . "&tracking_id=" . urlencode($tracking_id));
                }
                exit;

            } catch (\PDOException $e) {
                die("<div style='padding:30px; background:#fee2e2; color:#991b1b; border: 2px solid #f87171; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>🛑 Database Error!</h2>
                        <code>" . $e->getMessage() . "</code>
                     </div>");
            } catch (\Exception $e) {
                die("<div style='padding:30px; background:#fef08a; color:#854d0e; border: 2px solid #facc15; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>⚠️ Upload Error!</h2>
                        <p>" . $e->getMessage() . "</p>
                     </div>");
            }
        }
    }

    // 4. Staff View (Routing Slip / Print)
    public function view($tracking_id) {
        $this->requireAuth();
        
        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);
        
        if (!$document) { header("Location: index.php?action=dashboard"); exit; }
        
        // 🔥 Fetch the user who created / is using this tracking code
        $creator = null;
        if (!empty($document['creator_id'])) {
            $userModel = new \Models\User();
            $creator = $userModel->findById($document['creator_id']);
        }
        
        $this->render('view', ['tracking_id' => $tracking_id, 'document' => $document, 'creator' => $creator]);
    }

    // A voided document is read-only: changing actions go back to its page (buttons there are disabled too)
    private function isVoid($document) {
        return $document && strtolower($document['status'] ?? '') === 'void';
    }

    private function blockIfVoid($tracking_id) {
        if ($this->isVoid((new \Models\Document())->findByTrackingId($tracking_id))) {
            header("Location: index.php?action=view&tracking_id=" . urlencode($tracking_id) . "&error=void");
            exit;
        }
    }

    // Attachments: document files only, checked by content too (the name alone could be e.g. "x.php" or a renamed .exe)
    private const ATTACHMENT_MAX_BYTES = 5 * 1024 * 1024;
    private const OLE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"; // legacy .doc/.xls/.ppt
    private const ATTACHMENT_SIGNATURES = [
        'pdf' => '%PDF', 'jpg' => "\xFF\xD8\xFF", 'jpeg' => "\xFF\xD8\xFF", 'png' => "\x89PNG",
        'doc' => self::OLE_SIGNATURE, 'xls' => self::OLE_SIGNATURE, 'ppt' => self::OLE_SIGNATURE,
        'docx' => "PK\x03\x04", 'xlsx' => "PK\x03\x04", 'pptx' => "PK\x03\x04",
    ];
    // Part every real Office Open XML file of that kind contains
    private const OOXML_MAIN_PART = ['docx' => 'word/document.xml', 'xlsx' => 'xl/workbook.xml', 'pptx' => 'ppt/presentation.xml'];
    // Only these get the tracking code stamped on them (see stamp_attachment)
    private const STAMPABLE_TYPES = ['pdf', 'jpg', 'jpeg', 'png'];

    private function attachmentMatchesType($path, $ext) {
        $signature = self::ATTACHMENT_SIGNATURES[$ext] ?? null;
        if ($signature === null || file_get_contents($path, false, null, 0, strlen($signature)) !== $signature) {
            return false;
        }
        if (isset(self::OOXML_MAIN_PART[$ext])) {
            // Zip entry names are stored as plain text, so this works without the zip extension.
            // Macro-enabled files (vbaProject.bin) are refused even when renamed to .docx/.xlsx/.pptx.
            $content = file_get_contents($path);
            return strpos($content, '[Content_Types].xml') !== false
                && strpos($content, self::OOXML_MAIN_PART[$ext]) !== false
                && stripos($content, 'vbaProject.bin') === false;
        }
        return true;
    }

    private function allowedAttachmentExtension($file) {
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (($file['size'] ?? filesize($file['tmp_name'])) > self::ATTACHMENT_MAX_BYTES) {
            throw new \Exception("The file is larger than 5MB. Please upload a smaller file.");
        }
        if (!isset(self::ATTACHMENT_SIGNATURES[$ext]) || !$this->attachmentMatchesType($file['tmp_name'], $ext)) {
            throw new \Exception("Only document files can be uploaded: PDF, Word, Excel, PowerPoint, JPG or PNG.");
        }
        return $ext;
    }

    // Adds the tracking code to a freshly uploaded attachment (done in the browser, saved via saveStamped)
    public function stampAttachment($tracking_id) {
        $this->requireAuth();

        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);
        $next = ($_GET['next'] ?? '') === 'edit_letter' ? 'edit_letter' : 'view';
        $nextUrl = 'index.php?action=' . $next . '&tracking_id=' . urlencode($tracking_id);

        // Nothing to stamp, void, already stamped, or an Office file (not stamped): carry on
        if (!$document || $this->isVoid($document) || empty($document['file_path']) || str_starts_with(basename($document['file_path']), 'stamped_')
            || !in_array(strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION)), self::STAMPABLE_TYPES, true)) {
            header("Location: " . $nextUrl);
            exit;
        }

        $this->render('stamp_attachment', [
            'tracking_id' => $tracking_id,
            'document' => $document,
            'nextUrl' => $nextUrl,
            'fileType' => strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION)),
        ]);
    }

    // Replace the uploaded attachment with its stamped copy (JSON response)
    public function saveStamped() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $id = $_POST['tracking_id'] ?? '';
        $model = new \Models\Document();
        $document = $model->findByTrackingId($id);
        $file = $_FILES['stamped'] ?? null;

        if (!$document || empty($document['file_path']) || str_starts_with(basename($document['file_path']), 'stamped_')) {
            http_response_code(409);
            echo json_encode(['error' => 'This attachment has already been stamped.']);
            exit;
        }
        if ($this->isVoid($document)) {
            http_response_code(409);
            echo json_encode(['error' => 'This document is void and can no longer be changed.']);
            exit;
        }
        $ext = strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::STAMPABLE_TYPES, true) || !$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 50 * 1024 * 1024 || !$this->attachmentMatchesType($file['tmp_name'], $ext)) {
            http_response_code(400);
            echo json_encode(['error' => 'The stamped file is not a valid ' . strtoupper($ext) . '.']);
            exit;
        }

        $safe_filename = uniqid('stamped_') . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../public/uploads/' . $safe_filename)) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not save the stamped file.']);
            exit;
        }

        $model->updateAttachment($id, 'uploads/' . $safe_filename);
        $original = __DIR__ . '/../../public/' . $document['file_path'];
        if (file_exists($original)) { unlink($original); }
        $this->logActivity('document_stamp', "Added tracking code to the attachment of document $id.");

        echo json_encode(['ok' => true]);
        exit;
    }

    // Only the creator (or an admin) may edit a letter, and never once it is void
    private function canEditLetter($document) {
        $isOwner = isset($_SESSION['user_id']) && $document['creator_id'] == $_SESSION['user_id'];
        $isAdmin = strtolower($_SESSION['user_role'] ?? '') === 'admin';
        return ($isOwner || $isAdmin) && strtolower($document['status'] ?? '') !== 'void';
    }

    // Letter Editor (edit the CICS template in the browser)
    public function editLetter($tracking_id) {
        $this->requireAuth();

        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);

        if (!$document || !\Models\Document::usesTemplate($document['category'] ?? '')) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        $contentPath = \Models\Document::letterPath($tracking_id, 'json');
        $letterContent = file_exists($contentPath) ? json_decode(file_get_contents($contentPath), true) : null;

        $this->render('letter_editor', [
            'tracking_id' => $tracking_id,
            'document' => $document,
            'canEdit' => $this->canEditLetter($document),
            'hasLetter' => file_exists(\Models\Document::letterPath($tracking_id)),
            'letterContent' => $letterContent,
        ]);
    }

    // Read-only letter preview (standalone page, embedded on the document page)
    public function letterPreview($tracking_id) {
        $this->requireAuth();

        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);

        if (!$document || !\Models\Document::usesTemplate($document['category'] ?? '')) {
            http_response_code(404);
            echo "Letter not found.";
            exit;
        }

        $contentPath = \Models\Document::letterPath($tracking_id, 'json');
        $letterContent = file_exists($contentPath) ? json_decode(file_get_contents($contentPath), true) : null;
        $hasLetter = $letterContent !== null;

        // Loaded outside the dashboard layout, like the QR scan page
        require __DIR__ . '/../../views/letter_preview.php';
    }

    // Serve the saved letter (.docx) to logged-in users
    public function letterFile($tracking_id) {
        $this->requireAuth();

        $path = \Models\Document::letterPath($tracking_id);
        if (!file_exists($path)) {
            http_response_code(404);
            echo "Letter not found.";
            exit;
        }

        $disposition = isset($_GET['download']) ? 'attachment' : 'inline';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: ' . $disposition . '; filename="' . $tracking_id . '.docx"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: no-store');
        readfile($path);
        exit;
    }

    // Save the letter posted by the editor (JSON response)
    public function saveLetter() {
        $this->requireAuth();
        header('Content-Type: application/json');

        $id = $_POST['tracking_id'] ?? '';
        $model = new \Models\Document();
        $document = $model->findByTrackingId($id);

        if (!$document || !\Models\Document::usesTemplate($document['category'] ?? '')) {
            http_response_code(404);
            echo json_encode(['error' => 'Document not found.']);
            exit;
        }
        if (!$this->canEditLetter($document)) {
            http_response_code(403);
            echo json_encode(['error' => 'You are not allowed to edit this letter.']);
            exit;
        }

        $file = $_FILES['letter'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed (code ' . ($file['error'] ?? 'none') . ').']);
            exit;
        }
        // A .docx is a ZIP archive: check the signature instead of trusting the file name
        if ($file['size'] > 20 * 1024 * 1024 || file_get_contents($file['tmp_name'], false, null, 0, 4) !== "PK\x03\x04") {
            http_response_code(400);
            echo json_encode(['error' => 'The file is not a valid .docx letter (max 20MB).']);
            exit;
        }

        // Editor content (Quill delta) so the letter can be reopened for editing
        $content = json_decode($_POST['content'] ?? '', true);
        if (!is_array($content) || !isset($content['ops']) || strlen($_POST['content']) > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid letter content.']);
            exit;
        }

        $path = \Models\Document::letterPath($id);
        $isFirstSave = !file_exists($path);
        if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0777, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not create the letters folder.']);
            exit;
        }
        if (file_put_contents(\Models\Document::letterPath($id, 'json'), json_encode($content)) === false
            || !move_uploaded_file($file['tmp_name'], $path)) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not save the letter. Check folder permissions.']);
            exit;
        }

        $userName = $_SESSION['user_name'] ?? 'Unknown User';
        if ($isFirstSave) {
            $model->addHistory($id, $document['current_location'], "Letter drafted from the CICS template by " . $userName);
        }
        $this->logActivity('letter_save', "Saved letter for document $id.");

        echo json_encode(['ok' => true, 'saved_at' => date('h:i A')]);
        exit;
    }

    // 5. QR Scan Page - read-only document details (PUBLIC - NO LOGIN REQUIRED)
    public function scan($tracking_id) {
        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);

        // Setup the variables for the view
        $data = [
            'tracking_id' => $tracking_id,
            'document' => $document,
            'error' => !$document ? "Document ID not found." : null
        ];

        // Extract variables so $tracking_id, $document, and $error are available in the view
        extract($data);
        
        // INSTEAD of $this->render(), require the file directly.
        // This forces it to load OUTSIDE the admin dashboard layout.
        require __DIR__ . '/../../views/scan.php';
    }

    // Process Manual Return from Dashboard (REQUIRES LOGIN)
    public function markReturned() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        if ($id) { $this->blockIfVoid($id); }

        // Automatically grab the user's department from their session!
        $location = $_SESSION['user_department'] ?? 'Department'; 
        
        if ($id) {
            $model = new \Models\Document();

            // 🔥 Capture WHO clicked the "Mark as Returned" button
            $returnedBy = $_SESSION['user_name'] ?? 'Unknown User';

            // 1. Update history and status (include who performed the action)
            $model->addHistory($id, $location, "Document Returned to " . $location . "\nby " . $returnedBy);
            $model->updateStatus($id, 'Returned');
            $this->logActivity('document_return', "Document $id returned to '$location' by $returnedBy.");

            // 🔔 Notify the origin department that the document has been returned
            $document = $model->findByTrackingId($id);
            if ($document) {
                $this->notify($document['creator_id'], 'warning', 'Document Returned', 
                    "Your document '{$document['title']}' ($id) has been returned to $location.",
                    $id);
            }
        }
        
        // Redirect back to the document view page
        header("Location: index.php?action=view&tracking_id=" . urlencode($id));
        exit;
    }

    // Process Mark as Void
    public function markVoid() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        if ($id) { $this->blockIfVoid($id); } // already void: don't void (and log) it twice

        if ($id) {
            $model = new \Models\Document();

            // 1. Update history and status
            $model->addHistory($id, $_SESSION['user_department'] ?? 'Unknown', "Document marked as VOID");
            $model->updateStatus($id, 'Void');
            $this->logActivity('document_void', "Document $id marked as void.");
            
            // 🔔 Notify the document creator
            $document = $model->findByTrackingId($id);
            if ($document && $document['creator_id']) {
                $this->notify($document['creator_id'], 'warning', 'Document Voided', 
                    "Your document '{$document['title']}' ($id) has been marked as void.",
                    $id);
            }
        }
        
        // Redirect back to the document view page
        header("Location: index.php?action=view&tracking_id=" . urlencode($id));
        exit;
    }

    // 8. Document List (User's department only)
    public function list() {
        $this->requireAuth();
        
        $user_dept = $_SESSION['user_department'] ?? '';
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        
        // Grab filter parameters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'category' => $_GET['category'] ?? '',
            'department' => $_GET['department'] ?? ''
        ];
        
        // Clean empty filters
        $filters = array_filter($filters, function($v) { return $v !== ''; });
        
        $model = new \Models\Document();
        
        // Get departments for the filter dropdown
        $deptStmt = $model->getDepartments();
        
        $result = $model->getPaginatedForDepartment($user_dept, $page, 10, $filters);
        
        $this->render('documents', [
            'documents' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current' => $result['current']
            ],
            'action' => 'list',
            'filters' => $filters,
            'departments' => $deptStmt
        ]);
    }

    // 9. All Documents (Admin only - all departments)
    public function allDocuments() {
        $this->requireAuth();
        
        $role = strtolower($_SESSION['user_role'] ?? '');
        
        // Only admin can access
        if ($role !== 'admin') {
            header("Location: index.php?action=list&error=access_denied");
            exit;
        }
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        
        // Grab filter parameters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'category' => $_GET['category'] ?? '',
            'department' => $_GET['department'] ?? ''
        ];
        
        // Clean empty filters
        $filters = array_filter($filters, function($v) { return $v !== ''; });
        
        $model = new \Models\Document();
        
        // Get departments for the filter dropdown
        $deptStmt = $model->getDepartments();
        
        $result = $model->getPaginatedWithFilters($page, 10, $filters);
        
        $this->render('documents', [
            'documents' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current' => $result['current']
            ],
            'action' => 'allDocuments',
            'filters' => $filters,
            'departments' => $deptStmt
        ]);
    }

    public function apiTrack() {
        // Explicitly tell the browser we are sending JSON data
        header('Content-Type: application/json');

        $tracking_id = $_GET['tracking_id'] ?? null;
        
        if (!$tracking_id) {
            echo json_encode(['error' => 'No ID provided']);
            exit;
        }

        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);

        if (!$document) {
            echo json_encode(['error' => 'Document not found']);
            exit;
        }

        // Return the document data directly as JSON
        echo json_encode($document);
        exit;
    }
    
    // View Activity Logs
    public function logs() {
        $this->requireAuth();
        
        $role = strtolower(trim($_SESSION['user_role'] ?? ''));
        $user_dept = $_SESSION['user_department'] ?? '';
        $user_id = $_SESSION['user_id'] ?? null;
        
        $documentModel = new \Models\Document();
        $activityModel = new \Models\ActivityLog();
        
        $documentLogs = [];
        $userLogs = [];
        
        if ($role === 'admin') {
            // Admin sees all logs
            $documentLogs = $documentModel->getPaginated(1, 100)['data'] ?? [];
            $docHistory = [];
            foreach ($documentLogs as $doc) {
                $history = $documentModel->findByTrackingId($doc['tracking_id']);
                $historyList = $history['history'] ?? [];
                foreach ($historyList as $h) {
                    $docHistory[] = array_merge([
                        'tracking_id' => $doc['tracking_id'],
                        'title' => $doc['title']
                    ], $h);
                }
            }
            $userLogs = $activityModel->getRecent(100);
        } elseif ($role === 'dept_head') {
            // Dept Head sees all activity logs within their department
            $documentLogs = $documentModel->getPaginatedForDepartment($user_dept, 1, 100)['data'] ?? [];
            $docHistory = [];
            foreach ($documentLogs as $doc) {
                $history = $documentModel->findByTrackingId($doc['tracking_id']);
                $historyList = $history['history'] ?? [];
                foreach ($historyList as $h) {
                    $docHistory[] = array_merge([
                        'tracking_id' => $doc['tracking_id'],
                        'title' => $doc['title']
                    ], $h);
                }
            }
            $userLogs = $activityModel->getRecentByDepartment($user_dept, 100);
        } else {
            // Staff cannot access logs
            header("Location: index.php?action=dashboard&error=access_denied");
            exit;
        }
        
        $this->render('logs', [
            'logs' => $docHistory,
            'userLogs' => $userLogs,
            'role' => $role,
            'user_dept' => $user_dept
        ]);
    }

    // API: Search documents for the global search bar
    public function apiSearch() {
        header('Content-Type: application/json');
        
        $keyword = $_GET['q'] ?? '';
        if (strlen(trim($keyword)) < 1) {
            echo json_encode([]);
            exit;
        }
        
        $model = new \Models\Document();
        $results = $model->search($keyword);
        
        // Format results for the frontend
        $formatted = [];
        foreach ($results as $id => $doc) {
            $formatted[] = [
                'tracking_id' => $id,
                'title' => $doc['title'],
                'status' => $doc['status'] ?? 'Pending',
                'category' => \Models\Document::categoryLabel($doc['category'] ?? ''),
                'current_location' => $doc['current_location'] ?? '',
                'url' => 'index.php?action=view&tracking_id=' . urlencode($id)
            ];
        }
        
        echo json_encode($formatted);
        exit;
    }

    // Process File Upload for Existing Documents
    public function uploadAttachment() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tracking_id = trim($_POST['tracking_id'] ?? '');

            if (empty($tracking_id)) {
                die("Error: Missing Tracking ID.");
            }
            $this->blockIfVoid($tracking_id);

            try {
                // 1. Validate the file
                if (!isset($_FILES['scanned_file']) || $_FILES['scanned_file']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new \Exception("Please select a file before submitting.");
                }

                if ($_FILES['scanned_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception("PHP Upload Error Code: " . $_FILES['scanned_file']['error']);
                }

                // Check the type before touching the current attachment
                $file_extension = $this->allowedAttachmentExtension($_FILES['scanned_file']);

                $upload_dir = __DIR__ . '/../../public/uploads/';

                // 2. Prepare directory
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true)) {
                        throw new \Exception("Could not create the uploads directory.");
                    }
                }

                // Check if an old file exists and DELETE IT first!
                $model = new \Models\Document();
                $existingDoc = $model->findByTrackingId($tracking_id);
                
                if ($existingDoc && !empty($existingDoc['file_path'])) {
                    // Build the exact path to the old file
                    $old_file_path = __DIR__ . '/../../public/' . $existingDoc['file_path'];
                    // If it exists on the hard drive, delete it
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path); 
                    }
                }

                // 3. Move the new file
                $safe_filename = uniqid('attach_') . '.' . $file_extension;
                $target_path = $upload_dir . $safe_filename;

                if (!move_uploaded_file($_FILES['scanned_file']['tmp_name'], $target_path)) {
                    throw new \Exception("PHP does not have permission to save the file to: " . $target_path);
                }

                // 4. Update the Database
                $file_path = 'uploads/' . $safe_filename;
                $model->updateAttachment($tracking_id, $file_path);
                $this->logActivity('document_upload', "Uploaded new attachment for document $tracking_id.");

                // 5. Success! Stamp the tracking code on it, then go back to the document
                header("Location: index.php?action=stamp_attachment&tracking_id=" . urlencode($tracking_id) . "&next=view");
                exit;

            } catch (\PDOException $e) {
                die("<div style='padding:30px; background:#fee2e2; color:#991b1b; border: 2px solid #f87171; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>🛑 Database Error!</h2>
                        <code>" . $e->getMessage() . "</code>
                     </div>");
            } catch (\Exception $e) {
                die("<div style='padding:30px; background:#fef08a; color:#854d0e; border: 2px solid #facc15; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>⚠️ Upload Error!</h2>
                        <p>" . $e->getMessage() . "</p>
                     </div>");
            }
        }
    }
}
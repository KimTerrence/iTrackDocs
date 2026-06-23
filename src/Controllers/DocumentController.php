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
    
    // 1. Show the Dashboard
    public function dashboard() {
        $this->requireAuth();
        
        // Grab the user's department from their login session
        $user_dept = $_SESSION['user_department'] ?? '';
        
        $model = new \Models\Document();
        
        // Only fetch documents that belong in their department
        $documents = $model->getRelevantToDepartment($user_dept);
        
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
            
            // 🔥 SECURE: Ignore the HTML form entirely and grab the Origin straight from the session!
            $origin = $_SESSION['user_department'] ?? 'Unknown'; 
            
            // 🔥 NEW: Capture the target department from the dropdown menu
            $target_department = trim($_POST['target_department'] ?? '');
            
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

                    $file_extension = pathinfo($_FILES['scanned_file']['name'], PATHINFO_EXTENSION);
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
                
                // If successful, go to the view page!
                header("Location: index.php?action=view&tracking_id=" . $tracking_id);
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
        $this->render('view', ['tracking_id' => $tracking_id, 'document' => $document]);
    }

  // 5. Mobile Scan Page (PUBLIC - NO LOGIN REQUIRED)
    public function scan($tracking_id) {
        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);
        
        // Setup the variables for the view
        $data = [
            'tracking_id' => $tracking_id,
            'document' => $document,
            'success' => isset($_GET['success']) ? true : false,
            'error' => !$document ? "Document ID not found." : null
        ];
        
        // Extract variables so $tracking_id, $success, and $error are available in the view
        extract($data);
        
        // INSTEAD of $this->render(), require the file directly.
        // This forces it to load OUTSIDE the admin dashboard layout.
        require __DIR__ . '/../../views/scan.php';
    }

    // 7. Process Mobile Scan (PUBLIC - NO LOGIN REQUIRED)
    public function markReceived() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        $location = $_POST['location'] ?? 'Receiving Desk'; 
        
        if ($id) {
            $model = new \Models\Document();
            
            // 1. Update the database
            $model->addHistory($id, $location, "Received at " . $location);
            $model->updateStatus($id, 'Received');
            $this->logActivity('document_receive', "Document $id received at '$location'.");
            // 2. Fetch the document details
            $document = $model->findByTrackingId($id);
            
            // 3. Fetch the SPECIFIC Creator's Email (🔥 UPDATED 🔥)
            $creator_email = $model->getCreatorEmail($id);
            
            // 4. Send Email Notification
            if ($document && $creator_email) {
                $subject = "Document Arrived: " . $document['title'];
                
                $message = "
                <html>
                <body style='font-family: sans-serif; color: #334155; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;'>
                        <h2 style='color: #2563eb; margin-top: 0;'>Document Scanned & Received!</h2>
                        <p>Good news,</p>
                        <p>The document you created, <strong>" . htmlspecialchars($document['title']) . "</strong>, has successfully arrived at its next destination.</p>
                        
                        <div style='background: #fff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 20px 0;'>
                            <p style='margin: 0 0 10px 0;'><strong>Tracking ID:</strong> " . $id . "</p>
                            <p style='margin: 0 0 10px 0;'><strong>Current Location:</strong> " . htmlspecialchars($location) . "</p>
                            <p style='margin: 0;'><strong>Time:</strong> " . date('M d, Y h:i A') . "</p>
                        </div>
                        
                        <p style='font-size: 12px; color: #94a3b8;'>This is an automated notification from your iTrackDocs system.</p>
                    </div>
                </body>
                </html>
                ";

                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    
                    // 👉 REPLACE THESE TWO LINES WITH YOUR DETAILS 👈
                    $mail->Username   = 'quineskimterrence@gmail.com'; 
                    $mail->Password   = 'ajofwgqqnbomfanh'; 
                    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('quineskmterrence@gmail.com', 'iTrackDocs Notification');
                    $mail->addAddress($creator_email); // Sends to the document creator

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                } catch (Exception $e) {
                    // Force the system to stop and show us the exact error!
                    die("<div style='padding:30px; background:#fee2e2; color:#991b1b; border: 2px solid #f87171; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                            <h2 style='margin-top:0;'>🛑 PHPMailer Error!</h2>
                            <p>Google or your server rejected the email. Here is the exact reason:</p>
                            <hr style='border-color:#fca5a5; margin: 15px 0;'>
                            <code>" . $mail->ErrorInfo . "</code>
                         </div>");
                }
            }
        }
        
        // Redirect back to the scan page with a success message
        header("Location: index.php?action=scan&tracking_id=" . urlencode($id) . "&success=1");
        exit;
    }

    // Process Manual Return from Dashboard (REQUIRES LOGIN)
    public function markReturned() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        
        // Automatically grab the user's department from their session!
        $location = $_SESSION['user_department'] ?? 'Department'; 
        
        if ($id) {
            $model = new \Models\Document();
            
            // 1. Update history and status
            $model->addHistory($id, $location, "Document Returned to " . $location);
            $model->updateStatus($id, 'Returned');
            $this->logActivity('document_return', "Document $id returned to '$location'.");

            // 2. Optional: You can copy-paste your PHPMailer email code here 
            // if you want the creator to be emailed when it gets returned!
        }
        
        // Redirect back to the document view page
        header("Location: index.php?action=view&tracking_id=" . urlencode($id));
        exit;
    }

    // Process Manual Received from Dashboard (REQUIRES LOGIN)
    public function markReceivedFromDashboard() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        $location = $_SESSION['user_department'] ?? 'Department'; 
        
        if ($id) {
            $model = new \Models\Document();
            
            // 1. Update history and status
            $model->addHistory($id, $location, "Document Received at " . $location);
            $model->updateStatus($id, 'Received');
            $this->logActivity('document_receive', "Document $id received at '$location'.");

            // 2. Email the document creator
            $creator_email = $model->getCreatorEmail($id);
            $document = $model->findByTrackingId($id);
            
            if ($document && $creator_email) {
                $subject = "Document Received: " . $document['title'];
                
                $message = "
                <html>
                <body style='font-family: sans-serif; color: #334155; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;'>
                        <h2 style='color: #2563eb; margin-top: 0;'>Document Received!</h2>
                        <p>Good news,</p>
                        <p>The document you created, <strong>" . htmlspecialchars($document['title']) . "</strong>, has been <strong>received</strong> at its next destination.</p>
                        
                        <div style='background: #fff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 20px 0;'>
                            <p style='margin: 0 0 10px 0;'><strong>Tracking ID:</strong> " . $id . "</p>
                            <p style='margin: 0 0 10px 0;'><strong>Current Location:</strong> " . htmlspecialchars($location) . "</p>
                            <p style='margin: 0;'><strong>Time:</strong> " . date('M d, Y h:i A') . "</p>
                        </div>
                        
                        <p style='font-size: 12px; color: #94a3b8;'>This is an automated notification from your iTrackDocs system.</p>
                    </div>
                </body>
                </html>
                ";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'quineskimterrence@gmail.com'; 
                    $mail->Password   = 'ajofwgqqnbomfanh'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->setFrom('quineskmterrence@gmail.com', 'iTrackDocs Notification');
                    $mail->addAddress($creator_email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                } catch (\Exception $e) {
                    // Silently fail email but continue
                    error_log("Email failed: " . $e->getMessage());
                }
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
        
        $model = new \Models\Document();
        $result = $model->getPaginatedForDepartment($user_dept, $page, 10);
        
        $this->render('documents', [
            'documents' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current' => $result['current']
            ],
            'action' => 'list'
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
        
        $model = new \Models\Document();
        $result = $model->getPaginated($page, 10);
        
        $this->render('documents', [
            'documents' => $result['data'],
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current' => $result['current']
            ],
            'action' => 'allDocuments'
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
                'category' => $doc['category'] ?? 'Uncategorized',
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

            try {
                // 1. Validate the file
                if (!isset($_FILES['scanned_file']) || $_FILES['scanned_file']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new \Exception("Please select a file before submitting.");
                }

                if ($_FILES['scanned_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception("PHP Upload Error Code: " . $_FILES['scanned_file']['error']);
                }

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
                $file_extension = pathinfo($_FILES['scanned_file']['name'], PATHINFO_EXTENSION);
                $safe_filename = uniqid('attach_') . '.' . $file_extension;
                $target_path = $upload_dir . $safe_filename;

                if (!move_uploaded_file($_FILES['scanned_file']['tmp_name'], $target_path)) {
                    throw new \Exception("PHP does not have permission to save the file to: " . $target_path);
                }

                // 4. Update the Database
                $file_path = 'uploads/' . $safe_filename;
                $model->updateAttachment($tracking_id, $file_path);
                $this->logActivity('document_upload', "Uploaded new attachment for document $tracking_id.");

                // 5. Success! Go back to the document
                header("Location: index.php?action=view&tracking_id=" . urlencode($tracking_id));
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
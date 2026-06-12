<?php
namespace Controllers;

class DocumentController extends Controller {
    
    // 🔥 FIXED: Added the missing security helper! 🔥
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=authenticate");
            exit;
        }
    }
    
    // 1. Show the Dashboard
    public function dashboard() {
        $this->requireAuth();
        
        $model = new \Models\Document();
        $this->render('dashboard', ['documents' => $model->getAll()]);
    }

    // 2. Show the Create Form
    public function create() {
        $this->requireAuth();
        $this->render('create');
    }

    // 3. Process the Create Form
    public function store() {
        $this->requireAuth(); // This will now work perfectly!

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $origin = trim($_POST['origin'] ?? '');
            $file_path = null; 

            try {
                // 1. Check if the user ACTUALLY selected a file
                if (isset($_FILES['scanned_file']) && $_FILES['scanned_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    
                    // If there was an error during upload (like file too big)
                    if ($_FILES['scanned_file']['error'] !== UPLOAD_ERR_OK) {
                        throw new \Exception("PHP Upload Error Code: " . $_FILES['scanned_file']['error'] . " (Code 1 means file is too big for your server settings).");
                    }
                    
                    $upload_dir = __DIR__ . '/../../public/uploads/';
                    
                    // Try to create the folder if it doesn't exist
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0777, true)) {
                            throw new \Exception("Could not create the uploads directory at: " . $upload_dir . ". Check your folder permissions.");
                        }
                    }

                    $file_extension = pathinfo($_FILES['scanned_file']['name'], PATHINFO_EXTENSION);
                    $safe_filename = uniqid('doc_') . '.' . $file_extension;
                    $target_path = $upload_dir . $safe_filename;

                    // Try to save the file
                    if (move_uploaded_file($_FILES['scanned_file']['tmp_name'], $target_path)) {
                        $file_path = 'uploads/' . $safe_filename; 
                    } else {
                        throw new \Exception("The file was received, but PHP does not have permission to save it to: " . $target_path);
                    }
                }

                // 2. Try to save to the database
                $model = new \Models\Document();
                $tracking_id = $model->create($title, $origin, $file_path);
                
                // If successful, go to the view page!
                header("Location: index.php?action=view&tracking_id=" . $tracking_id);
                exit;

            } catch (\PDOException $e) {
                // Catch Database errors specifically!
                die("<div style='padding:30px; background:#fee2e2; color:#991b1b; border: 2px solid #f87171; border-radius:12px; font-family:sans-serif; max-width: 600px; margin: 50px auto;'>
                        <h2 style='margin-top:0;'>🛑 Database Error!</h2>
                        <p>Did you forget to run the SQL command to add the <b>file_path</b> column?</p>
                        <hr style='border-color:#fca5a5; margin: 15px 0;'>
                        <code>" . $e->getMessage() . "</code>
                     </div>");
            } catch (\Exception $e) {
                // Catch all other File/Folder errors
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

    // 5. Public Tracking Page
    public function track($tracking_id) {
        $model = new \Models\Document();
        $document = $model->findByTrackingId($tracking_id);
        
        $this->render('track', [
            'tracking_id' => $tracking_id, 
            'document' => $document,
            'error' => !$document ? "Document ID not found." : null
        ]);
    }

  // 6. Mobile Scan Page (PUBLIC - NO LOGIN REQUIRED)
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
        // Note: Adjust the path below if your views folder is located elsewhere
        require __DIR__ . '/../../views/scan.php';
    }

    // 7. Process Mobile Scan (PUBLIC - NO LOGIN REQUIRED)
    public function markReceived() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
        
        $id = $_POST['tracking_id'] ?? null;
        // Since it's a public scan, they might not type a location. Default it if empty.
        $location = $_POST['location'] ?? 'Receiving Desk'; 
        
        if ($id) {
            $model = new \Models\Document();
            $model->addHistory($id, $location, "Received at " . $location);
        }
        
        // Redirect back to the scan page, but append &success=1 to show the green checkmark
        header("Location: index.php?action=scan&tracking_id=" . urlencode($id) . "&success=1");
        exit;
    }

    // 8. Document List
    public function list() {
        $this->requireAuth();
        
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
            'action' => 'list'
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

                // 🔥 NEW: Check if an old file exists and DELETE IT first! 🔥
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
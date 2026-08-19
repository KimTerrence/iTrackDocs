<?php
namespace Controllers;

class UserController extends Controller {

    // --------------------------------------------------------
    // SECURITY GATEKEEPERS
    // --------------------------------------------------------

    // Security Helper: Make sure the user is logged in
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            // If they aren't logged in, redirect to home where the login modal will show
            header("Location: index.php?error=access_denied");
            exit;
        }
    }
    
    // 🔥 FIXED: Security Helper now allows Admins AND Heads
    private function requireAdminOrHead() {
        // First, check if they are logged in at all
        $this->requireAuth();

        // Standardize the role to lowercase to prevent case-sensitive typos
        $role = strtolower($_SESSION['user_role'] ?? '');

        // Next, check if they are an admin or a head. If neither, kick them out!
        if ($role !== 'admin' && $role !== 'head') {
            header("Location: index.php?action=dashboard&error=unauthorized");
            exit;
        }
    }

    // --------------------------------------------------------
    // METHODS
    // --------------------------------------------------------

    // Load the User Management View
    public function list() {
        $this->requireAdminOrHead(); // Use the new security check
        
        $db = (new \Config\Database())->getConnection();
        
        $role = strtolower($_SESSION['user_role'] ?? '');
        
        // Grab filter parameters
        $search = trim($_GET['search'] ?? '');
        $roleFilter = trim($_GET['role'] ?? '');

        // Build the query dynamically
        // 🔥 CICS ONLY: All users belong to CICS
        $whereClause = "WHERE department = 'CICS'";
        $params = [];
        
        // Apply search filter
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Apply role filter
        if (!empty($roleFilter)) {
            $whereClause .= " AND LOWER(role) = :role";
            $params[':role'] = strtolower($roleFilter);
        }
        
        $query = "SELECT * FROM users " . $whereClause . " ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Normalize role for dropdown comparison
        $currentRole = $roleFilter;
        
        $this->render('users', [
            'users' => $users,
            'action' => 'users',
            'search' => $search,
            'currentRole' => $currentRole
        ]);
    }

    // Handle the Form Submission (The 'C' in MVC)
   // Handle the Form Submission (The 'C' in MVC)
    public function store() {
        $this->requireAdminOrHead();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? 'password123'; 
            $role = $_POST['role'] ?? 'staff';
                   
            // 🔥 CICS ONLY: All users belong to CICS
            $department = 'CICS';

            // Only save if we have the critical data
            if (!empty($name) && !empty($email) && !empty($department)) {
                $userModel = new \Models\User();
                
                // Check for duplicate email
                $dbCheck = (new \Config\Database())->getConnection();
                $existing = $dbCheck->prepare("SELECT id FROM users WHERE email = :email");
                $existing->execute(['email' => $email]);
                if ($existing->rowCount() > 0) {
                    $this->render('users', ['error' => 'Email already exists.', 'users' => (new \Models\User())->getUsers()]);
                    return;
                }
                
                // Make sure your User Model's create() method accepts 5 parameters!
                $userModel->create($name, $email, $password, $role, $department);
                $this->logActivity('user_create', "Created user '$email' with role '$role' in department '$department'.");
            }

            // Redirect back to the user list
            header("Location: index.php?action=users");
            exit;
        }
    }

    // Edit an Existing User
    public function updateUser() {
        $this->requireAdminOrHead(); // Protect the update route too!

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'staff';
            $password = $_POST['password'] ?? ''; // Might be empty!

            if ($user_id && !empty($name) && !empty($email)) {
                $userModel = new \Models\User();
                
                // Check for duplicate email (excluding current user)
                $db = (new \Config\Database())->getConnection();
                $existing = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
                $existing->execute(['email' => $email, 'id' => $user_id]);
                if ($existing->rowCount() > 0) {
                    $this->render('users', ['error' => 'Email already exists.', 'users' => (new \Models\User())->getUsers()]);
                    return;
                }
                
                // 🔥 CICS ONLY: Update user with CICS department
                $userModel->updateAsAdmin($user_id, $name, $email, $role, $password, 'CICS');
                $this->logActivity('user_update', "Updated user ID #$user_id ('$email').");
            }

            header("Location: index.php?action=users&success=updated");
            exit;
        }
    }

   // 1. Show the Settings Page
    public function settings() {
        // Gatekeeper: Must be logged in!
        $this->requireAuth();

        $user_id = $_SESSION['user_id'];
        
        // Fetch the user's current data from the database
        $model = new \Models\User();
        $user = $model->findById($user_id);

        // THE FIX: Use $this->render() instead of require()
        // This wraps your settings.php snippet inside the main layout.php skeleton!
        $this->render('settings', ['user' => $user]);
    }

    // Block or Unblock a user (replaces Delete)
    public function blockUser() {
        $this->requireAdminOrHead();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $action = $_POST['action'] ?? ''; // 'block' or 'unblock'

            if ($user_id && in_array($action, ['block', 'unblock'])) {
                $status = ($action === 'block') ? 'blocked' : 'active';
                $userModel = new \Models\User();
                $userModel->updateStatus($user_id, $status);
                $this->logActivity('user_' . $status, "Set user ID #$user_id status to '$status'.");
            }

            header("Location: index.php?action=users");
            exit;
        }
    }

    // Process Password Change from Settings
    public function updatePassword() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validate
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $this->render('settings', ['error' => 'All password fields are required.', 'user' => (new \Models\User())->findById($user_id)]);
                return;
            }

            if ($new_password !== $confirm_password) {
                $this->render('settings', ['error' => 'New passwords do not match.', 'user' => (new \Models\User())->findById($user_id)]);
                return;
            }

            if (strlen($new_password) < 8) {
                $this->render('settings', ['error' => 'Password must be at least 8 characters.', 'user' => (new \Models\User())->findById($user_id)]);
                return;
            }

            // Verify current password
            $model = new \Models\User();
            $user = $model->findById($user_id);
            
            if (!password_verify($current_password, $user['password'])) {
                $this->render('settings', ['error' => 'Current password is incorrect.', 'user' => (new \Models\User())->findById($user_id)]);
                return;
            }

            // Update password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $db = (new \Config\Database())->getConnection();
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed, 'id' => $user_id]);

            header("Location: index.php?action=settings&success=1");
            exit;
        }
    }

    // 2. Process the Form Submission
    public function updateSettings() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Basic Validation
            if (empty($name) || empty($email)) {
                $model = new \Models\User();
                $user = $model->findById($user_id);
                $error = "All fields are required.";
                
                // THE FIX: Use render() here too so errors show up inside the layout!
                $this->render('settings', [
                    'user' => $user, 
                    'error' => $error
                ]);
                return;
            }

            // 🔥 CICS ONLY: Force department to CICS
            $department = 'CICS';
            
            // Update the Database
            $model = new \Models\User();
            $model->updateProfile($user_id, $name, $email, $department);

            // Update the session variables so the top right of the dashboard updates immediately
            $_SESSION['user_name'] = $name;

            // Redirect back to settings with a success flag in the URL
            header("Location: index.php?action=settings&success=1");
            exit;
        }
    }
}
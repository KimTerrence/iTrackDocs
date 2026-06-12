<?php
namespace Controllers;

class UserController extends Controller {

    // --------------------------------------------------------
    // SECURITY GATEKEEPERS
    // --------------------------------------------------------

    // Security Helper: Make sure the user is logged in
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            // If they aren't logged in, kick them to the login page
            header("Location: index.php?action=authenticate");
            exit;
        }
    }
    
    // Security Helper: Only let Admins through
    private function requireAdmin() {
        // First, check if they are logged in at all
        $this->requireAuth();

        // Next, check if they are actually an admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?action=dashboard");
            exit;
        }
    }

    // --------------------------------------------------------
    // METHODS
    // --------------------------------------------------------

    // Load the User Management View
    public function list() {
        $this->requireAdmin(); 
        
        $db = (new \Config\Database())->getConnection();
        $users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
        
        $this->render('users', ['users' => $users, 'action' => 'users']);
    }

    // Handle the Form Submission (The 'C' in MVC)
    public function store() {
        $this->requireAdmin(); // Hard block non-admins

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            
            // Set a default password if you didn't add a password input to the modal yet
            $password = $_POST['password'] ?? 'password123'; 
            $role = $_POST['role'] ?? 'staff';

            if (!empty($name) && !empty($username)) {
                $userModel = new \Models\User();
                $userModel->create($name, $username, $password, $role);
            }

            // Redirect back to the user list to refresh the table
            header("Location: index.php?action=users");
            exit;
        }
    }

    // Edit an Existing User
    public function updateUser() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $role = $_POST['role'] ?? 'staff';
            $password = $_POST['password'] ?? ''; // Might be empty!

            if ($user_id && !empty($name) && !empty($username)) {
                $userModel = new \Models\User();
                $userModel->updateAsAdmin($user_id, $name, $username, $role, $password);
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

    // 2. Process the Form Submission
    public function updateSettings() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $department = trim($_POST['department'] ?? '');

            // Basic Validation
            if (empty($name) || empty($email) || empty($department)) {
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
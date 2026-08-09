<?php
namespace Controllers;

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new \Models\User();
            $user = $userModel->authenticate($email, $password);

            if ($user === 'blocked') {
                // User is blocked! Prevent login
                $this->logActivity('login_blocked', "Attempted login for blocked account '$email'.");
                $this->render('home', ['error' => 'Your account has been blocked. Contact your administrator.']);
            } elseif ($user) {
                // Success! Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                // 🔥 CICS ONLY: Force department to CICS
                $_SESSION['user_department'] = 'CICS';
                $this->logActivity('login', "User '$email' logged in.");
                header("Location: index.php?action=dashboard");
                exit;
            } else {
                // Failed! Pass error to the layout so the modal stays open
                $this->logActivity('login_failed', "Failed login attempt for email '$email'.");
                $this->render('home', ['error' => 'Invalid email or password.']);
            }
        }
    }

    public function logout() {
        $username = $_SESSION['user_name'] ?? 'unknown';
        session_destroy();
        $this->logActivity('logout', "User '$username' logged out.");
        header("Location: index.php");
        exit;
    }
}
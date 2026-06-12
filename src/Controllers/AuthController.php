<?php
namespace Controllers;

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new \Models\User();
            $user = $userModel->authenticate($username, $password);

            if ($user) {
                // Success! Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                header("Location: index.php?action=dashboard");
                exit;
            } else {
                // Failed! Pass error to the layout so the modal stays open
                $this->render('home', ['error' => 'Invalid username or password.']);
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
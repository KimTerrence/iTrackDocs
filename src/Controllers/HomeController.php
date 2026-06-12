<?php
// src/Controllers/HomeController.php
namespace Controllers;

class HomeController extends Controller {
    
    // This is the index method that your index.php is looking for!
    public function index() {
        
        // If the user is already logged in, skip the landing page and go to the dashboard
        if (isset($_SESSION['user_name'])) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        // If they are a guest, render the home view
        $this->render('home');
    }
}
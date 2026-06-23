<?php
// src/Controllers/Controller.php
namespace Controllers;

class Controller {
    
  // src/Controllers/Controller.php
protected function render($view, $data = []) {
    extract($data);
    
    // Automatically make $action available to all views
    $action = $_GET['action'] ?? 'home'; 

    ob_start();
    require __DIR__ . "/../../views/{$view}.php";
    $content = ob_get_clean();
    require __DIR__ . "/../../views/layout.php";
}

protected function logActivity($action, $description) {
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    $userId = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['user_name'] ?? 'system';
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $model = new \Models\ActivityLog();
    $model->log($userId, $username, $action, $description, $ip);
}
}

<?php
// public/index.php
session_start(); // Always start the session first!
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
// 1. Clean Autoloader (No more debug text)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// 2. Capture URL Parameters
$action = $_GET['action'] ?? 'home';
$tracking_id = $_GET['tracking_id'] ?? null;

// 3. The Traffic Cop (Routing)
if ($action === 'home') {
    (new \Controllers\HomeController())->index();
} elseif ($action === 'authenticate') {
    (new \Controllers\AuthController())->login();
} elseif ($action === 'logout') {
    (new \Controllers\AuthController())->logout();
} elseif ($action === 'dashboard') {
    (new \Controllers\DocumentController())->dashboard();
} elseif ($action === 'list') { 
    (new \Controllers\DocumentController())->list();
} elseif ($action === 'allDocuments') { 
    (new \Controllers\DocumentController())->allDocuments();
} elseif ($action === 'create') {
    (new \Controllers\DocumentController())->create();
} elseif ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\DocumentController())->store();
} elseif ($action === 'upload_attachment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\DocumentController())->uploadAttachment();
} elseif ($action === 'view' && $tracking_id) {
    (new \Controllers\DocumentController())->view($tracking_id);
} elseif ($action === 'users') {
    (new \Controllers\UserController())->list();
} elseif ($action === 'add_user') {
    (new \Controllers\UserController())->store();
} elseif ($action === 'edit_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\UserController())->updateUser();
} elseif ($action === 'block_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\UserController())->blockUser();
} elseif ($action === "mark_returned"){
    (new \Controllers\DocumentController())->markReturned();
} elseif ($action === 'settings') {
    (new \Controllers\UserController())->settings();
} elseif ($action === 'update_settings') {
    (new \Controllers\UserController())->updateSettings();
} elseif ($action === 'update_password') {
    (new \Controllers\UserController())->updatePassword();
} elseif ($action === 'api_track') {
    // FIXED: Added api_track as an elseif statement!
    (new \Controllers\DocumentController())->apiTrack();
} elseif ($action === 'scan') {
    if ($tracking_id) {
        (new \Controllers\DocumentController())->scan($tracking_id);
    } else {
        die("System Error: No Tracking ID was provided in the URL.");
    }
} elseif ($action === 'mark_received' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\DocumentController())->markReceived();
} elseif ($action === 'mark_received_dashboard' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\DocumentController())->markReceivedFromDashboard();
} elseif ($action === 'mark_void' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\DocumentController())->markVoid();
} elseif ($action === 'logs') {
    (new \Controllers\DocumentController())->logs();
} elseif ($action === 'api_search') {
    (new \Controllers\DocumentController())->apiSearch();
} elseif ($action === 'api_notifications') {
    (new \Controllers\NotificationController())->getNotifications();
} elseif ($action === 'api_notification_count') {
    (new \Controllers\NotificationController())->getUnreadCount();
} elseif ($action === 'api_notification_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\NotificationController())->markAsRead();
} elseif ($action === 'api_notification_read_all' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new \Controllers\NotificationController())->markAllAsRead();
} else {
    // Fallback for unknown routes
    (new \Controllers\HomeController())->index();
}
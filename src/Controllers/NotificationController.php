<?php
namespace Controllers;

use Models\Notification;

class NotificationController extends Controller {
    
    // API: Get notifications for the current user
    public function getNotifications() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $model = new Notification();
        $notifications = $model->getRecent($userId, 20);
        
        // Format timestamps
        foreach ($notifications as &$n) {
            $n['created_at_formatted'] = date('M d, Y h:i A', strtotime($n['created_at']));
            $n['time_ago'] = $this->timeAgo($n['created_at']);
        }
        
        echo json_encode($notifications);
        exit;
    }
    
    // API: Get unread count
    public function getUnreadCount() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }
        
        $model = new Notification();
        $count = $model->getUnreadCount($_SESSION['user_id']);
        
        echo json_encode(['count' => $count]);
        exit;
    }
    
    // API: Mark a single notification as read
    public function markAsRead() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }
        
        $notificationId = $_POST['id'] ?? null;
        if (!$notificationId) {
            echo json_encode(['error' => 'No ID provided']);
            exit;
        }
        
        $model = new Notification();
        $model->markAsRead((int)$notificationId, $_SESSION['user_id']);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    // API: Mark all notifications as read
    public function markAllAsRead() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Not authenticated']);
            exit;
        }
        
        $model = new Notification();
        $model->markAllAsRead($_SESSION['user_id']);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    // Helper: Human-readable time ago
    private function timeAgo($datetime) {
        $now = time();
        $timestamp = strtotime($datetime);
        $diff = $now - $timestamp;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M d', $timestamp);
    }
}
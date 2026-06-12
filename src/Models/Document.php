<?php
namespace Models;

use Config\Database;
use PDO;
use Exception;

class Document {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM documents ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $documents = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $documents[$row['tracking_id']] = $row;
        }
        return $documents;
    }

    public function findByTrackingId($id) {
        // 1. Get the document
        $query = "SELECT * FROM documents WHERE tracking_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $document = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($document) {
            // 2. Get its history logs
            $historyQuery = "SELECT * FROM document_history WHERE tracking_id = :id ORDER BY recorded_at ASC";
            $historyStmt = $this->conn->prepare($historyQuery);
            $historyStmt->bindParam(':id', $id);
            $historyStmt->execute();
            
            $document['history'] = [];
            while ($log = $historyStmt->fetch(PDO::FETCH_ASSOC)) {
                $document['history'][] = [
                    'date' => date('M d, Y h:i A', strtotime($log['recorded_at'])),
                    'action' => $log['action']
                ];
            }
        }

        return $document;
    }

    public function create($title, $origin, $file_path = null) {
        $id = 'DOC-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
        
        // 1. Insert into documents
        $query = "INSERT INTO documents (tracking_id, title, origin, current_location, status, file_path) 
                  VALUES (:id, :title, :origin, :origin, 'Routing', :file_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':origin', $origin);
        $stmt->bindParam(':file_path', $file_path);
        
        // 🔥 STRICT DB CHECK 🔥
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database failed to create document! SQL Error: " . $error[2]);
        }

        // 2. Add first history log
        $this->addHistory($id, $origin, "Document created at " . $origin);

        return $id;
    }

    public function addHistory($id, $location, $action) {
        $updateQuery = "UPDATE documents SET current_location = :location WHERE tracking_id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':location', $location);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        $query = "INSERT INTO document_history (tracking_id, location, action) 
                  VALUES (:id, :location, :action)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':action', $action);
        
        // 🔥 STRICT DB CHECK 🔥
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new Exception("Database failed to save history log! SQL Error: " . $error[2]);
        }
        
        return true;
    }

    public function getPaginated($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM documents");
        $total = $stmt->fetchColumn();
        
        $query = "SELECT * FROM documents ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $docs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $docs[$row['tracking_id']] = $row;
        }
        
        return [
            'data' => $docs,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current' => $page
        ];
    }
    // Update just the file path for an existing document
    public function updateAttachment($tracking_id, $file_path) {
        $query = "UPDATE documents SET file_path = :file_path WHERE tracking_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_path', $file_path);
        $stmt->bindParam(':id', $tracking_id);
        
        // 🔥 STRICT DB CHECK 🔥
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new \Exception("Database failed to update attachment! SQL Error: " . $error[2]);
        }
        
        return true;
    }
}
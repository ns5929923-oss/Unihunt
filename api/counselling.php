<?php
/**
 * Counselling Sessions API Controller
 * Handles counselling session booking and management
 */

require_once '../config/config.php';
require_once '../config/database.php';

class CounsellingAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Book a counselling session
     */
    public function bookSession($data) {
        try {
            $conn = $this->db->getConnection();
            
            // Validate required fields
            $required = ['name', 'email', 'session_type'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'error' => "Field '$field' is required"
                    ];
                }
            }
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Invalid email format'
                ];
            }
            
            // Prepare data
            $sessionData = [
                ':name' => trim($data['name']),
                ':email' => trim($data['email']),
                ':phone' => $data['phone'] ?? null,
                ':preferred_date' => $data['preferred_date'] ?? null,
                ':preferred_time' => $data['preferred_time'] ?? null,
                ':session_type' => $data['session_type'],
                ':stream' => $data['stream'] ?? null,
                ':interests' => isset($data['interests']) ? json_encode($data['interests']) : null,
                ':budget_range' => $data['budget_range'] ?? null,
                ':location_preference' => $data['location_preference'] ?? null
            ];
            
            $sql = "INSERT INTO counselling_sessions 
                    (name, email, phone, preferred_date, preferred_time, session_type, 
                     stream, interests, budget_range, location_preference) 
                    VALUES (:name, :email, :phone, :preferred_date, :preferred_time, :session_type, 
                            :stream, :interests, :budget_range, :location_preference)";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($sessionData);
            
            if ($result) {
                $sessionId = $conn->lastInsertId();
                
                // Send confirmation email (placeholder)
                $this->sendConfirmationEmail($data['email'], $data['name'], $sessionId);
                
                return [
                    'success' => true,
                    'message' => 'Counselling session booked successfully!',
                    'session_id' => $sessionId,
                    'data' => [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'session_type' => $data['session_type'],
                        'status' => 'pending'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to book session'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get counselling sessions (admin only)
     */
    public function getSessions($filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM counselling_sessions WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['status'])) {
                $sql .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['session_type'])) {
                $sql .= " AND session_type = :session_type";
                $params[':session_type'] = $filters['session_type'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND preferred_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND preferred_date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            // Pagination
            if (!empty($filters['page']) && !empty($filters['limit'])) {
                $offset = ($filters['page'] - 1) * $filters['limit'];
                $sql .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $filters['limit'];
                $params[':offset'] = $offset;
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $sessions = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($sessions as &$session) {
                $session['interests'] = json_decode($session['interests'], true);
            }
            
            return [
                'success' => true,
                'data' => $sessions,
                'count' => count($sessions)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update session status
     */
    public function updateSessionStatus($sessionId, $status, $notes = null) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "UPDATE counselling_sessions SET status = :status";
            $params = [':status' => $status, ':id' => $sessionId];
            
            if ($notes) {
                $sql .= ", counsellor_notes = :notes";
                $params[':notes'] = $notes;
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Session status updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to update session status'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get session statistics
     */
    public function getSessionStats() {
        try {
            $conn = $this->db->getConnection();
            
            // Total sessions
            $sql = "SELECT COUNT(*) as total FROM counselling_sessions";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $total = $stmt->fetch()['total'];
            
            // Sessions by status
            $sql = "SELECT status, COUNT(*) as count FROM counselling_sessions GROUP BY status";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $byStatus = $stmt->fetchAll();
            
            // Sessions by type
            $sql = "SELECT session_type, COUNT(*) as count FROM counselling_sessions GROUP BY session_type";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $byType = $stmt->fetchAll();
            
            // Recent sessions (last 30 days)
            $sql = "SELECT COUNT(*) as recent FROM counselling_sessions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $recent = $stmt->fetch()['recent'];
            
            return [
                'success' => true,
                'data' => [
                    'total' => $total,
                    'recent' => $recent,
                    'by_status' => $byStatus,
                    'by_type' => $byType
                ]
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send confirmation email (placeholder)
     */
    private function sendConfirmationEmail($email, $name, $sessionId) {
        // This would integrate with an email service like PHPMailer
        // For now, just log the action
        error_log("Confirmation email would be sent to: $email for session: $sessionId");
        
        // In a real implementation, you would:
        // 1. Use PHPMailer or similar
        // 2. Create email templates
        // 3. Send actual emails
        // 4. Handle email delivery status
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new CounsellingAPI();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $response = $api->bookSession($input);
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new CounsellingAPI();
    $response = [];
    
    // Get query parameters
    $filters = [
        'status' => $_GET['status'] ?? '',
        'session_type' => $_GET['session_type'] ?? '',
        'date_from' => $_GET['date_from'] ?? '',
        'date_to' => $_GET['date_to'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'limit' => $_GET['limit'] ?? 20
    ];
    
    // Remove empty filters
    $filters = array_filter($filters, function($value) {
        return $value !== '';
    });
    
    // Determine endpoint
    if (isset($_GET['stats'])) {
        $response = $api->getSessionStats();
    } elseif (isset($_GET['update_status'])) {
        $sessionId = $_GET['session_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $notes = $_GET['notes'] ?? null;
        
        if ($sessionId && $status) {
            $response = $api->updateSessionStatus($sessionId, $status, $notes);
        } else {
            $response = [
                'success' => false,
                'error' => 'Session ID and status are required'
            ];
        }
    } else {
        $response = $api->getSessions($filters);
    }
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

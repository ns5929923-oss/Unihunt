<?php
/**
 * Enquiries API Controller
 * Handles enquiries, applications, and general contact forms
 */

require_once '../config/config.php';
require_once '../config/database.php';

class EnquiriesAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Submit an enquiry
     */
    public function submitEnquiry($data) {
        try {
            $conn = $this->db->getConnection();
            
            // Validate required fields
            $required = ['type', 'name', 'email'];
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
            
            // Validate enquiry type
            $validTypes = ['Enquire', 'Apply', 'Booking', 'General'];
            if (!in_array($data['type'], $validTypes)) {
                return [
                    'success' => false,
                    'error' => 'Invalid enquiry type'
                ];
            }
            
            // Prepare data
            $enquiryData = [
                ':type' => $data['type'],
                ':university_id' => $data['university_id'] ?? null,
                ':name' => trim($data['name']),
                ':email' => trim($data['email']),
                ':phone' => $data['phone'] ?? null,
                ':subject' => $data['subject'] ?? null,
                ':message' => $data['message'] ?? null,
                ':details' => isset($data['details']) ? json_encode($data['details']) : null,
                ':priority' => $data['priority'] ?? 'medium'
            ];
            
            $sql = "INSERT INTO enquiries 
                    (type, university_id, name, email, phone, subject, message, details, priority) 
                    VALUES (:type, :university_id, :name, :email, :phone, :subject, :message, :details, :priority)";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($enquiryData);
            
            if ($result) {
                $enquiryId = $conn->lastInsertId();
                
                // Send notification email (placeholder)
                $this->sendNotificationEmail($data);
                
                return [
                    'success' => true,
                    'message' => 'Enquiry submitted successfully!',
                    'enquiry_id' => $enquiryId,
                    'data' => [
                        'type' => $data['type'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'status' => 'new'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to submit enquiry'
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
     * Get enquiries (admin only)
     */
    public function getEnquiries($filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT e.*, u.name as university_name 
                    FROM enquiries e 
                    LEFT JOIN universities u ON e.university_id = u.id 
                    WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['type'])) {
                $sql .= " AND e.type = :type";
                $params[':type'] = $filters['type'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND e.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['priority'])) {
                $sql .= " AND e.priority = :priority";
                $params[':priority'] = $filters['priority'];
            }
            
            if (!empty($filters['university_id'])) {
                $sql .= " AND e.university_id = :university_id";
                $params[':university_id'] = $filters['university_id'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND e.created_at >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND e.created_at <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $sql .= " ORDER BY e.created_at DESC";
            
            // Pagination
            if (!empty($filters['page']) && !empty($filters['limit'])) {
                $offset = ($filters['page'] - 1) * $filters['limit'];
                $sql .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $filters['limit'];
                $params[':offset'] = $offset;
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $enquiries = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($enquiries as &$enquiry) {
                $enquiry['details'] = json_decode($enquiry['details'], true);
            }
            
            return [
                'success' => true,
                'data' => $enquiries,
                'count' => count($enquiries)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update enquiry status
     */
    public function updateEnquiryStatus($enquiryId, $status, $response = null, $assignedTo = null) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "UPDATE enquiries SET status = :status";
            $params = [':status' => $status, ':id' => $enquiryId];
            
            if ($response) {
                $sql .= ", response = :response";
                $params[':response'] = $response;
            }
            
            if ($assignedTo) {
                $sql .= ", assigned_to = :assigned_to";
                $params[':assigned_to'] = $assignedTo;
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Enquiry status updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to update enquiry status'
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
     * Get enquiry statistics
     */
    public function getEnquiryStats() {
        try {
            $conn = $this->db->getConnection();
            
            // Total enquiries
            $sql = "SELECT COUNT(*) as total FROM enquiries";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $total = $stmt->fetch()['total'];
            
            // Enquiries by type
            $sql = "SELECT type, COUNT(*) as count FROM enquiries GROUP BY type";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $byType = $stmt->fetchAll();
            
            // Enquiries by status
            $sql = "SELECT status, COUNT(*) as count FROM enquiries GROUP BY status";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $byStatus = $stmt->fetchAll();
            
            // Recent enquiries (last 30 days)
            $sql = "SELECT COUNT(*) as recent FROM enquiries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $recent = $stmt->fetch()['recent'];
            
            // Pending enquiries
            $sql = "SELECT COUNT(*) as pending FROM enquiries WHERE status = 'new'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $pending = $stmt->fetch()['pending'];
            
            return [
                'success' => true,
                'data' => [
                    'total' => $total,
                    'recent' => $recent,
                    'pending' => $pending,
                    'by_type' => $byType,
                    'by_status' => $byStatus
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
     * Send notification email (placeholder)
     */
    private function sendNotificationEmail($data) {
        // This would integrate with an email service
        error_log("Notification email would be sent for enquiry: " . $data['type']);
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new EnquiriesAPI();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $response = $api->submitEnquiry($input);
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new EnquiriesAPI();
    $response = [];
    
    // Get query parameters
    $filters = [
        'type' => $_GET['type'] ?? '',
        'status' => $_GET['status'] ?? '',
        'priority' => $_GET['priority'] ?? '',
        'university_id' => $_GET['university_id'] ?? '',
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
        $response = $api->getEnquiryStats();
    } elseif (isset($_GET['update_status'])) {
        $enquiryId = $_GET['enquiry_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $responseText = $_GET['response'] ?? null;
        $assignedTo = $_GET['assigned_to'] ?? null;
        
        if ($enquiryId && $status) {
            $response = $api->updateEnquiryStatus($enquiryId, $status, $responseText, $assignedTo);
        } else {
            $response = [
                'success' => false,
                'error' => 'Enquiry ID and status are required'
            ];
        }
    } else {
        $response = $api->getEnquiries($filters);
    }
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

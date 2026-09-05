<?php
/**
 * Newsletter Subscription Handler
 * Handles newsletter subscription and unsubscription
 */

require_once 'config/config.php';
require_once 'config/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? 'subscribe';
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $interests = $_POST['interests'] ?? [];
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Valid email address is required');
    }
    
    if ($action === 'subscribe') {
        // Handle interests array
        if (is_string($interests)) {
            $interests = explode(',', $interests);
        }
        
        // Check if email already exists
        $sql = "SELECT id, status FROM newsletter_subscribers WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            if ($existing['status'] === 'active') {
                echo json_encode([
                    'success' => true,
                    'message' => 'You are already subscribed to our newsletter!',
                    'already_subscribed' => true
                ]);
                exit;
            } else {
                // Reactivate subscription
                $sql = "UPDATE newsletter_subscribers 
                        SET status = 'active', name = :name, interests = :interests, subscribed_at = NOW(), unsubscribed_at = NULL 
                        WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([
                    ':email' => $email,
                    ':name' => $name,
                    ':interests' => json_encode($interests)
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Welcome back! Your subscription has been reactivated.',
                        'reactivated' => true
                    ]);
                } else {
                    throw new Exception('Failed to reactivate subscription');
                }
            }
        } else {
            // New subscription
            $sql = "INSERT INTO newsletter_subscribers (email, name, interests) 
                    VALUES (:email, :name, :interests)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':email' => $email,
                ':name' => $name,
                ':interests' => json_encode($interests)
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you for subscribing to our newsletter!',
                    'subscribed' => true
                ]);
            } else {
                throw new Exception('Failed to subscribe');
            }
        }
        
    } elseif ($action === 'unsubscribe') {
        // Unsubscribe
        $sql = "UPDATE newsletter_subscribers 
                SET status = 'unsubscribed', unsubscribed_at = NOW() 
                WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([':email' => $email]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'You have been unsubscribed from our newsletter.',
                'unsubscribed' => true
            ]);
        } else {
            throw new Exception('Failed to unsubscribe');
        }
        
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

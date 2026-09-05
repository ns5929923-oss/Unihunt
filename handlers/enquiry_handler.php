<?php
/**
 * Enquiry Form Handler
 * Processes enquiry, application, and general contact form submissions
 */

require_once 'config/config.php';
require_once 'api/enquiries.php';

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

// Get form data
$data = [
    'type' => $_POST['type'] ?? 'General',
    'university_id' => $_POST['university_id'] ?? '',
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'subject' => $_POST['subject'] ?? '',
    'message' => $_POST['message'] ?? '',
    'details' => $_POST['details'] ?? [],
    'priority' => $_POST['priority'] ?? 'medium'
];

// Handle details array/object
if (is_string($data['details'])) {
    $data['details'] = json_decode($data['details'], true);
}

// Clean and validate data
$data['name'] = trim($data['name']);
$data['email'] = trim($data['email']);
$data['phone'] = trim($data['phone']);
$data['subject'] = trim($data['subject']);
$data['message'] = trim($data['message']);

// Process the enquiry
$api = new EnquiriesAPI();
$result = $api->submitEnquiry($data);

// Return response
if ($result['success']) {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);
?>

<?php
/**
 * Counselling Session Booking Handler
 * Processes counselling session booking form submissions
 */

require_once 'config/config.php';
require_once 'api/counselling.php';

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
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'preferred_date' => $_POST['preferred_date'] ?? '',
    'preferred_time' => $_POST['preferred_time'] ?? '',
    'session_type' => $_POST['session_type'] ?? 'Zoom',
    'stream' => $_POST['stream'] ?? '',
    'interests' => $_POST['interests'] ?? [],
    'budget_range' => $_POST['budget_range'] ?? '',
    'location_preference' => $_POST['location_preference'] ?? ''
];

// Handle interests array
if (is_string($data['interests'])) {
    $data['interests'] = explode(',', $data['interests']);
}

// Clean and validate data
$data['name'] = trim($data['name']);
$data['email'] = trim($data['email']);
$data['phone'] = trim($data['phone']);

// Process the booking
$api = new CounsellingAPI();
$result = $api->bookSession($data);

// Return response
if ($result['success']) {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);
?>

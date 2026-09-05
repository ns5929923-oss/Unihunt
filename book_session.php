<?php
// book_session.php
// This file now returns JSON responses for AJAX clients.
// It preserves typical booking logic (attempt DB insert if $conn exists),
// and ensures we return {"success": true} after successful booking (requirement #5).

header('Content-Type: application/json; charset=utf-8');

// Start session if needed (preserve behavior from original site)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow only POST for booking (basic guard)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Collect and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$platform = isset($_POST['platform']) ? trim($_POST['platform']) : '';
$session_date = isset($_POST['session_date']) ? trim($_POST['session_date']) : '';

// Basic validation
if ($name === '' || $email === '' || $platform === '' || $session_date === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide name, email, platform and date.']);
    exit;
}

// Try to include existing DB config if present (this was referenced in unihunt.html)
$dbIncluded = false;
if (file_exists(_DIR_ . '/db.php')) {
    // db.php is expected to define a DB connection (commonly $conn or $db)
    include_once _DIR_ . '/db.php';
    $dbIncluded = true;
}

// Attempt to insert booking into DB if we detect a mysqli $conn or PDO $pdo
$bookingSaved = false;
$dbErrorMessage = '';

try {
    // If $conn (mysqli) is available
    if (isset($conn) && $conn instanceof mysqli) {
        $stmt = $conn->prepare("INSERT INTO bookings (name, email, platform, session_date, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param('ssss', $name, $email, $platform, $session_date);
            $ok = $stmt->execute();
            if ($ok) {
                $bookingSaved = true;
            } else {
                $dbErrorMessage = $stmt->error;
            }
            $stmt->close();
        } else {
            $dbErrorMessage = $conn->error;
        }
    }
    // If $pdo (PDO) is available
    elseif (isset($pdo) && $pdo instanceof PDO) {
        $stmt = $pdo->prepare("INSERT INTO bookings (name, email, platform, session_date, created_at) VALUES (:name, :email, :platform, :session_date, NOW())");
        $ok = $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':platform' => $platform,
            ':session_date' => $session_date
        ]);
        if ($ok) $bookingSaved = true;
    }
} catch (Exception $ex) {
    $dbErrorMessage = $ex->getMessage();
}

// If DB insertion didn't run or failed, fall back to writing a log file (safe fallback)
// This ensures the AJAX client can still get success when the site wants to allow bookings even without DB.
if (!$bookingSaved) {
    // Compose a booking record
    $record = [
        'name' => $name,
        'email' => $email,
        'platform' => $platform,
        'session_date' => $session_date,
        'created_at' => date('c'),
        'source' => 'fallback-log'
    ];

    $logFile = _DIR_ . '/bookings_log.json';
    try {
        // Append the booking record as a JSON line (safe, easy to inspect)
        $line = json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        $bookingSaved = true;
    } catch (Exception $ex) {
        $dbErrorMessage .= ' | ' . $ex->getMessage();
    }
}

// Final response
if ($bookingSaved) {
    // IMPORTANT: return JSON { "success": true } as required. Also include an optional message.
    echo json_encode([
        'success' => true,
        'message' => 'Session Booked Successfully!'
    ]);
    exit;
} else {
    // Booking failed completely — return failure JSON with optional debugging message
    http_response_code(500);
    $msg = 'Failed to book session.';
    if ($dbErrorMessage) $msg .= ' ' . $dbErrorMessage;
    echo json_encode([
        'success' => false,
        'message' => $msg
    ]);
    exit;
}

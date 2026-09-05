<?php
/**
 * Application Configuration
 * Global settings and constants for UniHunt
 */

// Application Settings
define('APP_NAME', 'UniHunt');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/project%20unihunt');

// Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'unihunt');

// Security Settings
define('ENCRYPTION_KEY', 'unihunt_2024_secure_key');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Email Settings (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// File Upload Settings
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Pagination Settings
define('RECORDS_PER_PAGE', 10);

// API Settings
define('API_VERSION', 'v1');
define('API_BASE_URL', '/api/' . API_VERSION);

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

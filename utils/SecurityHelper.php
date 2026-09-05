<?php
/**
 * Security Helper Class
 * Provides security utilities for input validation, sanitization, and protection
 */

class SecurityHelper {
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Indian format)
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return preg_match('/^(\+91|91|0)?[6-9]\d{9}$/', $phone);
    }
    
    /**
     * Validate required fields
     */
    public static function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "Field '$field' is required";
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate string length
     */
    public static function validateLength($string, $min = 0, $max = 255) {
        $length = strlen(trim($string));
        return $length >= $min && $length <= $max;
    }
    
    /**
     * Validate numeric range
     */
    public static function validateRange($number, $min = null, $max = null) {
        if (!is_numeric($number)) {
            return false;
        }
        
        $number = floatval($number);
        
        if ($min !== null && $number < $min) {
            return false;
        }
        
        if ($max !== null && $number > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate date format
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Validate time format
     */
    public static function validateTime($time, $format = 'H:i:s') {
        $t = DateTime::createFromFormat($format, $time);
        return $t && $t->format($format) === $time;
    }
    
    /**
     * Validate JSON string
     */
    public static function validateJson($json) {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => $now];
            return true;
        }
        
        $data = $_SESSION[$key];
        
        // Reset if time window has passed
        if ($now - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => $now];
            return true;
        }
        
        // Check if limit exceeded
        if ($data['count'] >= $maxAttempts) {
            return false;
        }
        
        // Increment count
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Sanitize filename for upload
     */
    public static function sanitizeFilename($filename) {
        // Remove any character that isn't alphanumeric, dot, dash, or underscore
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Remove multiple dots
        $filename = preg_replace('/\.{2,}/', '.', $filename);
        
        // Ensure filename isn't empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'], $maxSize = 5242880) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check for PHP code in file content
        $fileContent = file_get_contents($file['tmp_name']);
        if (strpos($fileContent, '<?php') !== false || strpos($fileContent, '<?=') !== false) {
            $errors[] = 'File contains PHP code';
        }
        
        return $errors;
    }
    
    /**
     * Escape output for HTML
     */
    public static function escapeOutput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escapeOutput'], $data);
        }
        
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate secure random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        $logFile = 'logs/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check for SQL injection patterns
     */
    public static function detectSQLInjection($input) {
        $patterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bupdate\b.*\bset\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\balter\b.*\btable\b)/i',
            '/(\bexec\b|\bexecute\b)/i',
            '/(\bscript\b.*\b>)/i',
            '/(\bjavascript\b:)/i',
            '/(\bonload\b|\bonerror\b)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate API key
     */
    public static function validateAPIKey($apiKey) {
        // In a real implementation, you would check against a database
        // For now, we'll use a simple hardcoded key
        $validKeys = ['unihunt_api_key_2024'];
        
        return in_array($apiKey, $validKeys);
    }
    
    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Strict Transport Security (HTTPS only)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
?>

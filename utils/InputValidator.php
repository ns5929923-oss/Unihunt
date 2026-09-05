<?php
/**
 * Input Validator Class
 * Provides comprehensive input validation for forms and API endpoints
 */

require_once 'SecurityHelper.php';

class InputValidator {
    
    private $errors = [];
    private $data = [];
    
    /**
     * Constructor
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate counselling session data
     */
    public function validateCounsellingSession($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['name', 'email', 'session_type'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate name
        if (isset($data['name']) && !SecurityHelper::validateLength($data['name'], 2, 100)) {
            $this->errors[] = 'Name must be between 2 and 100 characters';
        }
        
        // Validate email
        if (isset($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
            $this->errors[] = 'Invalid email format';
        }
        
        // Validate phone (if provided)
        if (!empty($data['phone']) && !SecurityHelper::validatePhone($data['phone'])) {
            $this->errors[] = 'Invalid phone number format';
        }
        
        // Validate session type
        $validSessionTypes = ['Zoom', 'Phone', 'Chat', 'In-person'];
        if (isset($data['session_type']) && !in_array($data['session_type'], $validSessionTypes)) {
            $this->errors[] = 'Invalid session type';
        }
        
        // Validate date (if provided)
        if (!empty($data['preferred_date']) && !SecurityHelper::validateDate($data['preferred_date'])) {
            $this->errors[] = 'Invalid date format';
        }
        
        // Validate time (if provided)
        if (!empty($data['preferred_time']) && !SecurityHelper::validateTime($data['preferred_time'])) {
            $this->errors[] = 'Invalid time format';
        }
        
        // Check for SQL injection
        foreach ($data as $key => $value) {
            if (is_string($value) && SecurityHelper::detectSQLInjection($value)) {
                $this->errors[] = "Suspicious input detected in field: $key";
                SecurityHelper::logSecurityEvent('sql_injection_attempt', [
                    'field' => $key,
                    'value' => $value,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate enquiry data
     */
    public function validateEnquiry($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['type', 'name', 'email'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate enquiry type
        $validTypes = ['Enquire', 'Apply', 'Booking', 'General'];
        if (isset($data['type']) && !in_array($data['type'], $validTypes)) {
            $this->errors[] = 'Invalid enquiry type';
        }
        
        // Validate name
        if (isset($data['name']) && !SecurityHelper::validateLength($data['name'], 2, 100)) {
            $this->errors[] = 'Name must be between 2 and 100 characters';
        }
        
        // Validate email
        if (isset($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
            $this->errors[] = 'Invalid email format';
        }
        
        // Validate phone (if provided)
        if (!empty($data['phone']) && !SecurityHelper::validatePhone($data['phone'])) {
            $this->errors[] = 'Invalid phone number format';
        }
        
        // Validate subject (if provided)
        if (!empty($data['subject']) && !SecurityHelper::validateLength($data['subject'], 5, 200)) {
            $this->errors[] = 'Subject must be between 5 and 200 characters';
        }
        
        // Validate message (if provided)
        if (!empty($data['message']) && !SecurityHelper::validateLength($data['message'], 10, 1000)) {
            $this->errors[] = 'Message must be between 10 and 1000 characters';
        }
        
        // Validate priority
        $validPriorities = ['low', 'medium', 'high'];
        if (!empty($data['priority']) && !in_array($data['priority'], $validPriorities)) {
            $this->errors[] = 'Invalid priority level';
        }
        
        // Check for SQL injection
        foreach ($data as $key => $value) {
            if (is_string($value) && SecurityHelper::detectSQLInjection($value)) {
                $this->errors[] = "Suspicious input detected in field: $key";
                SecurityHelper::logSecurityEvent('sql_injection_attempt', [
                    'field' => $key,
                    'value' => $value,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate quiz data
     */
    public function validateQuiz($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['answers'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate answers
        if (isset($data['answers']) && is_array($data['answers'])) {
            foreach ($data['answers'] as $questionId => $answer) {
                if (!is_numeric($answer) || !SecurityHelper::validateRange($answer, -2, 2)) {
                    $this->errors[] = "Invalid answer for question $questionId";
                }
            }
        } else {
            $this->errors[] = 'Answers must be provided as an array';
        }
        
        // Validate session ID (if provided)
        if (!empty($data['session_id']) && !SecurityHelper::validateLength($data['session_id'], 10, 50)) {
            $this->errors[] = 'Invalid session ID format';
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate newsletter subscription
     */
    public function validateNewsletter($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['email'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate email
        if (isset($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
            $this->errors[] = 'Invalid email format';
        }
        
        // Validate name (if provided)
        if (!empty($data['name']) && !SecurityHelper::validateLength($data['name'], 2, 100)) {
            $this->errors[] = 'Name must be between 2 and 100 characters';
        }
        
        // Validate action
        $validActions = ['subscribe', 'unsubscribe'];
        if (!empty($data['action']) && !in_array($data['action'], $validActions)) {
            $this->errors[] = 'Invalid action';
        }
        
        // Validate interests (if provided)
        if (!empty($data['interests']) && is_array($data['interests'])) {
            foreach ($data['interests'] as $interest) {
                if (!SecurityHelper::validateLength($interest, 2, 50)) {
                    $this->errors[] = 'Each interest must be between 2 and 50 characters';
                    break;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate university data (for admin)
     */
    public function validateUniversity($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['id', 'name', 'city', 'type'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate ID
        if (isset($data['id']) && !SecurityHelper::validateLength($data['id'], 2, 10)) {
            $this->errors[] = 'University ID must be between 2 and 10 characters';
        }
        
        // Validate name
        if (isset($data['name']) && !SecurityHelper::validateLength($data['name'], 5, 255)) {
            $this->errors[] = 'University name must be between 5 and 255 characters';
        }
        
        // Validate city
        if (isset($data['city']) && !SecurityHelper::validateLength($data['city'], 2, 100)) {
            $this->errors[] = 'City must be between 2 and 100 characters';
        }
        
        // Validate type
        $validTypes = ['Government', 'Private'];
        if (isset($data['type']) && !in_array($data['type'], $validTypes)) {
            $this->errors[] = 'Invalid university type';
        }
        
        // Validate rating
        if (!empty($data['rating']) && !SecurityHelper::validateRange($data['rating'], 0, 5)) {
            $this->errors[] = 'Rating must be between 0 and 5';
        }
        
        // Validate budget
        if (!empty($data['budget_k']) && !SecurityHelper::validateRange($data['budget_k'], 0, 10000)) {
            $this->errors[] = 'Budget must be between 0 and 10,000 (in thousands)';
        }
        
        // Validate JSON fields
        $jsonFields = ['streams', 'courses', 'exams', 'facilities', 'images', 'fees', 'reviews'];
        foreach ($jsonFields as $field) {
            if (!empty($data[$field])) {
                if (is_string($data[$field]) && !SecurityHelper::validateJson($data[$field])) {
                    $this->errors[] = "Invalid JSON format for field: $field";
                }
            }
        }
        
        // Validate email (if provided)
        if (!empty($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
            $this->errors[] = 'Invalid email format';
        }
        
        // Validate phone (if provided)
        if (!empty($data['phone']) && !SecurityHelper::validatePhone($data['phone'])) {
            $this->errors[] = 'Invalid phone number format';
        }
        
        // Validate website (if provided)
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $this->errors[] = 'Invalid website URL';
        }
        
        // Validate established year
        if (!empty($data['established_year'])) {
            $currentYear = date('Y');
            if (!SecurityHelper::validateRange($data['established_year'], 1800, $currentYear)) {
                $this->errors[] = 'Invalid established year';
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate blog data (for admin)
     */
    public function validateBlog($data) {
        $this->errors = [];
        
        // Required fields
        $required = ['id', 'title', 'tag'];
        $requiredErrors = SecurityHelper::validateRequired($data, $required);
        $this->errors = array_merge($this->errors, $requiredErrors);
        
        // Validate ID
        if (isset($data['id']) && !SecurityHelper::validateLength($data['id'], 2, 10)) {
            $this->errors[] = 'Blog ID must be between 2 and 10 characters';
        }
        
        // Validate title
        if (isset($data['title']) && !SecurityHelper::validateLength($data['title'], 10, 255)) {
            $this->errors[] = 'Title must be between 10 and 255 characters';
        }
        
        // Validate slug (if provided)
        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $this->errors[] = 'Slug must contain only lowercase letters, numbers, and hyphens';
        }
        
        // Validate tag
        if (isset($data['tag']) && !SecurityHelper::validateLength($data['tag'], 2, 50)) {
            $this->errors[] = 'Tag must be between 2 and 50 characters';
        }
        
        // Validate excerpt (if provided)
        if (!empty($data['excerpt']) && !SecurityHelper::validateLength($data['excerpt'], 10, 500)) {
            $this->errors[] = 'Excerpt must be between 10 and 500 characters';
        }
        
        // Validate content (if provided)
        if (!empty($data['content']) && !SecurityHelper::validateLength($data['content'], 50, 10000)) {
            $this->errors[] = 'Content must be between 50 and 10,000 characters';
        }
        
        // Validate author (if provided)
        if (!empty($data['author']) && !SecurityHelper::validateLength($data['author'], 2, 100)) {
            $this->errors[] = 'Author name must be between 2 and 100 characters';
        }
        
        // Validate status
        $validStatuses = ['draft', 'published', 'archived'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
            $this->errors[] = 'Invalid status';
        }
        
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get first error
     */
    public function getFirstError() {
        return !empty($this->errors) ? $this->errors[0] : null;
    }
    
    /**
     * Check if validation passed
     */
    public function isValid() {
        return empty($this->errors);
    }
    
    /**
     * Sanitize all input data
     */
    public function sanitizeData($data) {
        return SecurityHelper::sanitizeInput($data);
    }
}
?>

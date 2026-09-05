<?php
/**
 * Universities API Controller
 * Handles all university-related API endpoints
 */

require_once '../config/config.php';
require_once '../config/database.php';

class UniversitiesAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all universities with optional filtering
     */
    public function getUniversities($filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM universities WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['stream'])) {
                $sql .= " AND JSON_CONTAINS(streams, :stream)";
                $params[':stream'] = json_encode($filters['stream']);
            }
            
            if (!empty($filters['course'])) {
                $sql .= " AND JSON_CONTAINS(courses, :course)";
                $params[':course'] = json_encode($filters['course']);
            }
            
            if (!empty($filters['exam'])) {
                $sql .= " AND JSON_CONTAINS(exams, :exam)";
                $params[':exam'] = json_encode($filters['exam']);
            }
            
            if (!empty($filters['type'])) {
                $sql .= " AND type = :type";
                $params[':type'] = $filters['type'];
            }
            
            if (!empty($filters['city'])) {
                $sql .= " AND city = :city";
                $params[':city'] = $filters['city'];
            }
            
            if (!empty($filters['budget'])) {
                $sql .= " AND budget_k <= :budget";
                $params[':budget'] = $filters['budget'];
            }
            
            if (!empty($filters['featured'])) {
                $sql .= " AND featured = :featured";
                $params[':featured'] = $filters['featured'];
            }
            
            // Ordering
            $sql .= " ORDER BY featured DESC, rating DESC, name ASC";
            
            // Pagination
            if (!empty($filters['page']) && !empty($filters['limit'])) {
                $offset = ($filters['page'] - 1) * $filters['limit'];
                $sql .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $filters['limit'];
                $params[':offset'] = $offset;
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $universities = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($universities as &$uni) {
                $uni['streams'] = json_decode($uni['streams'], true);
                $uni['courses'] = json_decode($uni['courses'], true);
                $uni['exams'] = json_decode($uni['exams'], true);
                $uni['facilities'] = json_decode($uni['facilities'], true);
                $uni['images'] = json_decode($uni['images'], true);
                $uni['fees'] = json_decode($uni['fees'], true);
                $uni['reviews'] = json_decode($uni['reviews'], true);
            }
            
            return [
                'success' => true,
                'data' => $universities,
                'count' => count($universities)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get a single university by ID
     */
    public function getUniversity($id) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM universities WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $university = $stmt->fetch();
            
            if (!$university) {
                return [
                    'success' => false,
                    'error' => 'University not found'
                ];
            }
            
            // Decode JSON fields
            $university['streams'] = json_decode($university['streams'], true);
            $university['courses'] = json_decode($university['courses'], true);
            $university['exams'] = json_decode($university['exams'], true);
            $university['facilities'] = json_decode($university['facilities'], true);
            $university['images'] = json_decode($university['images'], true);
            $university['fees'] = json_decode($university['fees'], true);
            $university['reviews'] = json_decode($university['reviews'], true);
            
            return [
                'success' => true,
                'data' => $university
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search universities by name or description
     */
    public function searchUniversities($query, $filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM universities WHERE 
                    (name LIKE :query OR overview LIKE :query OR city LIKE :query)";
            $params = [':query' => '%' . $query . '%'];
            
            // Apply additional filters
            if (!empty($filters['stream'])) {
                $sql .= " AND JSON_CONTAINS(streams, :stream)";
                $params[':stream'] = json_encode($filters['stream']);
            }
            
            if (!empty($filters['type'])) {
                $sql .= " AND type = :type";
                $params[':type'] = $filters['type'];
            }
            
            $sql .= " ORDER BY featured DESC, rating DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $universities = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($universities as &$uni) {
                $uni['streams'] = json_decode($uni['streams'], true);
                $uni['courses'] = json_decode($uni['courses'], true);
                $uni['exams'] = json_decode($uni['exams'], true);
                $uni['facilities'] = json_decode($uni['facilities'], true);
                $uni['images'] = json_decode($uni['images'], true);
                $uni['fees'] = json_decode($uni['fees'], true);
                $uni['reviews'] = json_decode($uni['reviews'], true);
            }
            
            return [
                'success' => true,
                'data' => $universities,
                'count' => count($universities),
                'query' => $query
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get universities by city
     */
    public function getUniversitiesByCity($city) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM universities WHERE city = :city ORDER BY featured DESC, rating DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':city' => $city]);
            $universities = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($universities as &$uni) {
                $uni['streams'] = json_decode($uni['streams'], true);
                $uni['courses'] = json_decode($uni['courses'], true);
                $uni['exams'] = json_decode($uni['exams'], true);
                $uni['facilities'] = json_decode($uni['facilities'], true);
                $uni['images'] = json_decode($uni['images'], true);
                $uni['fees'] = json_decode($uni['fees'], true);
                $uni['reviews'] = json_decode($uni['reviews'], true);
            }
            
            return [
                'success' => true,
                'data' => $universities,
                'count' => count($universities),
                'city' => $city
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get featured universities
     */
    public function getFeaturedUniversities() {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM universities WHERE featured = 1 ORDER BY rating DESC LIMIT 10";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $universities = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($universities as &$uni) {
                $uni['streams'] = json_decode($uni['streams'], true);
                $uni['courses'] = json_decode($uni['courses'], true);
                $uni['exams'] = json_decode($uni['exams'], true);
                $uni['facilities'] = json_decode($uni['facilities'], true);
                $uni['images'] = json_decode($uni['images'], true);
                $uni['fees'] = json_decode($uni['fees'], true);
                $uni['reviews'] = json_decode($uni['reviews'], true);
            }
            
            return [
                'success' => true,
                'data' => $universities,
                'count' => count($universities)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new UniversitiesAPI();
    $response = [];
    
    // Get query parameters
    $filters = [
        'stream' => $_GET['stream'] ?? '',
        'course' => $_GET['course'] ?? '',
        'exam' => $_GET['exam'] ?? '',
        'type' => $_GET['type'] ?? '',
        'city' => $_GET['city'] ?? '',
        'budget' => $_GET['budget'] ?? '',
        'featured' => $_GET['featured'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'limit' => $_GET['limit'] ?? 20
    ];
    
    // Remove empty filters
    $filters = array_filter($filters, function($value) {
        return $value !== '';
    });
    
    // Determine endpoint
    if (isset($_GET['id'])) {
        $response = $api->getUniversity($_GET['id']);
    } elseif (isset($_GET['search'])) {
        $response = $api->searchUniversities($_GET['search'], $filters);
    } elseif (isset($_GET['city'])) {
        $response = $api->getUniversitiesByCity($_GET['city']);
    } elseif (isset($_GET['featured'])) {
        $response = $api->getFeaturedUniversities();
    } else {
        $response = $api->getUniversities($filters);
    }
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

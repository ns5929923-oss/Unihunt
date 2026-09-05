<?php
/**
 * Blogs API Controller
 * Handles all blog-related API endpoints
 */

require_once '../config/config.php';
require_once '../config/database.php';

class BlogsAPI {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all blogs with optional filtering
     */
    public function getBlogs($filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM blogs WHERE status = 'published'";
            $params = [];
            
            // Apply filters
            if (!empty($filters['tag'])) {
                $sql .= " AND tag = :tag";
                $params[':tag'] = $filters['tag'];
            }
            
            if (!empty($filters['author'])) {
                $sql .= " AND author = :author";
                $params[':author'] = $filters['author'];
            }
            
            // Ordering
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
            $blogs = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $blogs,
                'count' => count($blogs)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get a single blog by ID or slug
     */
    public function getBlog($identifier) {
        try {
            $conn = $this->db->getConnection();
            
            // Check if identifier is numeric (ID) or string (slug)
            if (is_numeric($identifier)) {
                $sql = "SELECT * FROM blogs WHERE id = :identifier AND status = 'published'";
            } else {
                $sql = "SELECT * FROM blogs WHERE slug = :identifier AND status = 'published'";
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([':identifier' => $identifier]);
            $blog = $stmt->fetch();
            
            if (!$blog) {
                return [
                    'success' => false,
                    'error' => 'Blog not found'
                ];
            }
            
            // Increment view count
            $this->incrementViews($blog['id']);
            
            return [
                'success' => true,
                'data' => $blog
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search blogs by title or content
     */
    public function searchBlogs($query, $filters = []) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM blogs WHERE status = 'published' AND 
                    (title LIKE :query OR content LIKE :query OR excerpt LIKE :query)";
            $params = [':query' => '%' . $query . '%'];
            
            // Apply additional filters
            if (!empty($filters['tag'])) {
                $sql .= " AND tag = :tag";
                $params[':tag'] = $filters['tag'];
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $blogs = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $blogs,
                'count' => count($blogs),
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
     * Get blogs by tag
     */
    public function getBlogsByTag($tag) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM blogs WHERE tag = :tag AND status = 'published' ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':tag' => $tag]);
            $blogs = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $blogs,
                'count' => count($blogs),
                'tag' => $tag
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get popular blogs (by view count)
     */
    public function getPopularBlogs($limit = 5) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT * FROM blogs WHERE status = 'published' ORDER BY views DESC LIMIT :limit";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':limit' => $limit]);
            $blogs = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $blogs,
                'count' => count($blogs)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all unique tags
     */
    public function getTags() {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "SELECT DISTINCT tag, COUNT(*) as count FROM blogs WHERE status = 'published' GROUP BY tag ORDER BY count DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $tags = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $tags,
                'count' => count($tags)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Increment blog view count
     */
    private function incrementViews($blogId) {
        try {
            $conn = $this->db->getConnection();
            
            $sql = "UPDATE blogs SET views = views + 1 WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $blogId]);
            
        } catch (PDOException $e) {
            // Silently fail for view counting
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $api = new BlogsAPI();
    $response = [];
    
    // Get query parameters
    $filters = [
        'tag' => $_GET['tag'] ?? '',
        'author' => $_GET['author'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'limit' => $_GET['limit'] ?? 10
    ];
    
    // Remove empty filters
    $filters = array_filter($filters, function($value) {
        return $value !== '';
    });
    
    // Determine endpoint
    if (isset($_GET['id']) || isset($_GET['slug'])) {
        $identifier = $_GET['id'] ?? $_GET['slug'];
        $response = $api->getBlog($identifier);
    } elseif (isset($_GET['search'])) {
        $response = $api->searchBlogs($_GET['search'], $filters);
    } elseif (isset($_GET['tag'])) {
        $response = $api->getBlogsByTag($_GET['tag']);
    } elseif (isset($_GET['popular'])) {
        $limit = $_GET['limit'] ?? 5;
        $response = $api->getPopularBlogs($limit);
    } elseif (isset($_GET['tags'])) {
        $response = $api->getTags();
    } else {
        $response = $api->getBlogs($filters);
    }
    
    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

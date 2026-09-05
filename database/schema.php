<?php
/**
 * Complete Database Schema for UniHunt
 * Creates all necessary tables with proper relationships
 */

require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Create Universities table
    $sql = "CREATE TABLE IF NOT EXISTS universities (
        id VARCHAR(10) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        city VARCHAR(100) NOT NULL,
        state VARCHAR(100) DEFAULT 'Gujarat',
        type ENUM('Government', 'Private') NOT NULL,
        featured BOOLEAN DEFAULT FALSE,
        streams JSON NOT NULL,
        courses JSON NOT NULL,
        exams JSON NOT NULL,
        budget_k INT NOT NULL,
        rating DECIMAL(3,1) NOT NULL,
        placements TEXT,
        facilities JSON,
        images JSON,
        overview TEXT,
        fees JSON,
        reviews JSON,
        website VARCHAR(255),
        phone VARCHAR(20),
        email VARCHAR(255),
        address TEXT,
        established_year YEAR,
        accreditation VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $conn->exec($sql);

    // Create Blogs table
    $sql = "CREATE TABLE IF NOT EXISTS blogs (
        id VARCHAR(10) PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        tag VARCHAR(100) NOT NULL,
        excerpt TEXT,
        content LONGTEXT,
        featured_image VARCHAR(255),
        author VARCHAR(100) DEFAULT 'UniHunt Team',
        status ENUM('draft', 'published', 'archived') DEFAULT 'published',
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $conn->exec($sql);

    // Create Testimonials table
    $sql = "CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT NOT NULL,
        author VARCHAR(100) NOT NULL,
        university_id VARCHAR(10),
        rating INT DEFAULT 5,
        verified BOOLEAN DEFAULT FALSE,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE SET NULL
    )";

    $conn->exec($sql);

    // Create Enquiries table
    $sql = "CREATE TABLE IF NOT EXISTS enquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('Enquire', 'Apply', 'Booking', 'General') NOT NULL,
        university_id VARCHAR(10),
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(255),
        message TEXT,
        details JSON,
        status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        assigned_to VARCHAR(100),
        response TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE SET NULL
    )";

    $conn->exec($sql);

    // Create Counselling Sessions table
    $sql = "CREATE TABLE IF NOT EXISTS counselling_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        preferred_date DATE,
        preferred_time TIME,
        session_type ENUM('Zoom', 'Phone', 'Chat', 'In-person') DEFAULT 'Zoom',
        stream VARCHAR(100),
        interests JSON,
        budget_range VARCHAR(50),
        location_preference VARCHAR(100),
        status ENUM('pending', 'scheduled', 'completed', 'cancelled') DEFAULT 'pending',
        counsellor_notes TEXT,
        session_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $conn->exec($sql);

    // Create Users table (for admin panel)
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        role ENUM('admin', 'moderator', 'counsellor') DEFAULT 'moderator',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $conn->exec($sql);

    // Create Quiz Results table
    $sql = "CREATE TABLE IF NOT EXISTS quiz_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(100) NOT NULL,
        answers JSON NOT NULL,
        scores JSON NOT NULL,
        recommended_streams JSON NOT NULL,
        university_suggestions JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $conn->exec($sql);

    // Create Newsletter Subscribers table
    $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        name VARCHAR(255),
        interests JSON,
        status ENUM('active', 'unsubscribed') DEFAULT 'active',
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        unsubscribed_at TIMESTAMP NULL
    )";

    $conn->exec($sql);

    // Create University Reviews table
    $sql = "CREATE TABLE IF NOT EXISTS university_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        university_id VARCHAR(10) NOT NULL,
        reviewer_name VARCHAR(255) NOT NULL,
        reviewer_email VARCHAR(255) NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        title VARCHAR(255),
        review_text TEXT NOT NULL,
        pros TEXT,
        cons TEXT,
        verified BOOLEAN DEFAULT FALSE,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        helpful_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
    )";

    $conn->exec($sql);

    // Create Indexes for better performance
    $indexes = [
        "CREATE INDEX idx_universities_city ON universities(city)",
        "CREATE INDEX idx_universities_type ON universities(type)",
        "CREATE INDEX idx_universities_featured ON universities(featured)",
        "CREATE INDEX idx_blogs_tag ON blogs(tag)",
        "CREATE INDEX idx_blogs_status ON blogs(status)",
        "CREATE INDEX idx_enquiries_type ON enquiries(type)",
        "CREATE INDEX idx_enquiries_status ON enquiries(status)",
        "CREATE INDEX idx_counselling_status ON counselling_sessions(status)",
        "CREATE INDEX idx_reviews_university ON university_reviews(university_id)",
        "CREATE INDEX idx_reviews_status ON university_reviews(status)"
    ];

    foreach ($indexes as $index) {
        try {
            $conn->exec($index);
        } catch (PDOException $e) {
            // Index might already exist, continue
        }
    }

    echo "Database schema created successfully!\n";

} catch (PDOException $e) {
    echo "Error creating database schema: " . $e->getMessage() . "\n";
}
?>

<?php
/**
 * Database Seeder
 * Populates the database with sample data for UniHunt
 */

require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Starting database seeding...\n";
    
    // Clear existing data
    $tables = ['quiz_results', 'newsletter_subscribers', 'university_reviews', 'testimonials', 'enquiries', 'counselling_sessions', 'blogs', 'universities'];
    foreach ($tables as $table) {
        $conn->exec("DELETE FROM $table");
        echo "Cleared $table table\n";
    }
    
    // Insert Universities
    $universities = [
        [
            'id' => 'U1',
            'name' => 'Gujarat University',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'type' => 'Government',
            'featured' => true,
            'streams' => ['Science (PCM)', 'Computer/IT', 'Commerce'],
            'courses' => ['B.Tech', 'BCA', 'BBA', 'MBBS', 'B.Sc', 'BA', 'B.Com'],
            'exams' => ['GUJCET'],
            'budget_k' => 300,
            'rating' => 4.0,
            'placements' => 'Avg ₹3.5LPA',
            'facilities' => ['Hostel', 'Labs', 'Sports'],
            'images' => ['🎓', '🏛️'],
            'overview' => 'Leading engineering programs with strong industry ties. Known for practical learning.',
            'fees' => ['B.Tech' => '₹2.46L/yr', 'BCA' => '₹26k/yr', 'B.Sc' => '₹83k/yr', 'BBA' => '₹66k/yr', 'MBBS' => '₹50k/yr', 'B.Com' => '₹50k/yr', 'BA' => '₹10k/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Great labs and placements.'], ['by' => 'Student', 'text' => 'Supportive faculty.']],
            'website' => 'https://gujaratuniversity.ac.in',
            'phone' => '+91-79-26301341',
            'email' => 'info@gujaratuniversity.ac.in',
            'address' => 'Navrangpura, Ahmedabad, Gujarat 380009',
            'established_year' => 1949,
            'accreditation' => 'NAAC A+'
        ],
        [
            'id' => 'U2',
            'name' => 'Nirma University',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'type' => 'Private',
            'featured' => true,
            'streams' => ['Science (PCM)', 'Science (PCB)', 'Computer/IT', 'Commerce', 'Arts/Humanities'],
            'courses' => ['B.Tech', 'B.Com', 'BBA'],
            'exams' => ['NONE'],
            'budget_k' => 130,
            'rating' => 4.4,
            'placements' => 'Avg ₹7.2LPA',
            'facilities' => ['Hostel', 'Labs', 'Sports'],
            'images' => ['🎓', '🏛️'],
            'overview' => 'Within a short period of its existence, it has emerged as a nationally renowned higher education institution.',
            'fees' => ['B.Tech' => '₹2.46L/yr', 'BBA' => '₹3.65L/yr', 'B.Com' => '₹2.28L/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Great labs and placements.'], ['by' => 'Student', 'text' => 'Supportive faculty.']],
            'website' => 'https://nirmauni.ac.in',
            'phone' => '+91-2717-241900',
            'email' => 'info@nirmauni.ac.in',
            'address' => 'Sarkhej-Gandhinagar Highway, Ahmedabad, Gujarat 382481',
            'established_year' => 2003,
            'accreditation' => 'NAAC A+'
        ],
        [
            'id' => 'U3',
            'name' => 'CEPT University',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'type' => 'Private',
            'featured' => true,
            'streams' => ['Science (PCM)', 'Computer/IT'],
            'courses' => ['B.Tech'],
            'exams' => ['NONE'],
            'budget_k' => 399,
            'rating' => 3.7,
            'placements' => 'Avg ₹7.2LPA',
            'facilities' => ['Hostel', 'Labs', 'Sports'],
            'images' => ['🎓', '🏛️'],
            'overview' => 'Leading engineering programs with strong industry ties. Known for practical learning.',
            'fees' => ['B.Tech' => '₹1.2L/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Great labs and placements.'], ['by' => 'Student', 'text' => 'Supportive faculty.']],
            'website' => 'https://cept.ac.in',
            'phone' => '+91-79-26302740',
            'email' => 'info@cept.ac.in',
            'address' => 'Kasturbhai Lalbhai Campus, Ahmedabad, Gujarat 380009',
            'established_year' => 1962,
            'accreditation' => 'NAAC A'
        ],
        [
            'id' => 'U4',
            'name' => 'Ahmedabad University',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'type' => 'Private',
            'featured' => true,
            'streams' => ['Science (PCM)', 'Commerce', 'Computer/IT'],
            'courses' => ['B.Tech', 'BCA', 'BBA', 'B.Sc', 'BA', 'B.Com'],
            'exams' => ['JEE', 'GUJCET'],
            'budget_k' => 120,
            'rating' => 4.5,
            'placements' => 'Avg ₹7.2LPA',
            'facilities' => ['Hostel', 'Labs', 'Sports'],
            'images' => ['🎓', '🏛️'],
            'overview' => 'Leading engineering programs with strong industry ties. Known for practical learning.',
            'fees' => ['B.Tech' => '₹3.75L/yr', 'BCA' => '₹26k/yr', 'B.Sc' => '₹83k/yr', 'BBA' => '₹4.6L/yr', 'B.Com' => '₹4.95L/yr', 'BA' => '₹4.95L/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Great labs and placements.'], ['by' => 'Student', 'text' => 'Supportive faculty, Great infrastructure.']],
            'website' => 'https://ahduni.edu.in',
            'phone' => '+91-79-61911200',
            'email' => 'info@ahduni.edu.in',
            'address' => 'Commerce Six Roads, Ahmedabad, Gujarat 380009',
            'established_year' => 2009,
            'accreditation' => 'NAAC A+'
        ],
        [
            'id' => 'U5',
            'name' => 'SVNIT Surat',
            'city' => 'Surat',
            'state' => 'Gujarat',
            'type' => 'Government',
            'featured' => true,
            'streams' => ['Science (PCM)', 'Computer/IT'],
            'courses' => ['B.Tech'],
            'exams' => ['JEE'],
            'budget_k' => 120,
            'rating' => 4.0,
            'placements' => 'Avg ₹10.2LPA',
            'facilities' => ['Hostel', 'Labs', 'Sports'],
            'images' => ['🎓', '🏛️'],
            'overview' => 'Leading engineering programs with strong industry ties. Known for practical learning.',
            'fees' => ['B.Tech' => '₹1.2L/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Great labs and placements.'], ['by' => 'Student', 'text' => 'Supportive faculty.']],
            'website' => 'https://svnit.ac.in',
            'phone' => '+91-261-2201540',
            'email' => 'info@svnit.ac.in',
            'address' => 'Ichchhanath, Surat, Gujarat 395007',
            'established_year' => 1961,
            'accreditation' => 'NAAC A+'
        ],
        [
            'id' => 'U6',
            'name' => 'Surat Medical College',
            'city' => 'Surat',
            'state' => 'Gujarat',
            'type' => 'Government',
            'featured' => false,
            'streams' => ['Science (PCB)'],
            'courses' => ['MBBS', 'B.Sc'],
            'exams' => ['NEET'],
            'budget_k' => 300,
            'rating' => 4.2,
            'placements' => 'Residency support',
            'facilities' => ['Hospital', 'Hostel'],
            'images' => ['🧪', '🏥'],
            'overview' => 'Strong clinical exposure and experienced faculty for medical aspirants.',
            'fees' => ['MBBS' => '₹3L/yr', 'B.Sc' => '₹60k/yr'],
            'reviews' => [['by' => 'Parent', 'text' => 'Good patient inflow.']],
            'website' => 'https://suratmedicalcollege.edu.in',
            'phone' => '+91-261-2444444',
            'email' => 'info@suratmedicalcollege.edu.in',
            'address' => 'Umarwada, Surat, Gujarat 395010',
            'established_year' => 1964,
            'accreditation' => 'MCI Approved'
        ],
        [
            'id' => 'U7',
            'name' => 'Vadodara School of Management',
            'city' => 'Vadodara',
            'state' => 'Gujarat',
            'type' => 'Private',
            'featured' => true,
            'streams' => ['Management', 'Commerce'],
            'courses' => ['BBA', 'B.Com'],
            'exams' => ['CUET', 'None'],
            'budget_k' => 90,
            'rating' => 4.0,
            'placements' => 'Avg ₹4.8LPA',
            'facilities' => ['Incubator', 'Library'],
            'images' => ['📈', '📚'],
            'overview' => 'Entrepreneurship-focused curriculum with live projects.',
            'fees' => ['BBA' => '₹90k/yr', 'B.Com' => '₹50k/yr'],
            'reviews' => [['by' => 'Student', 'text' => 'Great for internships.']],
            'website' => 'https://vsm.edu.in',
            'phone' => '+91-265-2251000',
            'email' => 'info@vsm.edu.in',
            'address' => 'Vadodara, Gujarat 390001',
            'established_year' => 1995,
            'accreditation' => 'NAAC A'
        ],
        [
            'id' => 'U8',
            'name' => 'Rajkot Arts & Humanities College',
            'city' => 'Rajkot',
            'state' => 'Gujarat',
            'type' => 'Private',
            'featured' => false,
            'streams' => ['Arts/Humanities'],
            'courses' => ['BA'],
            'exams' => ['CUET', 'None'],
            'budget_k' => 40,
            'rating' => 3.8,
            'placements' => 'Training & NGOs',
            'facilities' => ['Clubs', 'Auditorium'],
            'images' => ['🎭', '🏫'],
            'overview' => 'Vibrant cultural scene with strong humanities faculty.',
            'fees' => ['BA' => '₹40k/yr'],
            'reviews' => [['by' => 'Alumni', 'text' => 'Engaging professors.']],
            'website' => 'https://rajkotarts.edu.in',
            'phone' => '+91-281-2444444',
            'email' => 'info@rajkotarts.edu.in',
            'address' => 'Rajkot, Gujarat 360001',
            'established_year' => 1985,
            'accreditation' => 'NAAC B+'
        ]
    ];
    
    $sql = "INSERT INTO universities (id, name, city, state, type, featured, streams, courses, exams, budget_k, rating, placements, facilities, images, overview, fees, reviews, website, phone, email, address, established_year, accreditation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($universities as $uni) {
        $stmt->execute([
            $uni['id'],
            $uni['name'],
            $uni['city'],
            $uni['state'],
            $uni['type'],
            $uni['featured'],
            json_encode($uni['streams']),
            json_encode($uni['courses']),
            json_encode($uni['exams']),
            $uni['budget_k'],
            $uni['rating'],
            $uni['placements'],
            json_encode($uni['facilities']),
            json_encode($uni['images']),
            $uni['overview'],
            json_encode($uni['fees']),
            json_encode($uni['reviews']),
            $uni['website'],
            $uni['phone'],
            $uni['email'],
            $uni['address'],
            $uni['established_year'],
            $uni['accreditation']
        ]);
    }
    echo "Inserted " . count($universities) . " universities\n";
    
    // Insert Blogs
    $blogs = [
        [
            'id' => 'B1',
            'title' => 'Top 5 Mistakes Students Make After 12th',
            'slug' => 'top-5-mistakes-students-make-after-12th',
            'tag' => 'Guidance',
            'excerpt' => 'Avoid these common pitfalls while choosing your path…',
            'content' => 'Choosing the right path after 12th grade is crucial for your future. Here are the top 5 mistakes students make and how to avoid them...',
            'author' => 'UniHunt Team',
            'status' => 'published'
        ],
        [
            'id' => 'B2',
            'title' => 'Science vs Commerce vs Arts – Which Should You Choose?',
            'slug' => 'science-vs-commerce-vs-arts-which-should-you-choose',
            'tag' => 'Streams',
            'excerpt' => 'A simple framework to pick your stream…',
            'content' => 'Deciding between Science, Commerce, and Arts can be overwhelming. Here\'s a comprehensive guide to help you make the right choice...',
            'author' => 'UniHunt Team',
            'status' => 'published'
        ],
        [
            'id' => 'B3',
            'title' => 'Affordable Colleges in Gujarat',
            'slug' => 'affordable-colleges-in-gujarat',
            'tag' => 'Budget',
            'excerpt' => 'Value-for-money picks across cities…',
            'content' => 'Finding quality education at affordable prices in Gujarat. Here are the best value-for-money colleges across different cities...',
            'author' => 'UniHunt Team',
            'status' => 'published'
        ],
        [
            'id' => 'B4',
            'title' => 'Top 10 Engineering Colleges in Gujarat',
            'slug' => 'top-10-engineering-colleges-in-gujarat',
            'tag' => 'Engineering',
            'excerpt' => 'Our Gujarat-first list to start your search…',
            'content' => 'Gujarat is home to some of the finest engineering colleges in India. Here\'s our comprehensive list of the top 10...',
            'author' => 'UniHunt Team',
            'status' => 'published'
        ],
        [
            'id' => 'B5',
            'title' => 'Best BBA Colleges in Gujarat',
            'slug' => 'best-bba-colleges-in-gujarat',
            'tag' => 'Management',
            'excerpt' => 'Shortlist these colleges for BBA aspirants…',
            'content' => 'Looking for the best BBA programs in Gujarat? Here are the top colleges that offer excellent management education...',
            'author' => 'UniHunt Team',
            'status' => 'published'
        ]
    ];
    
    $sql = "INSERT INTO blogs (id, title, slug, tag, excerpt, content, author, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($blogs as $blog) {
        $stmt->execute([
            $blog['id'],
            $blog['title'],
            $blog['slug'],
            $blog['tag'],
            $blog['excerpt'],
            $blog['content'],
            $blog['author'],
            $blog['status']
        ]);
    }
    echo "Inserted " . count($blogs) . " blogs\n";
    
    // Insert Testimonials
    $testimonials = [
        ['text' => '"The quiz matched me with PCM and I found great B.Tech options in Ahmedabad."', 'author' => 'Riya', 'university_id' => 'U1', 'verified' => true, 'status' => 'approved'],
        ['text' => '"Filters made it easy to shortlist budget colleges in Vadodara."', 'author' => 'Krunal', 'university_id' => 'U7', 'verified' => true, 'status' => 'approved'],
        ['text' => '"Loved the quick compare. Helped me choose faster."', 'author' => 'Aisha', 'university_id' => 'U2', 'verified' => true, 'status' => 'approved'],
        ['text' => '"Great platform for finding universities in Gujarat!"', 'author' => 'Priya', 'university_id' => 'U4', 'verified' => true, 'status' => 'approved'],
        ['text' => '"The counselling session was very helpful for my career guidance."', 'author' => 'Rahul', 'university_id' => 'U5', 'verified' => true, 'status' => 'approved']
    ];
    
    $sql = "INSERT INTO testimonials (text, author, university_id, verified, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($testimonials as $testimonial) {
        $stmt->execute([
            $testimonial['text'],
            $testimonial['author'],
            $testimonial['university_id'],
            $testimonial['verified'],
            $testimonial['status']
        ]);
    }
    echo "Inserted " . count($testimonials) . " testimonials\n";
    
    // Insert Sample Enquiries
    $enquiries = [
        [
            'type' => 'Enquire',
            'university_id' => 'U1',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+91-9876543210',
            'subject' => 'B.Tech Admission Query',
            'message' => 'I am interested in B.Tech Computer Science. Can you provide more details about the admission process?',
            'status' => 'new',
            'priority' => 'medium'
        ],
        [
            'type' => 'Apply',
            'university_id' => 'U2',
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+91-9876543211',
            'subject' => 'BBA Application',
            'message' => 'I would like to apply for BBA program. Please guide me through the application process.',
            'status' => 'in_progress',
            'priority' => 'high'
        ],
        [
            'type' => 'General',
            'university_id' => null,
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'phone' => '+91-9876543212',
            'subject' => 'General Information',
            'message' => 'I need help choosing the right stream for my career.',
            'status' => 'resolved',
            'priority' => 'low'
        ]
    ];
    
    $sql = "INSERT INTO enquiries (type, university_id, name, email, phone, subject, message, status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($enquiries as $enquiry) {
        $stmt->execute([
            $enquiry['type'],
            $enquiry['university_id'],
            $enquiry['name'],
            $enquiry['email'],
            $enquiry['phone'],
            $enquiry['subject'],
            $enquiry['message'],
            $enquiry['status'],
            $enquiry['priority']
        ]);
    }
    echo "Inserted " . count($enquiries) . " enquiries\n";
    
    // Insert Sample Counselling Sessions
    $sessions = [
        [
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'phone' => '+91-9876543213',
            'preferred_date' => '2024-02-15',
            'preferred_time' => '14:00:00',
            'session_type' => 'Zoom',
            'stream' => 'Science (PCM)',
            'interests' => ['Engineering', 'Technology'],
            'budget_range' => '1-2 Lakhs',
            'location_preference' => 'Ahmedabad',
            'status' => 'pending'
        ],
        [
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'phone' => '+91-9876543214',
            'preferred_date' => '2024-02-20',
            'preferred_time' => '10:00:00',
            'session_type' => 'Phone',
            'stream' => 'Commerce',
            'interests' => ['Business', 'Finance'],
            'budget_range' => '50k-1 Lakh',
            'location_preference' => 'Surat',
            'status' => 'scheduled'
        ]
    ];
    
    $sql = "INSERT INTO counselling_sessions (name, email, phone, preferred_date, preferred_time, session_type, stream, interests, budget_range, location_preference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($sessions as $session) {
        $stmt->execute([
            $session['name'],
            $session['email'],
            $session['phone'],
            $session['preferred_date'],
            $session['preferred_time'],
            $session['session_type'],
            $session['stream'],
            json_encode($session['interests']),
            $session['budget_range'],
            $session['location_preference'],
            $session['status']
        ]);
    }
    echo "Inserted " . count($sessions) . " counselling sessions\n";
    
    // Insert Admin User
    $sql = "INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'admin',
        'admin@unihunt.com',
        password_hash('admin123', PASSWORD_BCRYPT),
        'Administrator',
        'admin',
        'active'
    ]);
    echo "Inserted admin user\n";
    
    echo "Database seeding completed successfully!\n";
    echo "Admin credentials: admin / admin123\n";
    
} catch (PDOException $e) {
    echo "Error seeding database: " . $e->getMessage() . "\n";
}
?>

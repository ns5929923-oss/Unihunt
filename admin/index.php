<?php
/**
 * Admin Panel Dashboard
 * Main admin interface for managing UniHunt platform
 */

require_once 'config/config.php';
require_once 'config/database.php';

// Simple authentication check (in production, use proper session management)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Get dashboard statistics
try {
    // Universities count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM universities");
    $universitiesCount = $stmt->fetch()['count'];
    
    // Blogs count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE status = 'published'");
    $blogsCount = $stmt->fetch()['count'];
    
    // Enquiries count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'new'");
    $pendingEnquiries = $stmt->fetch()['count'];
    
    // Counselling sessions count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM counselling_sessions WHERE status = 'pending'");
    $pendingSessions = $stmt->fetch()['count'];
    
    // Recent enquiries
    $stmt = $conn->query("SELECT e.*, u.name as university_name 
                         FROM enquiries e 
                         LEFT JOIN universities u ON e.university_id = u.id 
                         ORDER BY e.created_at DESC LIMIT 5");
    $recentEnquiries = $stmt->fetchAll();
    
    // Recent counselling sessions
    $stmt = $conn->query("SELECT * FROM counselling_sessions ORDER BY created_at DESC LIMIT 5");
    $recentSessions = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniHunt Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">U</span>
                    </div>
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">UniHunt Admin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Welcome, Admin</span>
                    <a href="logout.php" class="text-sm text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Universities</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $universitiesCount; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Blogs</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $blogsCount; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Enquiries</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $pendingEnquiries; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Sessions</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $pendingSessions; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="universities.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Manage Universities</p>
                            <p class="text-xs text-gray-500">Add, edit, or remove universities</p>
                        </div>
                    </a>

                    <a href="blogs.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Manage Blogs</p>
                            <p class="text-xs text-gray-500">Create and edit blog posts</p>
                        </div>
                    </a>

                    <a href="enquiries.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">View Enquiries</p>
                            <p class="text-xs text-gray-500">Manage user enquiries</p>
                        </div>
                    </a>

                    <a href="counselling.php" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Counselling Sessions</p>
                            <p class="text-xs text-gray-500">Manage counselling bookings</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Enquiries -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Enquiries</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentEnquiries)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentEnquiries as $enquiry): ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($enquiry['name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($enquiry['email']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($enquiry['type']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $enquiry['status'] === 'new' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo ucfirst($enquiry['status']); ?>
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($enquiry['created_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No recent enquiries</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Counselling Sessions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Counselling Sessions</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentSessions)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentSessions as $session): ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($session['name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($session['email']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($session['session_type']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $session['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo ucfirst($session['status']); ?>
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($session['created_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No recent counselling sessions</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

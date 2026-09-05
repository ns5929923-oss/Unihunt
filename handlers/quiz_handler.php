<?php
/**
 * Quiz Results Handler
 * Processes career guidance quiz submissions and stores results
 */

require_once 'config/config.php';
require_once 'config/database.php';

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

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get quiz data
    $answers = $_POST['answers'] ?? [];
    $sessionId = $_POST['session_id'] ?? uniqid('quiz_', true);
    
    if (empty($answers)) {
        throw new Exception('No quiz answers provided');
    }
    
    // Calculate scores for each stream
    $streams = ['Science (PCM)', 'Science (PCB)', 'Commerce', 'Arts/Humanities', 'Management', 'Computer/IT'];
    $scores = array_fill_keys($streams, 0);
    
    // Define scoring rules based on answers
    $scoringRules = [
        'Science (PCM)' => ['math', 'physics', 'chemistry', 'engineering', 'problem_solving'],
        'Science (PCB)' => ['biology', 'chemistry', 'medicine', 'healthcare', 'research'],
        'Commerce' => ['business', 'accounting', 'finance', 'economics', 'marketing'],
        'Arts/Humanities' => ['literature', 'history', 'philosophy', 'social_sciences', 'creative'],
        'Management' => ['leadership', 'business', 'entrepreneurship', 'management', 'strategy'],
        'Computer/IT' => ['technology', 'programming', 'computers', 'software', 'innovation']
    ];
    
    // Calculate scores based on answers
    foreach ($answers as $questionId => $answer) {
        $answerValue = intval($answer); // Convert to integer (-2 to 2)
        
        // Map question to relevant streams
        $questionMapping = [
            '1' => ['Science (PCM)', 'Computer/IT'], // Math/Physics problems
            '2' => ['Science (PCB)'], // Biology and healthcare
            '3' => ['Management', 'Commerce'], // Business/entrepreneurship
            '4' => ['Commerce'], // Accounting/finance
            '5' => ['Arts/Humanities'], // Reading/writing/social sciences
            '6' => ['Computer/IT', 'Science (PCM)'] // Coding/logic
        ];
        
        if (isset($questionMapping[$questionId])) {
            foreach ($questionMapping[$questionId] as $stream) {
                $scores[$stream] += $answerValue;
            }
        }
    }
    
    // Normalize scores to positive values
    foreach ($scores as $stream => $score) {
        $scores[$stream] = max(0, $score + 10); // Add 10 to make all positive
    }
    
    // Get recommended streams (top 2)
    arsort($scores);
    $recommendedStreams = array_slice(array_keys($scores), 0, 2);
    
    // Get university suggestions based on recommended streams
    $universitySuggestions = [];
    foreach ($recommendedStreams as $stream) {
        $sql = "SELECT id, name, city, rating, budget_k FROM universities 
                WHERE JSON_CONTAINS(streams, :stream) 
                ORDER BY rating DESC LIMIT 3";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':stream' => json_encode($stream)]);
        $universities = $stmt->fetchAll();
        
        $universitySuggestions[$stream] = $universities;
    }
    
    // Store quiz results
    $sql = "INSERT INTO quiz_results (session_id, answers, scores, recommended_streams, university_suggestions) 
            VALUES (:session_id, :answers, :scores, :recommended_streams, :university_suggestions)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':session_id' => $sessionId,
        ':answers' => json_encode($answers),
        ':scores' => json_encode($scores),
        ':recommended_streams' => json_encode($recommendedStreams),
        ':university_suggestions' => json_encode($universitySuggestions)
    ]);
    
    if ($result) {
        // Return success response with recommendations
        echo json_encode([
            'success' => true,
            'message' => 'Quiz completed successfully!',
            'session_id' => $sessionId,
            'data' => [
                'scores' => $scores,
                'recommended_streams' => $recommendedStreams,
                'university_suggestions' => $universitySuggestions,
                'top_stream' => $recommendedStreams[0] ?? null,
                'top_score' => $scores[$recommendedStreams[0]] ?? 0
            ]
        ]);
    } else {
        throw new Exception('Failed to save quiz results');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

<?php
// Start session and DB connection
session_start();
require_once "../php/db_connect.php";

// Get quiz ID from URL
$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

if ($quiz_id) {
    // Query to get rankings for the selected quiz
    $query = "SELECT s.username, s.roll_no, qr.total_score, qr.attempted_at
              FROM quiz_results qr
              JOIN student s ON qr.student_id = s.id
              WHERE qr.quiz_id = $quiz_id
              ORDER BY qr.total_score DESC";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Output CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="quiz_rankings.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write column headers
        fputcsv($output, ['Username', 'Roll No', 'Score', 'Attempted At']);
        
        // Write data rows
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }

        // Close the output stream
        fclose($output);
    } else {
        echo "No rankings found for this quiz.";
    }
} else {
    echo "Invalid quiz ID.";
}
?>

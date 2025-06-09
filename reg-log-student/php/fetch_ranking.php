<?php
// Start session and DB connection
session_start();
require_once "../php/db_connect.php";

// Check if quiz_id is set
if (isset($_POST['quiz_id'])) {
    $quiz_id = $_POST['quiz_id'];

    // Query to get rankings for the selected quiz
    $query = "
        SELECT s.username, s.roll_no, q.total_score 
        FROM quiz_results q
        JOIN student s ON q.student_id = s.id
        INNER JOIN (
            SELECT student_id, MAX(attempted_at) as last_attempt
            FROM quiz_results
            WHERE quiz_id = $quiz_id
            GROUP BY student_id
        ) latest ON latest.student_id = q.student_id AND latest.last_attempt = q.attempted_at
        WHERE q.quiz_id = $quiz_id
        ORDER BY q.total_score DESC
    ";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Start the table for rankings
        $output = '<table id="rankingTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Roll Number</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>';

        $rank = 1; // Initialize rank
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= '<tr>
                            <td>' . $rank++ . '</td>
                            <td>' . htmlspecialchars($row['username']) . '</td>
                            <td>' . $row['roll_no'] . '</td>
                            <td>' . $row['total_score'] . '</td>
                        </tr>';
        }

        // End the table
        $output .= '</tbody></table>';
    } else {
        $output = "<p>No results found for this quiz.</p>";
    }

    // Return the ranking data as HTML
    echo $output;
}
?>

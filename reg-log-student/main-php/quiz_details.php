<?php
session_start();
include '../php/db_connect.php'; // Adjust the path if needed

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../main-php/index.php");
    exit();
}

// Get student ID from session
$student_id = $_SESSION['student_id'];

// Get quiz_id from URL
if (!isset($_GET['quiz_id'])) {
    echo "Quiz ID is missing!";
    exit();
}
$quiz_id = intval($_GET['quiz_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Quiz Attempts</h2>

    <?php
    // Fetch all attempts for this quiz by the student
    $stmt = $conn->prepare("SELECT id, attempted_at, total_score FROM quiz_results WHERE student_id = ? AND quiz_id = ? ORDER BY attempted_at DESC");
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='attempt-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='attempt-box'>";
            echo "<p><strong>Attempted On:</strong> " . $row['attempted_at'] . "</p>";
            echo "<p><strong>Total Score:</strong> " . $row['total_score'] . "</p>";
            echo "<a href='quiz_details.php?quiz_id={$quiz_id}&result_id={$row['id']}' class='btn'>View Answers</a>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>No attempts found for this quiz.</p>";
    }
    ?>

    <?php
    // If a particular attempt is selected
    if (isset($_GET['result_id'])) {
        $result_id = intval($_GET['result_id']);

        // Fetch student's answers for the selected attempt
        $stmt = $conn->prepare("SELECT qa.student_answer, qa.is_correct, q.question_text
                                FROM quiz_answers qa
                                JOIN questions q ON qa.question_id = q.id
                                WHERE qa.result_id = ?");
        $stmt->bind_param("i", $result_id);
        $stmt->execute();
        $answer_result = $stmt->get_result();

        if ($answer_result->num_rows > 0) {
            echo "<h2>Answers for Attempt ID: $result_id</h2>";
            echo "<div class='answers-list'>";
            while ($answer = $answer_result->fetch_assoc()) {
                echo "<div class='answer-box'>";
                echo "<p><strong>Question:</strong> " . htmlspecialchars($answer['question_text']) . "</p>";
                echo "<p><strong>Your Answer:</strong> " . htmlspecialchars($answer['student_answer']) . "</p>";
                echo "<p><strong>Status:</strong> " . ($answer['is_correct'] ? "<span style='color:green;'>Correct</span>" : "<span style='color:red;'>Wrong</span>") . "</p>";
                echo "</div><hr>";
            }
            echo "</div>";
        } else {
            echo "<p>No answers found for this attempt.</p>";
        }
    }
    ?>
</div>

</body>
</html>

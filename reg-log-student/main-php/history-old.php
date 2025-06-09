<?php
session_start();
include '../php/db_connect.php'; // Your DB connection

// Check if student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../main-php/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch quizzes the student has attempted
$query = "
    SELECT 
        q.id AS quiz_id, 
        q.title, 
        q.marks, 
        q.duration, 
        COUNT(qr.id) AS attempt_count
    FROM 
        quiz_results qr
    JOIN 
        quizzes q ON qr.quiz_id = q.id
    WHERE 
        qr.student_id = ?
    GROUP BY 
        qr.quiz_id
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usxer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz History</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Your CSS -->
    <style>
        .quiz-box {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 15px;
            border-radius: 10px;
            background: #f9f9f9;
        }
        .quiz-title {
            font-size: 1.4rem;
            font-weight: bold;
        }
        .quiz-details {
            margin-top: 10px;
        }
        .view-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Past Quizzes Attempted</h1>

<div class="quiz-container">
    <?php
    if ($result->num_rows > 0) {
        while ($quiz = $result->fetch_assoc()) {
            echo '<div class="quiz-box">';
            echo '<div class="quiz-title">' . htmlspecialchars($quiz['title']) . '</div>';
            echo '<div class="quiz-details">';
            echo 'Marks: ' . $quiz['marks'] . '<br>';
            echo 'Duration: ' . $quiz['duration'] . ' minutes<br>';
            echo 'Attempts: ' . $quiz['attempt_count'];
            echo '</div>';
            echo '<a href="quiz_details.php?quiz_id=' . $quiz['quiz_id'] . '" class="view-btn">View Attempts</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No quizzes attempted yet.</p>";
    }
    ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

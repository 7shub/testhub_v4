<?php
session_start();
include '../php/db_connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quiz_id'])) {
    $quiz_id = intval($_POST['quiz_id']); // Sanitize input

    // Delete questions related to the quiz first
    $delete_questions = "DELETE FROM questions WHERE quiz_id = ?";
    $stmt1 = $conn->prepare($delete_questions);
    $stmt1->bind_param("i", $quiz_id);
    $stmt1->execute();
    $stmt1->close();

    // Delete the quiz
    $delete_quiz = "DELETE FROM quizzes WHERE id = ?";
    $stmt2 = $conn->prepare($delete_quiz);
    $stmt2->bind_param("i", $quiz_id);
    
    if ($stmt2->execute()) {
        $stmt2->close();
        header("Location: ../main-php/dashbord.php?delete_success=1"); // Redirect after deletion
        exit();
    } else {
        echo "<script>alert('Error deleting quiz!'); window.history.back();</script>";
    }
}
?>

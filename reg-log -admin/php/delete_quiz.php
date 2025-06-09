<?php
session_start();
require_once '../php/db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['quiz_id'])) {
    $quiz_id = intval($_POST['quiz_id']); // Sanitize

    // First delete questions related to this quiz
    $deleteQuestions = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $deleteQuestions->bind_param("i", $quiz_id);
    $deleteQuestions->execute();
    $deleteQuestions->close();

    // Then delete the quiz itself
    $deleteQuiz = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $deleteQuiz->bind_param("i", $quiz_id);
    
    if ($deleteQuiz->execute()) {
        $deleteQuiz->close();
        header("Location: ../admin/dashbord.php?delete_success=1");
        exit();
    } else {
        echo "<script>alert('Error deleting quiz'); window.history.back();</script>";
    }
}
?>

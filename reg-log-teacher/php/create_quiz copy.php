
<?php
session_start();
include 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $marks = intval($_POST['marks']);
    $duration = intval($_POST['duration']);

    if (!empty($title) && $marks > 0 && $duration > 0) {
        $query = "INSERT INTO quizzes (title, marks, duration) VALUES ('$title', '$marks', '$duration')";
        if (mysqli_query($conn, $query)) {
            $quiz_id = mysqli_insert_id($conn); // Get the newly created quiz ID
            header("Location: ../main-php/question.php?quiz_id=$quiz_id"); // Redirect to add questions
            exit();
        } else {
            echo "<script>alert('Error creating quiz. Try again!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
    }
}
?>
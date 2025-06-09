<?php
require_once "../php/db_connect.php"; // Database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['quiz_id']) || empty($_POST['quiz_id']) || !isset($_POST['student_id']) || empty($_POST['student_id'])) {
        die("Invalid Quiz or Student ID");
    }

    $quiz_id = intval($_POST['quiz_id']);
    $student_id = intval($_POST['student_id']);
    $user_answers = isset($_POST['answer']) ? $_POST['answer'] : [];
    $total_score = 0;

    // Insert into quiz_results table
    $insert_result_query = "INSERT INTO quiz_results (student_id, quiz_id, total_score) VALUES ($student_id, $quiz_id, 0)";
    mysqli_query($conn, $insert_result_query);
    $result_id = mysqli_insert_id($conn); // Get last inserted ID

    // Fetch all questions and check answers
    $question_query = "SELECT id, correct_option, points, option1, option2, option3, option4 FROM questions WHERE quiz_id = $quiz_id";
    $question_result = mysqli_query($conn, $question_query);

    while ($row = mysqli_fetch_assoc($question_result)) {
        $question_id = $row['id'];
        $correct_option_index = $row['correct_option']; // 1-4
        $correct_answer = $row['option' . $correct_option_index]; // Fetch correct answer
        $student_answer = isset($user_answers[$question_id]) ? $user_answers[$question_id] : null;
        $is_correct = ($student_answer === $correct_answer) ? 1 : 0;

        if ($is_correct) {
            $total_score += $row['points'];
        }

        // Insert into quiz_answers table
        $insert_answer_query = "INSERT INTO quiz_answers (result_id, question_id, student_answer, is_correct)
                                    VALUES ($result_id, $question_id, '" . mysqli_real_escape_string($conn, $student_answer) . "', $is_correct)";
        mysqli_query($conn, $insert_answer_query);
    }

    // Update total score in quiz_results table
    $update_score_query = "UPDATE quiz_results SET total_score = $total_score WHERE id = $result_id";
    mysqli_query($conn, $update_score_query);

    // Redirect to results page
    header("Location: ../main-php/quiz_result.php?result_id=$result_id");
    exit();
} else {
    die("Invalid Request");
}
?>
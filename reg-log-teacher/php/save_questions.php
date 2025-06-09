<?php
require '../php/db_connect.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['quiz_id']) || empty($_POST['quiz_id'])) {
        die("Error: Quiz ID is missing.");
    }

    $quiz_id = $_POST['quiz_id'];
    
    // Check if quiz_id exists in the quizzes table
    $check_query = "SELECT `id` FROM `quizzes` WHERE id = '$quiz_id'";
    $stmt = $conn->prepare($check_query);
    //$stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0) {
        echo"$quiz_id";
        die("Error: Quiz ID does not exist in the database.");
    }
    
    $stmt->close();
    
    // Ensure the questions array is present
    if (!isset($_POST['question']) || !is_array($_POST['question'])) {
        die("Error: No questions provided.");
    }
    
    foreach ($_POST['question'] as $index => $question) {
        if (empty($question) || empty($_POST['points'][$index]) || empty($_POST['option1'][$index]) ||
            empty($_POST['option2'][$index]) || empty($_POST['option3'][$index]) || empty($_POST['option4'][$index]) ||
            !isset($_POST['correct_option'][$index])) {
            continue; // Skip if any required field is missing
        }

        $points = $_POST['points'][$index];
        $option1 = $_POST['option1'][$index];
        $option2 = $_POST['option2'][$index];
        $option3 = $_POST['option3'][$index];
        $option4 = $_POST['option4'][$index];
        $correct_option = $_POST['correct_option'][$index];
        
        $sql = "INSERT INTO questions (quiz_id, question, points, option1, option2, option3, option4, correct_option)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isissssi", $quiz_id, $question, $points, $option1, $option2, $option3, $option4, $correct_option);
            if (!$stmt->execute()) {
                die("Error executing query: " . $stmt->error);
            }
            $stmt->close();
        } else {
            die("Error preparing statement: " . $conn->error);
        }
    }
    
    // Redirect after successful insertion
    header("Location: ../main-php/dashbord.php?success=1");
    exit();
} else {
    die("Invalid request");
}
?>

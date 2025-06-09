<?php
session_start();
require_once "../php/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../main-php/index.php"); // Redirect if not logged in
    exit();
}

$student_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO feedback (student_id, rating, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $student_id, $rating, $comment);

    if ($stmt->execute()) {
        $_SESSION['feedback_success'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedback</title>
    <link rel="stylesheet" href="../assets/css/feedback.css" />
</head>
<body>
    <div class="feedback-card">
        <h2>How was the quiz?</h2>
        <form action="" method="POST">
            <div class="rating-container">
                <button type="button" class="rating-emoji" data-rating="1">😠</button>
                <button type="button" class="rating-emoji" data-rating="2">😕</button>
                <button type="button" class="rating-emoji" data-rating="3">😐</button>
                <button type="button" class="rating-emoji" data-rating="4">😀</button>
                <button type="button" class="rating-emoji" data-rating="5">🤩</button>
                <input type="hidden" name="rating" id="ratingInput" value="0">
            </div>
            
            <div class="box">
                <textarea id="textInput" name="comment" placeholder="Share your thoughts..." maxlength="100"></textarea>
                <div class="word-counter" id="wordCount">0/100</div>
            </div>

            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
    </div>

    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <h3>Thank You! 🎉</h3>
            <p>Your feedback helps us improve!</p>
            <a href="dashboard.php" class="submit-btn">Close</a>
        </div>
    </div>

    <script>
        const textArea = document.getElementById('textInput');
        const wordCountDisplay = document.getElementById('wordCount');
        const ratingButtons = document.querySelectorAll('.rating-emoji');
        const ratingInput = document.getElementById('ratingInput');
        const popup = document.getElementById('popup');

        // Update word count in real-time
        textArea.addEventListener('input', () => {
            let words = textArea.value.trim().split(/\s+/).filter(word => word.length > 0);
            wordCountDisplay.textContent = words.length + "/100"; 
        });

        // Handle rating button clicks
        ratingButtons.forEach(button => {
            button.addEventListener('click', () => {
                ratingInput.value = button.dataset.rating;
            });
        });
    </script>
</body>
</html>
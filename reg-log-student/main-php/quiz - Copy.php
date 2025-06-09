<?php
// Start session
session_start();
require_once "../php/db_connect.php"; // Database connection

// Check if user is logged in and quiz ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['student_id']) || empty($_GET['student_id'])) {
    die("Invalid Request");
}

$quiz_id = intval($_GET['id']);
$student_id = intval($_GET['student_id']);

// Fetch quiz details
$quiz_query = "SELECT * FROM quizzes WHERE id = $quiz_id";
$quiz_result = mysqli_query($conn, $quiz_query);
$quiz = mysqli_fetch_assoc($quiz_result);

if (!$quiz) {
    die("Quiz not found");
}

// Fetch questions for the quiz
$question_query = "SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC";
$question_result = mysqli_query($conn, $question_query);
$questions = mysqli_fetch_all($question_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?> - TestHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background-color: #f8f9fa;
        }

        .quiz-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-container {
            margin-top: 20px;
        }

        .submit-btn {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
        }

        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="quiz-container">
        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
        <p><strong>Total Marks:</strong> <?php echo $quiz['marks']; ?></p>
        <p><strong>Time Limit:</strong> <?php echo $quiz['duration']; ?> min</p>
        <div id="countdown" style="font-size:1.2em; color:#d32f2f; margin-bottom:15px;"></div>

        <form id="quizForm" action="../php/submit_quiz.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            
            <?php if (!empty($questions)) {
                foreach ($questions as $index => $question) { ?>
                    <div class="question">
                        <p><strong><?php echo ($index + 1) . ". " . htmlspecialchars($question['question']); ?></strong></p>
                        <label><input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option1']); ?>"> <?php echo htmlspecialchars($question['option1']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option2']); ?>"> <?php echo htmlspecialchars($question['option2']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option3']); ?>"> <?php echo htmlspecialchars($question['option3']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option4']); ?>"> <?php echo htmlspecialchars($question['option4']); ?></label>
                    </div>
            <?php } } ?>

            <div class="btn-container">
                <button class="submit-btn" type="submit">Submit Quiz</button>
            </div>
        </form>
        <br />
        <button onclick="openCompiler()">Open Code Compiler</button>
    </div>

    <!-- Compiler Iframe -->
    <div class="iframe-container" id="compilerFrame" style="position:fixed;bottom:10px;right:10px;width:800px;height:500px;display:none;background:white;border:2px solid #ccc;z-index:1000;border-radius:5px;box-shadow:0px 4px 8px rgba(0,0,0,0.2);">
        <button class="close-btn" onclick="closeCompiler()" style="position:absolute;top:5px;right:10px;background:red;color:white;padding:5px 10px;cursor:pointer;border-radius:3px;border:none;">X</button>
        <iframe src="https://www.jdoodle.com/online-compiler" id="compiler" style="width:100%;height:100%;border:none;"></iframe>
    </div>

    <script>
        // Auto-submit after time limit
        const timeLimitMin = <?php echo (int)$quiz['duration']; ?>;
        const timeLimitMs = timeLimitMin * 60 * 1000;
        let remaining = timeLimitMin * 60; // seconds
        const countdownEl = document.getElementById('countdown');
        function updateCountdown() {
            const min = Math.floor(remaining / 60);
            const sec = remaining % 60;
            countdownEl.textContent = `Time Remaining: ${min}:${sec.toString().padStart(2, '0')}`;
            if (remaining > 0) {
                remaining--;
                setTimeout(updateCountdown, 1000);
            }
        }
        updateCountdown();
        setTimeout(function() {
            alert("Time's up! Your quiz is being submitted.");
            document.getElementById('quizForm').submit();
        }, timeLimitMs);

        // Code Compiler logic
        function openCompiler() {
            document.getElementById('compilerFrame').style.display = 'block';
        }
        function closeCompiler() {
            document.getElementById('compilerFrame').style.display = 'none';
        }
    </script>

</body>
</html>

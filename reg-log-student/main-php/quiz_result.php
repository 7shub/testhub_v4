<?php
session_start();
require_once "../php/db_connect.php"; // Database connection

if (!isset($_GET['result_id'])) {
    die("Invalid Result ID");
}

$result_id = intval($_GET['result_id']);

// Fetch quiz result
$result_query = "SELECT qr.total_score, q.title  
                    FROM quiz_results qr
                    JOIN quizzes q ON qr.quiz_id = q.id
                    WHERE qr.id = ?";
$stmt = mysqli_prepare($conn, $result_query);
mysqli_stmt_bind_param($stmt, "i", $result_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$quiz_result = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$quiz_result) {
    die("Quiz result not found.");
}

// Fetch student answers
$answers_query = "SELECT qa.question_id, qa.student_answer, qa.is_correct, q.question, 
                                    q.option1, q.option2, q.option3, q.option4, q.correct_option
                 FROM quiz_answers qa
                 JOIN questions q ON qa.question_id = q.id
                 WHERE qa.result_id = ?";
$stmt = mysqli_prepare($conn, $answers_query);
mysqli_stmt_bind_param($stmt, "i", $result_id);
mysqli_stmt_execute($stmt);
$answers_result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - TestHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #06b6d4;
            --success: #22c55e;
            --danger: #ef4444;
            --bg: #f4f6fb;
            --card-bg: #fff;
            --text: #22223b;
            --muted: #6c757d;
            --border: #e0e7ff;
            --dark-bg: #1a1a2e;
            --dark-card: #16213e;
            --dark-text: #e6e6e6;
            --dark-border: #2a2a3a;
        }
        [data-theme="dark"] {
            --bg: var(--dark-bg);
            --card-bg: var(--dark-card);
            --text: var(--dark-text);
            --muted: #a0a0a0;
            --border: var(--dark-border);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
        }
        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border: 1px solid var(--border);
        }
        .quiz-container h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        .quiz-container p {
            color: var(--muted);
            margin-bottom: 10px;
        }
        .score {
            font-size: 1.2em;
            color: var(--success);
            margin-bottom: 20px;
        }
        .breakdown-list {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
        }
        .breakdown-item {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 18px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: box-shadow 0.2s;
            text-align: left;
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }
        .breakdown-item .icon {
            font-size: 1.7em;
            margin-top: 2px;
        }
        .breakdown-item.correct .icon {
            color: var(--success);
        }
        .breakdown-item.incorrect .icon {
            color: var(--danger);
        }
        .breakdown-content {
            flex: 1;
        }
        .breakdown-content p {
            margin-bottom: 6px;
            color: var(--text);
        }
        .breakdown-content p:last-child {
            margin-bottom: 0;
        }
        .btn-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .feedback-button {
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: background 0.2s, transform 0.2s;
        }
        .feedback-button:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .quiz-container {
                padding: 15px;
            }
            .breakdown-item {
                flex-direction: column;
                gap: 8px;
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    <button id="themeToggle" style="position:fixed;top:24px;right:24px;z-index:2000;background:var(--card-bg);color:var(--primary);border:1px solid var(--border);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.07);cursor:pointer;font-size:1.3em;transition:background 0.2s, color 0.2s;">
        <i id="themeIcon" class="fas fa-moon"></i>
    </button>
    <div class="quiz-container">
        <h2><?php echo htmlspecialchars($quiz_result['title']); ?> - Quiz Results</h2>
        <div class="score"><i class="fas fa-trophy"></i> Your Total Score: <?php echo $quiz_result['total_score']; ?></div>
        <h3 style="margin-bottom:18px; color:var(--secondary);">Question-wise Breakdown</h3>
        <ul class="breakdown-list">
            <?php while ($row = mysqli_fetch_assoc($answers_result)) {
                $correct_answer = $row['option' . $row['correct_option']];
                $is_correct = $row['is_correct'] ? "correct" : "incorrect";
            ?>
                <li class="breakdown-item <?php echo $is_correct; ?>">
                    <span class="icon">
                        <?php if ($row['is_correct']) { ?>
                            <i class="fas fa-check-circle"></i>
                        <?php } else { ?>
                            <i class="fas fa-times-circle"></i>
                        <?php } ?>
                    </span>
                    <div class="breakdown-content">
                        <p><strong>Q:</strong> <?php echo htmlspecialchars($row['question']); ?></p>
                        <p><strong>Your Answer:</strong> <?php echo htmlspecialchars($row['student_answer']); ?></p>
                        <p><strong>Correct Answer:</strong> <?php echo htmlspecialchars($correct_answer); ?></p>
                        <p style="color:<?php echo $row['is_correct'] ? 'var(--success)' : 'var(--danger)'; ?>;font-weight:500;">
                            <?php echo $row['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                        </p>
                    </div>
                </li>
            <?php } ?>
        </ul>
        <div class="btn-container">
            <a href="feedback.php" class="feedback-button"><i class="fas fa-comment-dots"></i> Give Feedback</a>
        </div>
    </div>
    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        function setTheme(mode) {
            if (mode === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
            } else {
                document.body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-moon';
            }
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) setTheme(savedTheme);

        themeToggle.addEventListener('click', function() {
            const isDark = document.body.getAttribute('data-theme') === 'dark';
            setTheme(isDark ? 'light' : 'dark');
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
        });

        // Disable browser back/forward buttons
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>

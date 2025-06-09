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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #06b6d4;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
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
        .quiz-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }
        .quiz-header h2 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 2em;
        }
        .quiz-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }
        .info-item i {
            color: var(--primary);
        }
        .countdown {
            background: var(--danger);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .question {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .question:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .question-number {
            color: var(--primary);
            font-weight: bold;
            margin-bottom: 10px;
        }
        .question-text {
            font-size: 1.1em;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .options {
            display: grid;
            gap: 12px;
        }
        .option {
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .option input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        .checkmark {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 20px;
            background: var(--card-bg);
            border: 2px solid var(--border);
            border-radius: 50%;
            transition: all 0.2s;
        }
        .option:hover .checkmark {
            border-color: var(--primary);
        }
        .option input[type="radio"]:checked ~ .checkmark {
            background: var(--primary);
            border-color: var(--primary);
        }
        .option input[type="radio"]:checked ~ .checkmark:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: var(--secondary);
            color: white;
        }
        .btn-secondary:hover {
            background: #0891b2;
            transform: translateY(-2px);
        }
        .compiler-frame {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 800px;
            height: 500px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
            overflow: hidden;
        }
        .compiler-header {
            padding: 10px 15px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .close-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2em;
            padding: 5px;
        }
        .compiler-iframe {
            width: 100%;
            height: calc(100% - 40px);
            border: none;
        }
        .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .progress {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .quiz-container {
                padding: 20px;
            }
            .quiz-info {
                flex-direction: column;
                gap: 10px;
            }
            .compiler-frame {
                width: 100%;
                height: 400px;
                bottom: 0;
                right: 0;
                border-radius: 12px 12px 0 0;
            }
        }
    </style>
</head>
<body>
    <button id="themeToggle" style="position:fixed;top:24px;right:24px;z-index:2000;background:var(--card-bg);color:var(--primary);border:1px solid var(--border);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.07);cursor:pointer;font-size:1.3em;transition:background 0.2s, color 0.2s;">
        <i id="themeIcon" class="fas fa-moon"></i>
    </button>
    <button id="fullscreenBtn" class="btn btn-primary" style="display:block; margin: 40px auto 0 auto;"> <i class="fas fa-expand"></i> Start Quiz in Fullscreen</button>
    <div class="quiz-container" id="quizContainer" style="display:none;">
        <div class="quiz-header">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <div class="quiz-info">
                <div class="info-item">
                    <i class="fas fa-star"></i>
                    <span>Total Marks: <?php echo $quiz['marks']; ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>Time Limit: <?php echo $quiz['duration']; ?> min</span>
                </div>
            </div>
            <div class="countdown" id="countdown">
                <i class="fas fa-hourglass-half"></i>
                <span>Time Remaining: 00:00</span>
            </div>
            <div class="progress-bar">
                <div class="progress" id="progress"></div>
            </div>
        </div>

        <form id="quizForm" action="../php/submit_quiz.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            
            <?php if (!empty($questions)) {
                foreach ($questions as $index => $question) { ?>
                    <div class="question">
                        <div class="question-number">Question <?php echo ($index + 1); ?></div>
                        <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                        <div class="options">
                            <label class="option">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option1']); ?>">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($question['option1']); ?>
                            </label>
                            <label class="option">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option2']); ?>">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($question['option2']); ?>
                            </label>
                            <label class="option">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option3']); ?>">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($question['option3']); ?>
                            </label>
                            <label class="option">
                                <input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($question['option4']); ?>">
                                <span class="checkmark"></span>
                                <?php echo htmlspecialchars($question['option4']); ?>
                            </label>
                        </div>
                    </div>
            <?php } } ?>

            <div class="btn-container">
                <button class="btn btn-secondary" type="button" onclick="openCompiler()">
                    <i class="fas fa-code"></i> Open Compiler
                </button>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-paper-plane"></i> Submit Quiz
                </button>
            </div>
        </form>
    </div>

    <div class="compiler-frame" id="compilerFrame">
        <div class="compiler-header">
            <span>Code Compiler</span>
            <button class="close-btn" onclick="closeCompiler()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <iframe src="https://www.jdoodle.com/online-compiler" class="compiler-iframe" id="compiler"></iframe>
    </div>

    <script>
        // Auto-submit after time limit
        const timeLimitMin = <?php echo (int)$quiz['duration']; ?>;
        const timeLimitMs = timeLimitMin * 60 * 1000;
        let remaining = timeLimitMin * 60; // seconds
        const countdownEl = document.getElementById('countdown');
        const progressEl = document.getElementById('progress');

        function updateCountdown() {
            const min = Math.floor(remaining / 60);
            const sec = remaining % 60;
            countdownEl.innerHTML = `<i class="fas fa-hourglass-half"></i> Time Remaining: ${min}:${sec.toString().padStart(2, '0')}`;
            
            // Update progress bar
            const progress = (remaining / (timeLimitMin * 60)) * 100;
            progressEl.style.width = `${progress}%`;
            
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

        // Hide quiz until fullscreen is entered
        const quizContainer = document.getElementById('quizContainer');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        quizContainer.style.display = 'none';
        fullscreenBtn.style.display = 'block';

        function launchFullscreen(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        }
        function isFullscreen() {
            return document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
        }
        function showQuizIfFullscreen() {
            if (isFullscreen()) {
                quizContainer.style.display = 'block';
                fullscreenBtn.style.display = 'none';
            }
        }
        fullscreenBtn.addEventListener('click', function() {
            launchFullscreen(document.documentElement);
            setTimeout(showQuizIfFullscreen, 300);
        });
        document.addEventListener('fullscreenchange', showQuizIfFullscreen);
        document.addEventListener('webkitfullscreenchange', showQuizIfFullscreen);
        document.addEventListener('mozfullscreenchange', showQuizIfFullscreen);
        document.addEventListener('MSFullscreenChange', showQuizIfFullscreen);

        // Prevent quiz from being shown if not in fullscreen
        showQuizIfFullscreen();

        function handleFullscreenChange() {
            if (!isFullscreen() && quizContainer.style.display === 'block') {
                alert('You should not act smart');
                document.getElementById('quizForm').submit();
            }
        }
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);

        // Detect tab switch/minimize and auto-submit on return
        let leftPage = false;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                leftPage = true;
            } else if (leftPage) {
                alert('I am smarter than you. The quiz is auto-submitting.');
                document.getElementById('quizForm').submit();
            }
        });

        // Dark/Light mode toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        function setTheme(mode) {
            if (mode === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                document.body.removeAttribute('data-theme');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
        // Load theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) setTheme(savedTheme);
        // Toggle theme on button click
        themeToggle.addEventListener('click', function() {
            const isDark = document.body.getAttribute('data-theme') === 'dark';
            setTheme(isDark ? 'light' : 'dark');
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
        });
    </script>
</body>
</html>

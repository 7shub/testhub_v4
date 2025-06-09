<?php
require '../php/db_connect.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Hub - Add Question</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        }
        [data-theme="dark"] {
            --bg: var(--dark-bg);
            --card-bg: var(--dark-card);
            --text: var(--dark-text);
            --muted: #a0a0a0;
            --border: #2a2a3a;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }
        .hero {
            background: linear-gradient(90deg, var(--primary) 60%, var(--secondary) 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(79,70,229,0.1);
        }
        .hero h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        [data-theme="dark"] .navbar {
            background: rgba(26,26,46,0.9);
        }
        .navbar .title {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }
        .navbar .nav-links a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .navbar .nav-links a:hover {
            color: var(--primary);
        }
        .content {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .form-container {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(79,70,229,0.08);
            border: 1px solid var(--border);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            font-size: 1.8em;
        }
        .question-block {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--border);
            margin-bottom: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .question-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79,70,229,0.1);
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group label {
            position: absolute;
            left: 15px;
            top: -10px;
            background: var(--card-bg);
            padding: 0 5px;
            font-size: 0.9em;
            color: var(--primary);
            font-weight: 500;
        }
        .form-group textarea,
        .form-group input[type="number"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            background: var(--card-bg);
            color: var(--text);
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }
        .form-group textarea:focus,
        .form-group input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
        }
        .options {
            background: var(--bg);
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
        .option-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            background: var(--card-bg);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .option-item textarea {
            flex: 1;
            min-height: 60px;
            resize: vertical;
        }
        .option-item input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        .add-question-button {
            width: 100%;
            background: var(--secondary);
            color: white;
            margin-top: 20px;
        }
        .add-question-button:hover {
            background: #0891b2;
        }
        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 100;
        }
        .theme-toggle:hover {
            transform: scale(1.1);
        }
        @media (max-width: 768px) {
            .content {
                margin-top: 20px;
                padding: 0 15px;
            }
            .form-container {
                padding: 20px;
            }
            .hero {
                padding: 30px 15px;
                border-radius: 30px;
            }
            .hero h1 {
                font-size: 1.8em;
            }
            .option-item {
                flex-direction: column;
                gap: 10px;
            }
            .option-item textarea {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Create New Questions</h1>
        <p>Add multiple choice questions to your quiz</p>
    </div>

    <div class="content">
        <div class="form-container">
            <h2><i class="fas fa-plus-circle"></i> New Question</h2>
            
            <form action="../php/save_questions.php" method="POST">
                <input type="hidden" name="quiz_id" value='<?php $quiz_id = $_GET['quiz_id']; echo"$quiz_id"; ?>'>
                
                <div id="questions-container">
                    <div class="question-block">
                        <div class="form-group">
                            <label>Question</label>
                            <textarea name="question[]" rows="4" placeholder="Enter your question" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Question Points</label>
                            <input type="number" name="points[]" placeholder="Enter Marks" required>
                        </div>

                        <div class="options">
                            <div class="option-item">
                                <textarea name="option1[]" rows="2" placeholder="Option 1" required></textarea>
                                <input type="radio" name="correct_option[0]" value="1" required>
                            </div>
                            <div class="option-item">
                                <textarea name="option2[]" rows="2" placeholder="Option 2" required></textarea>
                                <input type="radio" name="correct_option[0]" value="2" required>
                            </div>
                            <div class="option-item">
                                <textarea name="option3[]" rows="2" placeholder="Option 3" required></textarea>
                                <input type="radio" name="correct_option[0]" value="3" required>
                            </div>
                            <div class="option-item">
                                <textarea name="option4[]" rows="2" placeholder="Option 4" required></textarea>
                                <input type="radio" name="correct_option[0]" value="4" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn add-question-button" onclick="addQuestion()">
                    <i class="fas fa-plus"></i> Add Another Question
                </button>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <button type="reset" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">
        <i class="fas fa-moon"></i>
    </button>

    <script>
        let questionCount = 1;

        function addQuestion() {
            let container = document.getElementById("questions-container");
            let newQuestion = document.createElement("div");
            newQuestion.classList.add("question-block");

            newQuestion.innerHTML = `
                <div class="form-group">
                    <label>Question</label>
                    <textarea name="question[]" rows="4" placeholder="Enter your question" required></textarea>
                </div>

                <div class="form-group">
                    <label>Question Points</label>
                    <input type="number" name="points[]" placeholder="Enter Marks" required>
                </div>

                <div class="options">
                    <div class="option-item">
                        <textarea name="option1[]" rows="2" placeholder="Option 1" required></textarea>
                        <input type="radio" name="correct_option[${questionCount}]" value="1" required>
                    </div>
                    <div class="option-item">
                        <textarea name="option2[]" rows="2" placeholder="Option 2" required></textarea>
                        <input type="radio" name="correct_option[${questionCount}]" value="2" required>
                    </div>
                    <div class="option-item">
                        <textarea name="option3[]" rows="2" placeholder="Option 3" required></textarea>
                        <input type="radio" name="correct_option[${questionCount}]" value="3" required>
                    </div>
                    <div class="option-item">
                        <textarea name="option4[]" rows="2" placeholder="Option 4" required></textarea>
                        <input type="radio" name="correct_option[${questionCount}]" value="4" required>
                    </div>
                </div>
            `;

            container.appendChild(newQuestion);
            questionCount++;
        }

        // Dark/Light mode toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        
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

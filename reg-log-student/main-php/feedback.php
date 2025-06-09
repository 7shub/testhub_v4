<?php
session_start();
require_once "../php/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../main-php/index.php");
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
    } else {
        $_SESSION['feedback_error'] = "Error: " . $stmt->error;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #06b6d4;
            --success: #22c55e;
            --danger: #ef4444;
            --bg: #f4f6fb;
            --card-bg: rgba(255,255,255,0.7);
            --glass-blur: blur(12px);
            --text: #22223b;
            --muted: #6c757d;
            --border: #e0e7ff;
            --dark-bg: #1a1a2e;
            --dark-card: rgba(22,33,62,0.7);
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
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s, color 0.3s;
            position: relative;
        }
        .glass-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(120deg, var(--primary) 0%, var(--secondary) 100%);
            filter: var(--glass-blur);
            z-index: 0;
        }
        .feedback-card {
            position: relative;
            z-index: 1;
            max-width: 400px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(79,70,229,0.13);
            padding: 38px 32px 28px 32px;
            border: 1.5px solid var(--border);
            text-align: center;
            backdrop-filter: var(--glass-blur);
        }
        .feedback-card h2 {
            color: var(--primary);
            margin-bottom: 30px;
            font-size: 1.5em;
            font-weight: 700;
        }
        .emoji-slider {
            width: 100%;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .emoji-track {
            display: flex;
            justify-content: space-between;
            width: 90%;
            margin-bottom: 10px;
        }
        .emoji {
            font-size: 2.1em;
            opacity: 0.5;
            transition: transform 0.2s, opacity 0.2s;
            user-select: none;
        }
        .emoji.selected {
            transform: scale(1.5);
            opacity: 1;
        }
        .slider {
            width: 90%;
            margin: 0 auto;
            accent-color: var(--primary);
        }
        .floating-label {
            position: relative;
            margin-bottom: 22px;
        }
        .floating-label textarea {
            width: 100%;
            min-height: 80px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            padding: 18px 14px 10px 14px;
            font-size: 1em;
            background: var(--card-bg);
            color: var(--text);
            transition: border 0.2s;
            resize: vertical;
        }
        .floating-label textarea:focus {
            border: 1.5px solid var(--primary);
            outline: none;
        }
        .floating-label label {
            position: absolute;
            left: 16px;
            top: 16px;
            color: var(--muted);
            font-size: 1em;
            pointer-events: none;
            background: transparent;
            transition: 0.2s;
        }
        .floating-label textarea:focus + label,
        .floating-label textarea:not(:placeholder-shown) + label {
            top: 2px;
            left: 12px;
            font-size: 0.85em;
            color: var(--primary);
            background: var(--card-bg);
            padding: 0 4px;
        }
        .word-counter {
            text-align: right;
            font-size: 0.95em;
            color: var(--muted);
            margin-bottom: 10px;
        }
        .submit-btn {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-weight: 500;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: background 0.2s, transform 0.2s;
        }
        .submit-btn:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.75);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .popup-overlay.show {
            display: flex;
        }
        .popup-content {
            background: var(--card-bg);
            color: var(--text);
            border-radius: 16px;
            padding: 36px 32px 28px 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.13);
            text-align: center;
            border: 1px solid var(--border);
            position: relative;
        }
        .checkmark {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-block;
            border: 4px solid var(--success);
            position: relative;
            margin-bottom: 18px;
            animation: pop 0.5s;
        }
        .checkmark:after {
            content: '';
            position: absolute;
            left: 16px;
            top: 24px;
            width: 16px;
            height: 28px;
            border-right: 4px solid var(--success);
            border-bottom: 4px solid var(--success);
            transform: rotate(45deg);
        }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .popup-content h3 {
            color: var(--success);
            margin-bottom: 10px;
        }
        .popup-content p {
            color: var(--muted);
            margin-bottom: 18px;
        }
        .theme-toggle-min {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 2000;
            background: none;
            color: var(--primary);
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3em;
            cursor: pointer;
            transition: color 0.2s;
        }
        .theme-toggle-min:focus {
            outline: 2px solid var(--primary);
        }
        @media (max-width: 600px) {
            .feedback-card, .popup-content {
                padding: 18px 8px 16px 8px;
            }
            .theme-toggle-min {
                top: 10px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    <button id="themeToggle" class="theme-toggle-min" title="Toggle dark/light mode">
        <i id="themeIcon" class="fas fa-moon"></i>
    </button>
    <div class="feedback-card">
        <h2>How was the quiz?</h2>
        <form id="feedbackForm" method="POST" autocomplete="off">
            <div class="emoji-slider">
                <div class="emoji-track">
                    <span class="emoji" data-value="1">😠</span>
                    <span class="emoji" data-value="2">😕</span>
                    <span class="emoji" data-value="3">😐</span>
                    <span class="emoji" data-value="4">😀</span>
                    <span class="emoji" data-value="5">🤩</span>
                </div>
                <input type="range" min="1" max="5" value="3" class="slider" id="emojiRange" name="rating">
            </div>
            <div class="floating-label">
                <textarea id="textInput" name="comment" placeholder=" " maxlength="100" required></textarea>
                <label for="textInput">Share your thoughts...</label>
            </div>
            <div class="word-counter" id="wordCount">0/100</div>
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
    </div>
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <span class="checkmark"></span>
            <h3>Thank You!</h3>
            <p>Your feedback helps us improve!</p>
            <button class="submit-btn" id="closePopup">Close</button>
        </div>
    </div>
    <script>
        // Emoji slider logic
        const emojiRange = document.getElementById('emojiRange');
        const emojis = document.querySelectorAll('.emoji');
        function updateEmojis(val) {
            emojis.forEach(e => {
                e.classList.remove('selected');
                if (e.dataset.value === val) e.classList.add('selected');
            });
        }
        emojiRange.addEventListener('input', function() {
            updateEmojis(this.value);
        });
        // Initialize
        updateEmojis(emojiRange.value);
        // Word counter
        const textArea = document.getElementById('textInput');
        const wordCountDisplay = document.getElementById('wordCount');
        textArea.addEventListener('input', () => {
            wordCountDisplay.textContent = textArea.value.length + "/100";
        });
        // Popup logic
        const popup = document.getElementById('popup');
        const feedbackForm = document.getElementById('feedbackForm');
        const closePopup = document.getElementById('closePopup');
        function showPopup() {
            popup.classList.add('show');
        }
        function hidePopup() {
            popup.classList.remove('show');
        }
        feedbackForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(feedbackForm);
            fetch("feedback.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                showPopup();
                setTimeout(() => {
                    window.location.href = "dashbord.php";
                }, 3000);
            })
            .catch(error => console.error("Error:", error));
        });
        closePopup.addEventListener("click", function() {
            hidePopup();
            window.location.href = "dashbord.php";
        });
        // Dark/Light mode toggle
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
    </script>
</body>
</html>

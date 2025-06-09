<?php
// Start session
session_start();
require_once "../php/db_connect.php"; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../main-php/index.php"); // Redirect to login if not logged in
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch quizzes from database
$query = "SELECT * FROM quizzes ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Test Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #06b6d4;
            --bg: #f4f6fb;
            --card-bg: #fff;
            --text: #22223b;
            --muted: #6c757d;
            --success: #22c55e;
            --danger: #ef4444;
            --dark-bg: #1a1a2e;
            --dark-card: #16213e;
            --dark-text: #e6e6e6;
        }
        [data-theme="dark"] {
            --bg: var(--dark-bg);
            --card-bg: var(--dark-card);
            --text: var(--dark-text);
            --muted: #a0a0a0;
        }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 0;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            width: 100%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            height: 64px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(79,70,229,0.08);
        }
        .navbar .title {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .title i {
            font-size: 1.5em;
            color: var(--secondary);
        }
        .navbar .nav-links {
            display: flex;
            gap: 30px;
        }
        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: color 0.2s;
            position: relative;
        }
        .navbar .nav-links a:hover {
            color: var(--success);
        }
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 40px 10px 20px 10px;
            background: linear-gradient(90deg, var(--primary) 60%, var(--secondary) 100%);
            color: #fff;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            margin-bottom: 30px;
        }
        .hero i {
            font-size: 3.5em;
            margin-bottom: 10px;
            color: var(--success);
        }
        .hero h1 {
            font-size: 2.2em;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .hero p {
            font-size: 1.1em;
            color: #e0e7ff;
            margin: 0;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto 40px auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
        }
        .search-container {
            max-width: 1100px;
            margin: 0 auto 20px;
            padding: 0 16px;
        }
        .search-box {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            font-size: 1.1em;
            background: var(--card-bg);
            color: var(--text);
            transition: all 0.3s;
        }
        .search-box:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(6,182,212,0.1);
        }
        .quiz-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(79,70,229,0.08);
            padding: 28px 24px 20px 24px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: box-shadow 0.2s, transform 0.2s;
            border: 1.5px solid #e0e7ff;
            position: relative;
            overflow: hidden;
        }
        .quiz-card:hover {
            box-shadow: 0 8px 32px rgba(6,182,212,0.13);
            transform: translateY(-4px) scale(1.02);
            border-color: var(--secondary);
        }
        .quiz-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.3em;
            color: var(--primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .quiz-card h3 i {
            color: var(--success);
            font-size: 1.1em;
        }
        .quiz-card p {
            margin: 4px 0;
            color: var(--muted);
            font-size: 1.05em;
        }
        .quiz-card .start-button {
            background: var(--success);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            margin-top: 18px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .quiz-card .start-button:hover {
            background: #15803d;
        }
        .footer {
            width: 100%;
            background: #22223b;
            color: #fff;
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 18px 0;
            font-size: 1.1em;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            margin-top: auto;
        }
        .footer a {
            color: #fff;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: var(--success);
        }
        @media (max-width: 700px) {
            .container {
                grid-template-columns: 1fr;
                padding: 0 4px;
            }
            .quiz-card {
                padding: 18px 8px 14px 8px;
            }
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 10px 8px;
                gap: 8px;
            }
            .navbar .title {
                font-size: 1.3em;
            }
            .footer {
                font-size: 1em;
                gap: 18px;
                padding: 12px 0;
            }
        }
        .new-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--success);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        .progress-container {
            width: 100%;
            height: 6px;
            background: #e0e7ff;
            border-radius: 3px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: var(--secondary);
            border-radius: 3px;
            transition: width 0.3s ease;
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
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .loading {
            position: relative;
            overflow: hidden;
        }
        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</head>
<body>
    <div class="navbar">
        <div class="title"><i class="fas fa-graduation-cap"></i> Test Hub</div>
        <div class="nav-links">
            <a href="./dashbord.php"><i class="fas fa-home"></i> Home</a>
            <a href="./history.php"><i class="fas fa-history"></i> History</a>
            <a href="./ranking.php"><i class="fas fa-trophy"></i> Ranking</a>
            <a href="../../home/home.html"><i class="fas fa-sign-out-alt"></i> Signout</a>
        </div>
    </div>

    <div class="hero">
        <i class="fas fa-user-graduate"></i>
        <h1>Welcome to Test Hub!</h1>
        <p>Take quizzes, track your progress, and climb the leaderboard.</p>
    </div>

    <div class="search-container">
        <input type="text" class="search-box" placeholder="Search quizzes..." id="quizSearch">
    </div>

    <div class="container" id="quizContainer">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $isNew = (strtotime($row["created_at"]) > strtotime('-7 days'));
                echo '<div class="quiz-card" data-title="' . htmlspecialchars($row["title"]) . '">
                            ' . ($isNew ? '<span class="new-badge">New</span>' : '') . '
                            <h3><i class="fas fa-book"></i> ' . htmlspecialchars($row["title"]) . ' <i class="fas fa-check-circle"></i></h3>
                            <p><strong>Total Marks:</strong> ' . $row["marks"] . '</p>
                            <p><strong>Time Limit:</strong> ' . $row["duration"] . ' min</p>
                            <button class="start-button" onclick="startQuiz(' . $row["id"] . ')"><i class="fas fa-play"></i> Start</button>
                        </div>';
            }
        } else {
            echo "<p>No quizzes available.</p>";
        }
        ?>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>

    <div class="footer">
        <a href="#"><i class="fas fa-info-circle"></i> About us</a>
        <a href="#"><i class="fas fa-user-shield"></i> Admin Login</a>
        <a href="#"><i class="fas fa-code"></i> Developers</a>
        <a href="#"><i class="fas fa-comment-dots"></i> Feedback</a>
    </div>

    <script>
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

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Search functionality
        document.getElementById('quizSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.quiz-card');
            
            cards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                card.style.display = title.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        function startQuiz(quizId) {
            sessionStorage.setItem('fullscreen', 'true');

            document.documentElement.requestFullscreen().then(() => {
                window.location.href = "./quiz.php?id=" + quizId + "&student_id=<?php echo $student_id; ?>";
            }).catch(() => {
                window.location.href = "./quiz.php?id=" + quizId + "&student_id=<?php echo $student_id; ?>";
            });
        }
    </script>
</body>
</html>
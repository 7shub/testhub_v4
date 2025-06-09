<?php
// Start session and DB connection
session_start();
require_once "../php/db_connect.php";

// Fetch all quizzes
$query = "SELECT id, title FROM quizzes ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Rankings</title>
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
            --sidebar-bg: #fff;
            --sidebar-border: #e0e7ff;
        }
        [data-theme="dark"] {
            --bg: var(--dark-bg);
            --card-bg: var(--dark-card);
            --text: var(--dark-text);
            --muted: #a0a0a0;
            --sidebar-bg: var(--dark-card);
            --sidebar-border: #2a2a3a;
        }
        body {
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
            background: var(--bg);
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
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar .nav-links a:hover {
            color: var(--success);
        }
        .main-wrapper {
            display: flex;
            margin-top: 64px;
            min-height: calc(100vh - 64px);
            position: relative;
        }
        .sidebar {
            width: 320px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            height: calc(100vh - 64px);
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar h2 {
            margin: 0 0 20px 0;
            color: var(--text);
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar h2 i {
            color: var(--secondary);
        }
        .quiz-item {
            padding: 12px 15px;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 8px;
            background: var(--card-bg);
            border: 1.5px solid var(--sidebar-border);
            color: var(--text);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quiz-item i {
            color: var(--muted);
            transition: color 0.3s ease;
        }
        .quiz-item:hover, .quiz-item.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateX(5px);
        }
        .quiz-item:hover i, .quiz-item.active i {
            color: #fff;
        }
        .content {
            flex-grow: 1;
            padding: 30px 40px;
            background: var(--bg);
            overflow-y: auto;
        }
        .content h2 {
            color: var(--text);
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .content h2 i {
            color: var(--secondary);
        }
        #rankingArea {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 25px;
            border: 1.5px solid var(--sidebar-border);
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
        }
        #rankingArea.empty {
            flex-direction: column;
            gap: 15px;
        }
        #rankingArea.empty i {
            font-size: 3em;
            color: var(--muted);
        }
        #rankingTable {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }
        #rankingTable th, #rankingTable td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--sidebar-border);
        }
        #rankingTable th {
            background: var(--card-bg);
            color: var(--text);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        #rankingTable tr:hover {
            background: rgba(79,70,229,0.05);
        }
        #rankingTable tr:first-child {
            background: rgba(34,197,94,0.1);
        }
        #rankingTable tr:first-child td {
            color: var(--success);
            font-weight: 600;
        }
        #rankingTable tr:nth-child(2) {
            background: rgba(6,182,212,0.1);
        }
        #rankingTable tr:nth-child(3) {
            background: rgba(239,68,68,0.1);
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
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
            .main-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                max-height: 300px;
                border-right: none;
                border-bottom: 1px solid var(--sidebar-border);
            }
            .content {
                padding: 20px;
            }
            .navbar {
                padding: 0 15px;
            }
            .navbar .title {
                font-size: 1.5rem;
            }
            .navbar .nav-links {
                gap: 15px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
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

    <div class="main-wrapper">
        <div class="sidebar">
            <h2><i class="fas fa-list"></i> Quizzes</h2>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($quiz = mysqli_fetch_assoc($result)) {
                    echo '<div class="quiz-item" onclick="loadRanking(' . $quiz['id'] . ')" data-id="' . $quiz['id'] . '">
                            <i class="fas fa-book"></i>
                            ' . htmlspecialchars($quiz['title']) . '
                          </div>';
                }
            } else {
                echo '<div class="quiz-item" style="color: var(--muted);">
                        <i class="fas fa-exclamation-circle"></i>
                        No quizzes available
                      </div>';
            }
            ?>
        </div>

        <div class="content">
            <h2><i class="fas fa-trophy"></i> Student Ranking</h2>
            <div id="rankingArea" class="empty">
                <i class="fas fa-chart-bar"></i>
                <p>Please select a quiz from the left to view rankings.</p>
            </div>
            <div class="action-buttons">
                <button id="downloadBtn" class="btn btn-primary" style="display:none;" onclick="downloadCSV()">
                    <i class="fas fa-download"></i> Download CSV
                </button>
                <button class="btn btn-secondary" onclick="window.location.href='dashbord.php'">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>

    <script>
        // Theme toggle functionality
        function toggleTheme() {
            document.body.setAttribute('data-theme', 
                document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'
            );
            localStorage.setItem('theme', document.body.getAttribute('data-theme'));
            document.querySelector('.theme-toggle i').className = 
                document.body.getAttribute('data-theme') === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // Initialize theme
        if (localStorage.getItem('theme') === 'dark' || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('theme'))) {
            document.body.setAttribute('data-theme', 'dark');
            document.querySelector('.theme-toggle i').className = 'fas fa-sun';
        }

        // Function to load rankings for the selected quiz via AJAX
        function loadRanking(quizId) {
            // Update active state
            document.querySelectorAll('.quiz-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-id') == quizId) {
                    item.classList.add('active');
                }
            });

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/fetch_ranking.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (this.status === 200) {
                    document.getElementById("rankingArea").innerHTML = this.responseText;
                    document.getElementById("rankingArea").classList.remove('empty');
                    document.getElementById("downloadBtn").style.display = 'flex';
                } else {
                    document.getElementById("rankingArea").innerHTML = 
                        '<i class="fas fa-exclamation-circle"></i><p>Error loading data.</p>';
                    document.getElementById("rankingArea").classList.add('empty');
                }
            };
            xhr.send("quiz_id=" + quizId);
        }

        // Function to trigger CSV download
        function downloadCSV() {
            const quizId = document.querySelector('.quiz-item.active').getAttribute('data-id');
            window.location.href = "../php/download_csv.php?quiz_id=" + quizId;
        }
    </script>
</body>
</html>

<?php
session_start();
include '../php/db_connect.php'; // Adjust the path if needed

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get student ID from session
$student_id = $_SESSION['user_id'];

// Fetch all attempted quizzes and their attempts for the sidebar
$sidebar_query = $conn->prepare("SELECT q.id AS quiz_id, q.title, qr.id AS result_id, qr.attempted_at, qr.total_score FROM quiz_results qr JOIN quizzes q ON qr.quiz_id = q.id WHERE qr.student_id = ? ORDER BY q.title, qr.attempted_at DESC");
$sidebar_query->bind_param("i", $student_id);
$sidebar_query->execute();
$sidebar_result = $sidebar_query->get_result();

// Organize sidebar data: quizzes => [attempts]
$quizzes = [];
foreach ($sidebar_result as $row) {
    $qid = $row['quiz_id'];
    if (!isset($quizzes[$qid])) {
        $quizzes[$qid] = [
            'title' => $row['title'],
            'attempts' => []
        ];
    }
    $quizzes[$qid]['attempts'][] = [
        'result_id' => $row['result_id'],
        'attempted_at' => $row['attempted_at'],
        'total_score' => $row['total_score']
    ];
}

// Get quiz_id and result_id from URL
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : (count($quizzes) ? array_key_first($quizzes) : null);
$result_id = isset($_GET['result_id']) ? intval($_GET['result_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz History</title>
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
        .quiz-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .quiz-item {
            margin-bottom: 15px;
        }
        .quiz-title-btn {
            width: 100%;
            background: var(--card-bg);
            border: 1.5px solid var(--sidebar-border);
            outline: none;
            text-align: left;
            padding: 12px 15px;
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .quiz-title-btn i {
            transition: transform 0.3s ease;
        }
        .quiz-title-btn.active i {
            transform: rotate(180deg);
        }
        .quiz-title-btn.active, .quiz-title-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .attempt-list {
            list-style: none;
            padding: 10px 0 0 0;
            margin: 0;
            display: none;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .attempt-link {
            display: block;
            padding: 12px 15px;
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            background: var(--card-bg);
            margin-bottom: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: 1.5px solid var(--sidebar-border);
        }
        .attempt-link:hover, .attempt-link.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateX(5px);
        }
        .content {
            flex-grow: 1;
            padding: 30px 40px;
            background: var(--bg);
            overflow-y: auto;
        }
        .answers-list {
            margin-top: 30px;
            display: grid;
            gap: 20px;
        }
        .answer-box {
            background: var(--card-bg);
            border: 1.5px solid var(--sidebar-border);
            border-radius: 16px;
            padding: 20px 25px;
            transition: all 0.3s ease;
        }
        .answer-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .answer-box p {
            margin: 8px 0;
            color: var(--text);
        }
        .answer-box .question {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .answer-box .answer {
            color: var(--text);
        }
        .answer-box .correct {
            color: var(--success);
            font-weight: 500;
        }
        .answer-box .incorrect {
            color: var(--danger);
            font-weight: 500;
        }
        .no-attempts {
            color: var(--muted);
            font-size: 0.95rem;
            margin: 10px 0 0 10px;
            font-style: italic;
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
        }
    </style>
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

        // Sidebar expand/collapse logic
        function toggleQuiz(qid) {
            var el = document.getElementById('attempts-' + qid);
            var btn = document.getElementById('quiz-btn-' + qid);
            var isVisible = el && el.style.display === 'block';
            
            document.querySelectorAll('.attempt-list').forEach(function(list) {
                if (list.id !== 'attempts-' + qid) {
                    list.style.display = 'none';
                }
            });
            
            document.querySelectorAll('.quiz-title-btn').forEach(function(btn) {
                if (btn.id !== 'quiz-btn-' + qid) {
                    btn.classList.remove('active');
                }
            });
            
            if (!isVisible) {
                if (el) el.style.display = 'block';
                if (btn) btn.classList.add('active');
            } else {
                if (el) el.style.display = 'none';
                if (btn) btn.classList.remove('active');
            }
        }

        window.onload = function() {
            var activeQuiz = '<?php echo $quiz_id; ?>';
            if (activeQuiz) toggleQuiz(activeQuiz);
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
    <div class="main-wrapper">
        <div class="sidebar">
            <h2><i class="fas fa-list"></i> Your Quizzes</h2>
            <ul class="quiz-list">
                <?php if (count($quizzes)): ?>
                    <?php foreach ($quizzes as $qid => $quiz): ?>
                        <li class="quiz-item">
                            <button class="quiz-title-btn<?php echo ($quiz_id == $qid) ? ' active' : ''; ?>" id="quiz-btn-<?php echo $qid; ?>" onclick="toggleQuiz('<?php echo $qid; ?>')">
                                <?php echo htmlspecialchars($quiz['title']); ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="attempt-list" id="attempts-<?php echo $qid; ?>" style="display:none;">
                                <?php if (count($quiz['attempts'])): ?>
                                    <?php foreach ($quiz['attempts'] as $attempt): ?>
                                        <li>
                                            <a class="attempt-link<?php echo ($quiz_id == $qid && $result_id == $attempt['result_id']) ? ' active' : ''; ?>" href="history.php?quiz_id=<?php echo $qid; ?>&result_id=<?php echo $attempt['result_id']; ?>">
                                                <i class="fas fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($attempt['attempted_at'])); ?>
                                                <span style="float: right;"><i class="fas fa-star"></i> <?php echo $attempt['total_score']; ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="no-attempts">No attempts yet</li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no-attempts">No attempted quizzes yet</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="content">
            <?php if ($result_id): ?>
                <?php
                $stmt = $conn->prepare("SELECT qa.student_answer, qa.is_correct, q.question FROM quiz_answers qa JOIN questions q ON qa.question_id = q.id WHERE qa.result_id = ?");
                $stmt->bind_param("i", $result_id);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <div class="answers-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="answer-box">
                            <p class="question"><i class="fas fa-question-circle"></i> <?php echo htmlspecialchars($row['question']); ?></p>
                            <p class="answer"><i class="fas fa-pencil-alt"></i> Your answer: <?php echo htmlspecialchars($row['student_answer']); ?></p>
                            <p class="<?php echo $row['is_correct'] ? 'correct' : 'incorrect'; ?>">
                                <i class="fas fa-<?php echo $row['is_correct'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                <?php echo $row['is_correct'] ? 'Correct answer!' : 'Incorrect answer'; ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; color: var(--muted); margin-top: 50px;">
                    <i class="fas fa-history" style="font-size: 4em; margin-bottom: 20px;"></i>
                    <h2>Select a quiz attempt to view details</h2>
                    <p>Choose from the list on the left to see your answers and results</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>
</body>
</html>

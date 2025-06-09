<?php
session_start();
require_once '../php/db_connect.php';

// Check if user is logged in as a teacher
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$selected_quiz = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

// Get all quizzes created by this teacher
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

// Get feedback for selected quiz
$feedback = [];
if ($selected_quiz) {
    // First get all students who took this quiz
    $stmt = $conn->prepare("
        SELECT DISTINCT s.id, s.username
        FROM student s
        JOIN quiz_results qr ON s.id = qr.student_id
        WHERE qr.quiz_id = ?
    ");
    $stmt->bind_param("i", $selected_quiz);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_ids = [];
    while ($row = $result->fetch_assoc()) {
        $student_ids[] = $row['id'];
    }

    // Then get feedback from these students
    if (!empty($student_ids)) {
        $placeholders = str_repeat('?,', count($student_ids) - 1) . '?';
        $stmt = $conn->prepare("
            SELECT f.*, s.username as student_name
            FROM feedback f
            JOIN student s ON f.student_id = s.id
            WHERE f.student_id IN ($placeholders)
            ORDER BY f.created_at DESC
        ");
        $stmt->bind_param(str_repeat('i', count($student_ids)), ...$student_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $feedback[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Feedback - Teacher Dashboard</title>
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
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto 40px auto;
            padding: 0 16px;
            width: 100%;
        }
        .card, .feedback-card {
            background: var(--card-bg) !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 16px rgba(79,70,229,0.08) !important;
            border: 1.5px solid #e0e7ff !important;
        }
        .card-header {
            background: none !important;
            color: var(--primary) !important;
            font-weight: 700;
            border-bottom: none !important;
        }
        .list-group-item {
            border: none !important;
            border-radius: 10px !important;
            margin-bottom: 8px;
            background: var(--card-bg) !important;
            color: var(--text) !important;
            transition: background 0.2s, color 0.2s;
        }
        .list-group-item.active, .list-group-item:hover {
            background: var(--primary) !important;
            color: #fff !important;
        }
        .feedback-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .feedback-card {
            margin-bottom: 15px;
            border-left: 4px solid var(--primary) !important;
            background: var(--card-bg);
            border-radius: 10px !important;
            box-shadow: 0 2px 8px rgba(79,70,229,0.04);
            padding: 18px 20px;
        }
        .back-button {
            position: fixed;
            top: 24px;
            left: 24px;
            z-index: 1000;
            background: var(--primary) !important;
            color: #fff !important;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3em;
            box-shadow: 0 2px 8px rgba(79,70,229,0.07);
            transition: background 0.2s, color 0.2s;
        }
        .back-button:hover {
            background: #4338ca !important;
            color: #fff !important;
        }
        .rating-stars {
            color: #ffc107;
            font-size: 1.2em;
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
        @media (max-width: 900px) {
            .container-fluid {
                padding: 0 4px;
            }
        }
        @media (max-width: 700px) {
            .hero {
                padding: 24px 4px 12px 4px;
                border-bottom-left-radius: 20px;
                border-bottom-right-radius: 20px;
            }
            .back-button {
                top: 10px;
                left: 10px;
                width: 40px;
                height: 40px;
                font-size: 1em;
            }
            .theme-toggle {
                width: 40px;
                height: 40px;
                font-size: 1em;
                bottom: 10px;
                right: 10px;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="hero">
        <i class="fas fa-comments"></i>
        <h1>Quiz Feedback</h1>
        <p>See what your students think about your quizzes.</p>
    </div>
    <a href="dashbord.php" class="btn back-button" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
    <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode"><i class="fas fa-moon"></i></button>
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom" style="border-bottom: 2px solid #e0e7ff !important;">
                    <h2 class="h2" style="color:var(--primary);font-weight:700;"><i class="fas fa-comments"></i> Quiz Feedback</h2>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-book"></i> Your Quizzes</h5>
                            </div>
                            <div class="card-body quiz-list">
                                <?php if (empty($quizzes)): ?>
                                    <p class="text-muted">No quizzes created yet.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($quizzes as $quiz_item): ?>
                                            <a href="?quiz_id=<?php echo $quiz_item['id']; ?>" 
                                               class="list-group-item list-group-item-action <?php echo $selected_quiz == $quiz_item['id'] ? 'active' : ''; ?>">
                                                <h6 class="mb-1"><i class="fas fa-book"></i> <?php echo htmlspecialchars($quiz_item['title']); ?></h6>
                                                <small class="text-muted">
                                                    Created: <?php echo date('M d, Y', strtotime($quiz_item['created_at'])); ?>
                                                </small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php if ($selected_quiz): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-star"></i> Feedback for <?php echo htmlspecialchars($quizzes[array_search($selected_quiz, array_column($quizzes, 'id'))]['title']); ?></h5>
                                </div>
                                <div class="card-body feedback-list">
                                    <?php if (empty($feedback)): ?>
                                        <p class="text-muted">No feedback received for this quiz yet.</p>
                                    <?php else: ?>
                                        <?php foreach ($feedback as $item): ?>
                                            <div class="card feedback-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="card-title mb-0"><i class="fas fa-user"></i> <?php echo htmlspecialchars($item['student_name']); ?></h6>
                                                        <div class="rating-stars">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="fas fa-star<?php echo $i <= $item['rating'] ? '' : '-o'; ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                    <p class="card-text"><?php echo htmlspecialchars($item['comment']); ?></p>
                                                    <small class="text-muted">
                                                        Submitted: <?php echo date('M d, Y H:i', strtotime($item['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html> 
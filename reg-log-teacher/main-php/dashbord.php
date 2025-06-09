<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            display: flex;
            align-items: center;
            gap: 8px;
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
        .button-container {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin: 0 0 24px 0;
        }
        .button1, .button2, .button3 {
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 500;
            background: var(--primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(79,70,229,0.08);
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .button1:hover, .button2:hover, .button3:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        .container {
            max-width: 1100px;
            margin: 0 auto 40px auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
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
        .quiz-card .delete-button {
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            margin-top: 18px;
            transition: background 0.2s;
        }
        .quiz-card .delete-button:hover {
            background: #b91c1c;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(30, 41, 59, 0.75);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--card-bg);
            color: var(--text);
            border-radius: 18px;
            padding: 36px 32px 28px 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.13);
            min-width: 320px;
            max-width: 95vw;
            border: 1.5px solid #e0e7ff;
            text-align: left;
            position: relative;
        }
        .modal-content .header {
            font-size: 1.3em;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 18px;
        }
        .modal-content label {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-weight: 500;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1.5px solid #e0e7ff;
            margin-bottom: 16px;
            font-size: 1em;
            background: var(--card-bg);
            color: var(--text);
            transition: border 0.2s;
        }
        .modal-content input:focus, .modal-content select:focus {
            border: 1.5px solid var(--primary);
            outline: none;
        }
        .modal-content .buttons {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
        }
        .save-button {
            background: var(--success);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .save-button:hover {
            background: #15803d;
        }
        .cancel-button {
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .cancel-button:hover {
            background: #b91c1c;
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
            .modal-content {
                padding: 18px 8px 16px 8px;
            }
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
        <div class="title"><i class="fas fa-chalkboard-teacher"></i> Test Hub</div>
        <div class="nav-links">
            <a href="./dashbord.php"><i class="fas fa-home"></i> Home</a>
            <a href="./quiz_results.php"><i class="fas fa-poll"></i> Result</a>
            <a href="./ranking.php"><i class="fas fa-trophy"></i> Ranking</a>
            <a href="./quiz_feedback.php"><i class="fas fa-comments"></i> Feedbacks</a>
            <a href="../../home/home.html"><i class="fas fa-sign-out-alt"></i> Signout</a>
        </div>
    </div>
    <div class="hero">
        <i class="fas fa-chalkboard-teacher"></i>
        <h1>Welcome, Teacher!</h1>
        <p>Manage your quizzes, view results, and track your students' progress.</p>
    </div>
    <div class="search-container">
        <input type="text" id="quizSearch" class="search-box" placeholder="Search quizzes by title...">
    </div>
    <div class="button-container">
        <button class="button1" onclick="showModal()"><i class="fas fa-plus"></i> New Quiz</button>
        <button class="button2" onclick="showFindModal()"><i class="fas fa-search"></i> Find Quiz</button>
        <button class="button3" onclick="showFilterModal()"><i class="fas fa-filter"></i> Filter Quizzes</button>
    </div>
    <div class="container" id="quizContainer">
        <?php
        include '../php/db_connect.php';
        $query = "SELECT quizzes.*, teachers.username AS teacher_name FROM quizzes 
                  JOIN teachers ON quizzes.teacher_id = teachers.id 
                  ORDER BY quizzes.id DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='quiz-card' data-title='{$row['title']}' data-created-at='{$row['created_at']}'>
                    <h3><i class='fas fa-book'></i> {$row['title']} <i class='fas fa-check-circle' style='color:var(--success);'></i></h3>
                    <p><strong>Total Marks:</strong> {$row['marks']}</p>
                    <p><strong>Time Limit:</strong> {$row['duration']} min</p>
                    <p><strong>Created By:</strong> {$row['teacher_name']}</p>
                    <p><strong>Created At:</strong> {$row['created_at']}</p>
                    <form action='../php/delete_quiz.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='quiz_id' value='{$row['id']}'>
                        <button type='submit' class='delete-button'><i class='fas fa-trash'></i> Delete</button>
                    </form>
                  </div>";
        }
        ?>
    </div>
    <div class="footer">
        <a href="#"><i class="fas fa-info-circle"></i> About us</a>
        <a href="#"><i class="fas fa-user-shield"></i> Admin Login</a>
        <a href="#"><i class="fas fa-code"></i> Developers</a>
        <a href="#"><i class="fas fa-comment-dots"></i> Feedback</a>
    </div>
    <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode"><i class="fas fa-moon"></i></button>
    <div id="quizModal" class="modal">
        <div class="modal-content">
            <div class="header"><i class="fas fa-plus"></i> New Quiz</div>
            <form action="../php/create_quiz.php" method="POST">
                <label>Title</label>
                <input type="text" name="title" placeholder="Enter Quiz Title" required>
                <label>Marks</label>
                <input type="number" name="marks" placeholder="Enter Total Marks" required>
                <label>Duration</label>
                <input type="number" name="duration" placeholder="Enter Duration (minutes)" required>
                <label>Teacher</label>
                <select name="teacher_id" required>
                    <?php
                    $teacherQuery = "SELECT id, username FROM teachers";
                    $teacherResult = mysqli_query($conn, $teacherQuery);
                    while ($teacher = mysqli_fetch_assoc($teacherResult)) {
                        echo "<option value='{$teacher['id']}'>{$teacher['username']}</option>";
                    }
                    ?>
                </select>
                <div class="buttons">
                    <button type="submit" class="save-button"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="cancel-button" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div id="findModal" class="modal">
        <div class="modal-content">
            <div class="header"><i class="fas fa-search"></i> Find Quiz</div>
            <label>Quiz Name</label>
            <input type="text" id="findInput" placeholder="Enter quiz name">
            <div class="buttons">
                <button class="save-button" onclick="findQuiz()"><i class="fas fa-search"></i> Find</button>
                <button class="cancel-button" onclick="closeFindModal()"><i class="fas fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
    <div id="filterModal" class="modal">
        <div class="modal-content">
            <div class="header"><i class="fas fa-filter"></i> Filter Quizzes</div>
            <label>Start Date</label>
            <input type="date" id="startDate">
            <label>End Date</label>
            <input type="date" id="endDate">
            <div class="buttons">
                <button class="save-button" onclick="filterQuizzes()"><i class="fas fa-filter"></i> Apply Filter</button>
                <button class="cancel-button" onclick="closeFilterModal()"><i class="fas fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
    <script>
        function showModal() { document.getElementById('quizModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('quizModal').style.display = 'none'; }
        function showFindModal() {
            document.getElementById('findModal').style.display = 'flex';
        }
        function closeFindModal() {
            document.getElementById('findModal').style.display = 'none';
        }
        function showFilterModal() {
            document.getElementById('filterModal').style.display = 'flex';
        }
        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }
        function findQuiz() {
            var searchText = document.getElementById("findInput").value.toLowerCase();
            var quizzes = document.querySelectorAll(".quiz-card");
            quizzes.forEach(function(quiz) {
                var title = quiz.getAttribute("data-title").toLowerCase();
                quiz.style.display = title.includes(searchText) ? "block" : "none";
            });
            closeFindModal();
        }
        function filterQuizzes() {
            var startDate = document.getElementById("startDate").value;
            var endDate = document.getElementById("endDate").value;
            if (!startDate || !endDate) {
                alert("Please select both start and end dates.");
                return;
            }
            var quizzes = document.querySelectorAll(".quiz-card");
            quizzes.forEach(function(quiz) {
                var createdAt = quiz.getAttribute("data-created-at").split(" ")[0]; // Extract YYYY-MM-DD
                quiz.style.display = (createdAt >= startDate && createdAt <= endDate) ? "block" : "none";
            });
            closeFilterModal();
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
    </script>
</body>
</html>
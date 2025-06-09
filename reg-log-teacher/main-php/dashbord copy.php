
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Hub</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="title">Test Hub</div>
        <div class="nav-links">
            <a href="./dashboard.php">Home</a>
            <a href="../ranking.html">Ranking</a>
            <a href="../../home/home.html">Signout</a>
        </div>
    </div>

    <!-- Buttons -->
    <div class="button-container">
        <button class="button1" onclick="showModal()">New Quiz</button>
        <button class="button2" onclick="showFindModal()">Find Quiz</button>
        <button class="button3" onclick="showFilterModal()">Filter Quizzes</button>
    </div>

    <!-- Main Container -->
    <div class="container" id="quizContainer">
        <?php
        include '../php/db_connect.php';
        $query = "SELECT * FROM quizzes ORDER BY id DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='quiz-card' data-title='{$row['title']}' data-created-at='{$row['created_at']}'>
                    <h3>{$row['title']} ✔</h3>
                    <p><strong>Total Marks:</strong> {$row['marks']}</p>
                    <p><strong>Time Limit:</strong> {$row['duration']} min</p>
                    <p><strong>Created At:</strong> {$row['created_at']}</p>
                    <form action='../php/delete_quiz.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='quiz_id' value='{$row['id']}'>
                        <button type='submit' class='delete-button'>Delete</button>
                    </form>
                  </div>";
        }
        ?>
    </div>

    <div class="footer">
        <a href="#">About us</a>
        <a href="#">Admin Login</a>
        <a href="#">Developers</a>
        <a href="#">Feedback</a>
    </div>

    <!-- Modal for New Quiz -->
    <div id="quizModal" class="modal">
        <div class="modal-content">
            <div class="header">New Quiz</div>
            <form action="../php/create_quiz.php" method="POST">
                <label>Title</label>
                <input type="text" name="title" placeholder="Enter Quiz Title" required>

                <label>Marks</label>
                <input type="number" name="marks" placeholder="Enter Total Marks" required>

                <label>Duration</label>
                <input type="number" name="duration" placeholder="Enter Duration (minutes)" required>

                <div class="buttons">
                    <button type="submit" class="save-button">Save</button>
                    <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Finding a Quiz -->
    <div id="findModal" class="modal">
        <div class="modal-content">
            <div class="header">Find Quiz</div>
            <label>Quiz Name</label>
            <input type="text" id="findInput" placeholder="Enter quiz name">
            <div class="buttons">
                <button class="save-button" onclick="findQuiz()">Find</button>
                <button class="cancel-button" onclick="closeFindModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Modal for Filtering Quizzes -->
    <div id="filterModal" class="modal">
        <div class="modal-content">
            <div class="header">Filter Quizzes</div>
            <label>Start Date</label>
            <input type="date" id="startDate">
            <label>End Date</label>
            <input type="date" id="endDate">
            <div class="buttons">
                <button class="save-button" onclick="filterQuizzes()">Apply Filter</button>
                <button class="cancel-button" onclick="closeFilterModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function showModal() {
            document.getElementById('quizModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('quizModal').style.display = 'none';
        }

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
    </script>
</body>
</html>

<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once "../php/db_connect.php";

// Fetch quizzes
$query = "SELECT * FROM quizzes ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Test Hub - Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4f46e5;
      --secondary: #06b6d4;
      --accent: #f59e42;
      --bg: #f4f6fb;
      --card-bg: #fff;
      --text: #22223b;
      --muted: #6c757d;
      --success: #22c55e;
      --danger: #ef4444;
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
      color: var(--accent);
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
      color: var(--accent);
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
      color: var(--accent);
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
    .success-banner {
      background-color: #d1fae5;
      color: var(--success);
      padding: 12px 20px;
      margin: 20px auto 0 auto;
      text-align: center;
      border-radius: 7px;
      font-weight: bold;
      max-width: 600px;
      box-shadow: 0 2px 8px rgba(34,197,94,0.08);
      font-size: 1.1em;
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
    .quiz-card form {
      margin-top: 18px;
      width: 100%;
      display: flex;
      justify-content: flex-end;
    }
    .quiz-card .start-button {
      background: var(--danger);
      color: #fff;
      border: none;
      padding: 8px 18px;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }
    .quiz-card .start-button:hover {
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
      flex-shrink: 0;
    }
    .footer a {
      color: #fff;
      text-decoration: none;
      transition: color 0.2s;
    }
    .footer a:hover {
      color: var(--accent);
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
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="title"><i class="fas fa-user-shield"></i> TestHub Admin</div>
    <div class="nav-links">
      <a href="./dashbord.php"><i class="fas fa-home"></i> Home</a>
      <a href="./ranking.php"><i class="fas fa-trophy"></i> Ranking</a>
      <a href="./manage_users.php"><i class="fas fa-users"></i> Users</a>
      <a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Signout</a>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="hero">
    <i class="fas fa-user-cog"></i>
    <h1>Welcome, Admin!</h1>
    <p>Manage quizzes, view rankings, and control users with ease.</p>
  </div>

  <!-- Success Banner -->
  <?php if (isset($_GET['delete_success'])): ?>
    <div class="success-banner"><i class="fas fa-check-circle"></i> Quiz deleted successfully!</div>
  <?php endif; ?>

  <!-- Main Container -->
  <div class="container">
    <?php
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="quiz-card">
                <h3><i class="fas fa-book"></i> ' . htmlspecialchars($row["title"]) . ' <i class="fas fa-check-circle"></i></h3>
                <p><strong>Total Marks:</strong> ' . $row["marks"] . '</p>
                <p><strong>Time Limit:</strong> ' . $row["duration"] . ' min</p>
                <form method="POST" action="../php/delete_quiz.php" onsubmit="return confirmDelete();">
                  <input type="hidden" name="quiz_id" value="' . $row["id"] . '">
                  <button type="submit" class="start-button"><i class="fas fa-trash"></i> Delete</button>
                </form>
              </div>';
      }
    } else {
      echo "<p>No quizzes available.</p>";
    }
    ?>
  </div>

  <!-- Footer -->
  <div class="footer">
    <a href="#"><i class="fas fa-info-circle"></i> About us</a>
    <a href="#"><i class="fas fa-user-shield"></i> Admin Login</a>
    <a href="#"><i class="fas fa-code"></i> Developers</a>
    <a href="#"><i class="fas fa-comment-dots"></i> Feedback</a>
  </div>

  <script>
    function confirmDelete() {
      return confirm("Are you sure you want to delete this quiz?");
    }

    // Disable browser back/forward buttons
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
      history.go(1);
    };

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // Prevent unauthorized navigation
    document.addEventListener('keydown', function(event) {
      // Disable F5 and Ctrl+R
      if (event.key === 'F5' || (event.ctrlKey && event.key === 'r')) {
        event.preventDefault();
      }
    });
  </script>

</body>
</html>

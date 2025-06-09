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
    }
    [data-theme="dark"] {
      --bg: var(--dark-bg);
      --card-bg: var(--dark-card);
      --text: var(--dark-text);
      --muted: #a0a0a0;
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
    .main-wrapper {
      display: flex;
      flex: 1;
      min-height: 0;
      width: 100%;
      max-width: 1200px;
      margin: 0 auto 40px auto;
      padding: 0 16px;
      box-sizing: border-box;
    }
    .sidebar {
      width: 320px;
      background: var(--card-bg);
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(79,70,229,0.08);
      border: 1.5px solid #e0e7ff;
      height: fit-content;
      padding: 24px 20px;
      box-sizing: border-box;
      overflow-y: auto;
      margin-right: 32px;
      margin-bottom: 0;
    }
    .sidebar h2 {
      margin: 0 0 20px 0;
      color: var(--primary);
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
      padding: 12px 15px;
      cursor: pointer;
      border-radius: 8px;
      margin-bottom: 8px;
      background: var(--card-bg);
      border: 1.5px solid #e0e7ff;
      color: var(--text);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.08em;
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
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(79,70,229,0.08);
      border: 1.5px solid #e0e7ff;
      min-height: 300px;
    }
    .content h2 {
      color: var(--primary);
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
      border: 1.5px solid #e0e7ff;
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
    #downloadBtn {
      margin-top: 20px;
      padding: 10px 18px;
      background: var(--success);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.08em;
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(34,197,94,0.08);
      transition: background 0.2s, transform 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    #downloadBtn:hover {
      background: #15803d;
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
    #backToDashboardBtn {
      position: fixed;
      bottom: 20px;
      left: 20px;
      padding: 12px 20px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.08em;
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(79,70,229,0.08);
      display: flex;
      align-items: center;
      gap: 8px;
      transition: background 0.2s, transform 0.2s;
    }
    #backToDashboardBtn:hover {
      background: #4338ca;
      transform: translateY(-2px);
    }
    @media (max-width: 900px) {
      .main-wrapper {
        flex-direction: column;
        padding: 0 4px;
      }
      .sidebar {
        width: 100%;
        margin-right: 0;
        margin-bottom: 24px;
      }
      .content {
        padding: 18px 8px;
      }
    }
    @media (max-width: 600px) {
      .hero {
        padding: 24px 4px 12px 4px;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
      }
      .sidebar {
        padding: 12px 4px;
      }
      .content {
        padding: 10px 2px;
      }
      #backToDashboardBtn, .theme-toggle {
        width: 40px;
        height: 40px;
        font-size: 1em;
        padding: 8px 10px;
      }
    }
    .ranking-list {
      display: flex;
      flex-direction: column;
      gap: 18px;
      margin: 0;
      padding: 0;
      list-style: none;
    }
    .ranking-card {
      display: flex;
      align-items: center;
      background: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(79,70,229,0.06);
      border: 1.5px solid #e0e7ff;
      padding: 18px 22px;
      gap: 18px;
      transition: box-shadow 0.2s, transform 0.2s;
      position: relative;
    }
    .ranking-card .rank-badge {
      font-size: 1.5em;
      font-weight: bold;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: #e0e7ff;
      color: var(--primary);
      margin-right: 10px;
      flex-shrink: 0;
    }
    .ranking-card.gold .rank-badge {
      background: linear-gradient(135deg, #ffd700 60%, #fffbe6 100%);
      color: #bfa100;
      box-shadow: 0 0 8px #ffd70055;
    }
    .ranking-card.silver .rank-badge {
      background: linear-gradient(135deg, #c0c0c0 60%, #f8f8f8 100%);
      color: #888;
      box-shadow: 0 0 8px #c0c0c055;
    }
    .ranking-card.bronze .rank-badge {
      background: linear-gradient(135deg, #cd7f32 60%, #fff3e0 100%);
      color: #a05a2c;
      box-shadow: 0 0 8px #cd7f3255;
    }
    .ranking-card .avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: var(--secondary);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2em;
      font-weight: 700;
      margin-right: 12px;
      flex-shrink: 0;
    }
    .ranking-card .student-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .ranking-card .student-info .name {
      font-weight: 700;
      color: var(--primary);
      font-size: 1.1em;
    }
    .ranking-card .student-info .score {
      color: var(--muted);
      font-size: 1em;
    }
    .ranking-card .score-badge {
      background: var(--success);
      color: #fff;
      border-radius: 8px;
      padding: 6px 16px;
      font-weight: 600;
      font-size: 1.08em;
      margin-left: 18px;
      box-shadow: 0 2px 8px rgba(34,197,94,0.08);
    }
    @media (max-width: 600px) {
      .ranking-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 12px 8px;
      }
      .ranking-card .score-badge {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="hero">
    <i class="fas fa-trophy"></i>
    <h1>Quiz Rankings</h1>
    <p>See how your students rank in each quiz.</p>
  </div>
  <div class="main-wrapper">
    <!-- Sidebar: Quiz List -->
    <div class="sidebar">
      <h2><i class="fas fa-book"></i> Quizzes</h2>
      <div class="quiz-list">
      <?php
        if (mysqli_num_rows($result) > 0) {
          while ($quiz = mysqli_fetch_assoc($result)) {
            echo '<div class="quiz-item" onclick="loadRanking(' . $quiz['id'] . ')"><i class="fas fa-book"></i> ' . htmlspecialchars($quiz['title']) . '</div>';
          }
        } else {
          echo "<p>No quizzes available.</p>";
        }
      ?>
      </div>
    </div>
    <!-- Content: Ranking Placeholder -->
    <div class="content">
      <h2><i class="fas fa-list-ol"></i> Student Ranking</h2>
      <div id="rankingArea">
        <p><i class="fas fa-arrow-left"></i> Please select a quiz from the left to view rankings.</p>
      </div>
    </div>
  </div>
  <!-- Button to go back to dashboard -->
  <button id="backToDashboardBtn" onclick="window.location.href='dashbord.php'"><i class="fas fa-arrow-left"></i> Back to Dashboard</button>
  <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode"><i class="fas fa-moon"></i></button>
  <script>
    // Function to load rankings for the selected quiz via AJAX
    function loadRanking(quizId) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "../php/fetch_ranking.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.onload = function () {
        if (this.status === 200) {
          // Transform the table into modern ranking cards
          renderRankingCards(this.responseText);
        } else {
          document.getElementById("rankingArea").innerHTML = "Error loading data.";
        }
      };
      xhr.send("quiz_id=" + quizId);
    }

    // Transform table HTML to modern ranking cards
    function renderRankingCards(tableHtml) {
      // Try to parse the table rows from the HTML
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = tableHtml;
      const table = tempDiv.querySelector('table');
      if (!table) {
        document.getElementById('rankingArea').innerHTML = tableHtml;
        return;
      }
      const rows = Array.from(table.querySelectorAll('tr'));
      if (rows.length < 2) {
        document.getElementById('rankingArea').innerHTML = '<p>No ranking data available.</p>';
        return;
      }
      // Assume first row is header
      const headers = Array.from(rows[0].querySelectorAll('th')).map(th => th.textContent.trim().toLowerCase());
      const rankIdx = headers.findIndex(h => h.includes('rank'));
      const nameIdx = headers.findIndex(h => h.includes('name') || h.includes('student'));
      const scoreIdx = headers.findIndex(h => h.includes('score'));
      const extraIdx = headers.findIndex(h => h.includes('extra'));
      const cards = [];
      for (let i = 1; i < rows.length; i++) {
        const cells = Array.from(rows[i].querySelectorAll('td'));
        if (cells.length < 2) continue;
        const rank = cells[rankIdx]?.textContent.trim() || (i);
        const name = cells[nameIdx]?.textContent.trim() || '';
        const score = cells[scoreIdx]?.textContent.trim() || '';
        let extra = extraIdx !== -1 ? cells[extraIdx]?.textContent.trim() : '';
        // Medal class for top 3
        let medal = '';
        if (rank == 1) medal = 'gold';
        else if (rank == 2) medal = 'silver';
        else if (rank == 3) medal = 'bronze';
        // Avatar: first letter of name
        const avatar = name ? name[0].toUpperCase() : '?';
        cards.push(`
          <li class="ranking-card ${medal}">
            <span class="rank-badge">${rank == 1 ? '🥇' : rank == 2 ? '🥈' : rank == 3 ? '🥉' : rank}</span>
            <span class="avatar"><i class="fas fa-user"></i> ${avatar}</span>
            <div class="student-info">
              <span class="name">${name}</span>
              ${extra ? `<span class="score">${extra}</span>` : ''}
            </div>
            <span class="score-badge">${score}</span>
          </li>
        `);
      }
      document.getElementById('rankingArea').innerHTML = `<ul class="ranking-list">${cards.join('')}</ul>`;
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
  </script>
</body>
</html>

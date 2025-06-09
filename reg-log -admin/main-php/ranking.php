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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
    }
    body {
      margin: 0;
      display: flex;
      font-family: 'Roboto', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }
    .sidebar {
      width: 270px;
      background: var(--primary);
      color: #fff;
      border-right: 1.5px solid #e0e7ff;
      height: 100vh;
      padding: 30px 18px 20px 18px;
      box-sizing: border-box;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    .sidebar h2 {
      margin-top: 0;
      font-size: 1.5em;
      font-weight: 700;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar h2 i {
      color: var(--success);
    }
    .quiz-item {
      padding: 12px 16px;
      cursor: pointer;
      border-radius: 8px;
      margin-bottom: 10px;
      background: rgba(255,255,255,0.07);
      color: #fff;
      font-size: 1.08em;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: background 0.2s, color 0.2s;
    }
    .quiz-item:hover, .quiz-item.active {
      background: #fff;
      color: var(--primary);
      font-weight: 700;
    }
    .content {
      flex-grow: 1;
      padding: 40px 30px 30px 30px;
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    .content h2 {
      font-size: 2em;
      font-weight: 700;
      margin-bottom: 18px;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    #rankingArea {
      background: var(--card-bg);
      border-radius: 14px;
      box-shadow: 0 4px 16px rgba(79,70,229,0.08);
      padding: 28px 18px 18px 18px;
      min-width: 320px;
      width: 100%;
      margin-bottom: 18px;
    }
    #rankingTable {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    #rankingTable th, #rankingTable td {
      padding: 12px 10px;
      border: 1px solid #e0e7ff;
      text-align: left;
    }
    #rankingTable th {
      background-color: #f0f4ff;
      color: var(--primary);
      font-size: 1.08em;
    }
    #rankingTable td {
      font-size: 1.05em;
    }
    #downloadBtn {
      margin-top: 10px;
      padding: 10px 18px;
      background-color: var(--success);
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 500;
      box-shadow: 0 2px 8px rgba(34,197,94,0.08);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    #downloadBtn i {
      font-size: 1.1em;
    }
    #downloadBtn:hover {
      background-color: #16a34a;
    }
    #backToDashboardBtn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 12px 20px;
      background-color: var(--primary);
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: 500;
      box-shadow: 0 2px 8px rgba(79,70,229,0.08);
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 100;
    }
    #backToDashboardBtn i {
      font-size: 1.1em;
    }
    #backToDashboardBtn:hover {
      background-color: #3730a3;
    }
    @media (max-width: 900px) {
      body {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 18px 6px 10px 6px;
        border-right: none;
        border-bottom: 1.5px solid #e0e7ff;
        justify-content: flex-start;
      }
      .quiz-item {
        margin-bottom: 0;
        margin-right: 10px;
      }
      .content {
        padding: 24px 6px 10px 6px;
      }
    }
    @media (max-width: 600px) {
      .content h2 {
        font-size: 1.2em;
      }
      #rankingArea {
        padding: 10px 2px 10px 2px;
      }
      #backToDashboardBtn {
        padding: 8px 10px;
        font-size: 1em;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar: Quiz List -->
  <div class="sidebar">
    <h2><i class="fas fa-list-ol"></i> Quizzes</h2>
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

  <!-- Content: Ranking Placeholder -->
  <div class="content">
    <h2><i class="fas fa-trophy"></i> Student Ranking</h2>
    <div id="rankingArea">
      <p>Please select a quiz from the left to view rankings.</p>
    </div>
    <button id="downloadBtn" style="display:none;" onclick="downloadCSV()"><i class="fas fa-download"></i> Download CSV</button>
  </div>

  <!-- Button to go back to dashboard -->
  <button id="backToDashboardBtn" onclick="window.location.href='dashbord.php'"><i class="fas fa-arrow-left"></i> Back to Dashboard</button>

  <script>
    // Function to load rankings for the selected quiz via AJAX
    function loadRanking(quizId) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "../php/fetch_ranking.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.onload = function () {
        if (this.status === 200) {
          document.getElementById("rankingArea").innerHTML = this.responseText;
          document.getElementById("downloadBtn").style.display = 'block'; // Show the download button
        } else {
          document.getElementById("rankingArea").innerHTML = "Error loading data.";
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

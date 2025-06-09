<?php
session_start();
require_once "../php/db_connect.php";

// Fetch teachers
$teachersQuery = "SELECT id, username, email FROM teachers ORDER BY created_at DESC";
$teachersResult = mysqli_query($conn, $teachersQuery);

// Fetch students
$studentsQuery = "SELECT id, username, roll_no, semester, email FROM student ORDER BY created_at DESC";
$studentsResult = mysqli_query($conn, $studentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Users</title>
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
      padding: 0;
      font-family: 'Roboto', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    h2 {
      margin-top: 40px;
      font-size: 2em;
      color: var(--primary);
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .user-table-container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto 40px auto;
      background: var(--card-bg);
      border-radius: 14px;
      box-shadow: 0 4px 16px rgba(79,70,229,0.08);
      padding: 18px 8px 8px 8px;
      overflow-x: auto;
    }
    .user-table {
      width: 100%;
      border-collapse: collapse;
      background: var(--card-bg);
    }
    .user-table th, .user-table td {
      padding: 14px 10px;
      border: 1px solid #e0e7ff;
      text-align: left;
      font-size: 1.05em;
    }
    .user-table th {
      background: #f0f4ff;
      color: var(--primary);
      font-weight: 700;
    }
    .user-table tr.accordion-row {
      cursor: pointer;
      transition: background 0.2s;
    }
    .user-table tr.accordion-row:hover {
      background: #f0f4ff;
    }
    .user-table tr.accordion-content-row {
      background: #f9fafb;
    }
    .accordion-content {
      padding: 12px 0 8px 0;
      font-size: 1em;
    }
    .delete-btn {
      background: var(--danger);
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 7px 16px;
      font-size: 1em;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .delete-btn:hover {
      background: #b91c1c;
    }
    .back-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 12px 20px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-size: 1.1em;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 2px 8px rgba(79,70,229,0.08);
      z-index: 100;
    }
    .back-btn i {
      font-size: 1.1em;
    }
    .back-btn:hover {
      background: #3730a3;
    }
    @media (max-width: 900px) {
      .user-table-container {
        padding: 8px 2px 8px 2px;
      }
      .user-table th, .user-table td {
        padding: 10px 4px;
        font-size: 0.98em;
      }
    }
    @media (max-width: 600px) {
      h2 {
        font-size: 1.2em;
      }
      .back-btn {
        padding: 8px 10px;
        font-size: 1em;
      }
      .user-table th, .user-table td {
        padding: 7px 2px;
        font-size: 0.93em;
      }
    }
  </style>
</head>
<body>

  <h2><i class="fas fa-chalkboard-teacher"></i> Teachers</h2>
  <div class="user-table-container">
    <table class="user-table">
      <thead>
        <tr>
          <th><i class="fas fa-user"></i> Username</th>
          <th><i class="fas fa-envelope"></i> Email</th>
          <th><i class="fas fa-cogs"></i> Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($teachersResult) > 0) {
          while ($teacher = mysqli_fetch_assoc($teachersResult)) {
            echo '<tr class="accordion-row" onclick="toggleAccordion(this)">
                    <td>' . htmlspecialchars($teacher['username']) . '</td>
                    <td>' . htmlspecialchars($teacher['email']) . '</td>
                    <td><button class="delete-btn" onclick="event.stopPropagation();deleteTeacher(' . $teacher['id'] . ')"><i class="fas fa-trash"></i> Delete</button></td>
                  </tr>';
            echo '<tr class="accordion-content-row" style="display:none;"><td colspan="3"><div class="accordion-content">'
                . '<strong>Teacher ID:</strong> ' . $teacher['id'] . '<br>'
                . '<strong>Email:</strong> ' . htmlspecialchars($teacher['email']) . '<br>'
                . '<span style="color:var(--muted);font-size:0.95em;">More details or actions can go here.</span>'
                . '</div></td></tr>';
          }
        } else {
          echo '<tr><td colspan="3">No teachers found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <h2><i class="fas fa-user-graduate"></i> Students</h2>
  <div class="user-table-container">
    <table class="user-table">
      <thead>
        <tr>
          <th><i class="fas fa-user"></i> Username</th>
          <th><i class="fas fa-id-badge"></i> Roll No</th>
          <th><i class="fas fa-layer-group"></i> Semester</th>
          <th><i class="fas fa-envelope"></i> Email</th>
          <th><i class="fas fa-cogs"></i> Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($studentsResult) > 0) {
          while ($student = mysqli_fetch_assoc($studentsResult)) {
            echo '<tr class="accordion-row" onclick="toggleAccordion(this)">
                    <td>' . htmlspecialchars($student['username']) . '</td>
                    <td>' . htmlspecialchars($student['roll_no']) . '</td>
                    <td>' . htmlspecialchars($student['semester']) . '</td>
                    <td>' . htmlspecialchars($student['email']) . '</td>
                    <td><button class="delete-btn" onclick="event.stopPropagation();deleteStudent(' . $student['id'] . ')"><i class="fas fa-trash"></i> Delete</button></td>
                  </tr>';
            echo '<tr class="accordion-content-row" style="display:none;"><td colspan="5"><div class="accordion-content">'
                . '<strong>Student ID:</strong> ' . $student['id'] . '<br>'
                . '<strong>Email:</strong> ' . htmlspecialchars($student['email']) . '<br>'
                . '<span style="color:var(--muted);font-size:0.95em;">More details or actions can go here.</span>'
                . '</div></td></tr>';
          }
        } else {
          echo '<tr><td colspan="5">No students found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Back to dashboard -->
  <a href="dashbord.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

  <script>
    function deleteTeacher(id) {
      if (confirm("Are you sure you want to delete this teacher?")) {
        window.location.href = "../php/delete_teacher.php?id=" + id;
      }
    }
    function deleteStudent(id) {
      if (confirm("Are you sure you want to delete this student?")) {
        window.location.href = "../php/delete_student.php?id=" + id;
      }
    }
    function toggleAccordion(row) {
      var next = row.nextElementSibling;
      if (next && next.classList.contains('accordion-content-row')) {
        next.style.display = (next.style.display === 'none' || next.style.display === '') ? 'table-row' : 'none';
      }
    }
  </script>

</body>
</html>

<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $roll_no = $_POST['roll_no'];
        $semester = $_POST['semester'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO student (username, roll_no, semester, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $username, $roll_no, $semester, $email, $password);
        if ($stmt->execute()) {
            header("Location: ../main-php/index.php");
            exit();
        } else {
            echo "<div style='text-align: center; margin-top: 20px;'>
                    <p style='color: red; font-size: 18px;'>Error: " . $stmt->error . "</p>
                    <a href='../main-php/index.php' style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #4caf50;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        margin-top: 10px;'>
                        try logging-in again
                    </a>
                  </div>";
        }
        
        $stmt->close();
    }

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM student WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == $id) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    $_SESSION['logged_in'] = true;
                    header("Location: ../main-php/dashbord.html");
                } else {
                    echo "<div style='text-align: center; margin-top: 20px;User already logged in from another session!<a href='../main-php/index.php' style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #4caf50;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        margin-top: 10px;'>
                        try logging-in again
                    </a>
                  </div>";
                }
            } else {
                echo "<div style='text-align: center; margin-top: 20px;'>Invalid credentials!<a href='../main-php/index.php' style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #4caf50;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        margin-top: 10px;'>
                        try logging-in again
                    </a>
                  </div>";
            }
        } else {
            echo "<div style='text-align: center; margin-top: 20px;'>User not found!<a href='../main-php/index.php' style='
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #4caf50;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        margin-top: 10px;'>
                        try logging-in again
                    </a>
                  </div>";
        }
        $stmt->close();
    }
}
$conn->close();
?>
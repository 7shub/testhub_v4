<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitizeInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    function showError($message) {
        echo "<div style='text-align: center; margin-top: 20px; font-family: Arial, sans-serif;'>
                    <p style='color: red; font-size: 18px;'>$message</p>
                    <a href='../main-php/index.php'
                        style='display: inline-block; padding: 10px 15px; background: #21264d; color: white; text-decoration: none; border-radius: 5px;'>
                        Go to Login Page
                    </a>
                </div>";
        exit();
    }

    if (isset($_POST['register'])) {
        $username = sanitizeInput($_POST['username']);
        $roll_no = sanitizeInput($_POST['roll_no']);
        $semester = sanitizeInput($_POST['semester']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];

        // Validate input fields
        if (!preg_match('/^[a-zA-Z0-9_]{4,}$/', $username)) {
            showError("Username must be at least 4 characters long and contain only letters, numbers, and underscores.");
        }

        if (!is_numeric($roll_no)) {
            showError("Roll number must be numeric.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            showError("Invalid email format.");
        }

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
            showError("Password must be at least 8 characters long and contain at least one letter and one number.");
        }

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM student WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            showError("Username or Email already exists.");
        }
        $stmt->close();

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO student (username, roll_no, semester, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $username, $roll_no, $semester, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            header("Location: ../main-php/index.php");
            exit();
        } else {
            showError("Error: " . $stmt->error);
        }
        $stmt->close();
    }

    if (isset($_POST['login'])) {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM student WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                header("Location: ../main-php/dashbord.php");
                exit();
            } else {
                showError("Invalid credentials.");
            }
        } else {
            showError("User not found.");
        }
        $stmt->close();
    }
}
$conn->close();
?>
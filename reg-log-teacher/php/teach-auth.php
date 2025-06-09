<?php
session_start();
include 'db_connect.php'; // Database connection file

// Handle Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use a prepared statement
    $stmt = $conn->prepare("SELECT id, username, password FROM teachers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['teacher_id'] = $row['id'];
            $_SESSION['teacher_username'] = $row['username'];
            header("Location: ../main-php/dashbord.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='../main-php/index.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='../main-php/index.php';</script>";
    }
    $stmt->close();
}

// Handle Registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user already exists
    $checkStmt = $conn->prepare("SELECT id FROM teachers WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Username already taken!'); window.location.href='../index.php';</script>";
        exit();
    }

    $checkStmt->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO teachers (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to register'); window.location.href='../index.php';</script>";
    }
    $stmt->close();
}
?>

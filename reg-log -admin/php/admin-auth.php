<?php
session_start();
include '../php/db_connect.php'; // Update if your db connection path is different

// Admin Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: ../main-php/dashbord.php"); // Redirect to admin dashboard
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='../main-php/index.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='../main-php/index.php';</script>";
    }
    $stmt->close();
}

// Admin Registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for duplicates
    $check = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Username already taken'); window.location.href='../main-php/index.php';</script>";
        exit();
    }
    $check->close();

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $insert = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $hashed);

    if ($insert->execute()) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../main-php/index.php';</script>";
    } else {
        echo "<script>alert('Error registering user'); window.location.href='../main-php/index.php';</script>";
    }
    $insert->close();
}
?>

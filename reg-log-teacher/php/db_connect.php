<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testhub";

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4"); // Set character encoding
} catch (Exception $e) {
    error_log($e->getMessage());
    exit("Database connection failed. Please try again later.");
}
?>

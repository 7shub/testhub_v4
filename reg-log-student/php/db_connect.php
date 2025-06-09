<?php
$servername = "localhost";
$username = "root"; // Change if using a different user
$password = ""; // Change if using a set password
$dbname = "testhub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


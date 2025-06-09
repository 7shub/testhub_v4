<?php
require_once "db_connect.php";
$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM student WHERE id = $id");
header("Location: ../admin/manage_users.php");
exit();
?>

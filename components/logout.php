<?php 
require_once "connection.php";
session_start();

$sql = "UPDATE users SET status = FALSE WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$stmt->close();

unset($_SESSION['username']);
unset($_SESSION['user_id']);
unset($_SESSION['password']);
session_destroy();

header("location: home.php");
mysqli_close($conn); 
?>
<?php
session_start();
include("config.php");
// Admin check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
$id = $_GET['id'];
$status = $_GET['status'];
mysqli_query($conn, "UPDATE pickups SET status='$status' WHERE id='$id'");
header("Location: admin_pickups.php");
exit();
?>
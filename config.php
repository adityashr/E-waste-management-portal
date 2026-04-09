<?php
$conn = mysqli_connect("localhost", "root", "", "ewaste_portal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
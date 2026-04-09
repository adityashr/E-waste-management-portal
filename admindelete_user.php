<?php
include("config.php");
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // 1️⃣ Delete user's pickups
    mysqli_query($conn, "DELETE FROM pickups WHERE user_id='$user_id'");

    // 2️⃣ Delete user
    mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");

    // 3️⃣ Auto logout if deleted user is currently logged in
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
        session_destroy(); // Destroy current session
        header("Location: login.php?msg=Your account has been deleted by admin");
        exit();
    }

    // Redirect back to admin users page
    header("Location: admin_users.php");
    exit();
}
?>
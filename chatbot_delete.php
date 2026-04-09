<?php
include "config.php";
session_start();

$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("DELETE FROM chat_messages WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

echo json_encode(["status" => "success"]);
?>
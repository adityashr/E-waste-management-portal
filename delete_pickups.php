<?php
include("config.php");
session_start();
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['ids']) && is_array($data['ids'])) {
    $ids = array_map('intval', $data['ids']); // sanitize
    $ids_list = implode(',', $ids);

    $sql = "DELETE FROM pickups WHERE user_id='$user_id' AND id IN ($ids_list)";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
<?php
include "config.php";

$res = $conn->query("SELECT * FROM chat_messages ORDER BY id ASC");

$messages = [];

while ($row = $res->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
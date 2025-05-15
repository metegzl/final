<?php
require_once "connection.php";

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT user_name, message FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
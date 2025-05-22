<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'] ?? '';
    $user_name = $_POST['user_name'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($session_id && $user_name && $message) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (session_id, user_name, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $session_id, $user_name, $message);
        $stmt->execute();
    }
}

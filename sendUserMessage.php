<?php
require_once "connection.php";

$session_id = $_POST['session_id'] ?? null;
$user_name = trim($_POST['user_name'] ?? '');
$message = trim($_POST['message'] ?? '');

if (preg_match('/mod|admin|yönetici/i', $user_name)) {
    http_response_code(400);
    echo "Geçersiz isim.";
    exit;
}

if (!$session_id || !$user_name || !$message) {
    http_response_code(400);
    echo "Eksik bilgi.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO chat_messages (session_id, user_name, message) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $session_id, $user_name, $message);
$stmt->execute();

echo "OK";

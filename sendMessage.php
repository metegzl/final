<?php
session_start();
require_once "connection.php";

// Parametreleri al
$session_id = $_POST['session_id'] ?? null;
$user_name  = trim($_POST['user_name'] ?? '');
$message    = trim($_POST['message'] ?? '');
$is_mod     = isset($_POST['is_mod']) ? intval($_POST['is_mod']) : 0;

header('Content-Type: application/json');

// Zorunlu alan kontrolü
if (!$session_id || !$user_name || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Eksik bilgi.']);
    exit;
}

// Kullanıcı adı kısıtlaması (mod/yıldız)
if (
    stripos($user_name, 'mod') !== false ||
    strpos($user_name, '★') !== false
) {
    echo json_encode(['success' => false, 'message' => 'Kullanıcı adında "MOD" veya yıldız sembolü kullanamazsınız!']);
    exit;
}

// Mesajı ekle
$stmt = $conn->prepare("INSERT INTO chat_messages (session_id, user_name, message, is_mod) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $session_id, $user_name, $message, $is_mod);
$success = $stmt->execute();
$stmt->close();

echo json_encode(['success' => $success]);

<?php
require_once("connection.php");
$code = $_GET['code'] ?? '';
$stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$res = $stmt->get_result();
echo json_encode(['exists' => $res->num_rows > 0]);

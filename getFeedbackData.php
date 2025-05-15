<?php
require_once("connection.php");

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    echo json_encode(['error' => 'Session ID yok']);
    exit;
}

$stmt = $conn->prepare("
    SELECT feedback_type, COUNT(*) as count 
    FROM panic_feedback 
    WHERE session_id = ? 
    GROUP BY feedback_type
");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [
    'too_fast' => 0,
    'too_slow' => 0,
    'too_quiet' => 0,
    'example' => 0,
    'last_slide' => 0,
    'panic' => 0
];

while ($row = $result->fetch_assoc()) {
    $data[$row['feedback_type']] = (int)$row['count'];
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>
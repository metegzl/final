<?php
require_once("connection.php");

$session_id = $_GET['session_id'] ?? 0;
$modView = isset($_GET['mod']) ? true : false;

$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $is_mod = isset($row['is_mod']) && $row['is_mod'] == 1;
    $user_label = htmlspecialchars($row['user_name']);

    if ($is_mod) {
        $user_label = '<span style="color:#e74c3c;font-weight:bold;">on ★ Mod</span>';
    }

    echo '<div class="message">';
    echo '<b>' . $user_label . '</b>: ';
    echo htmlspecialchars($row['message']);
    echo ' <span style="color:#999; font-size:13px;">(' . $row['created_at'] . ')</span>';

    if ($modView) {
        echo ' <button class="delete-btn" data-id="' . $row['id'] . '">Sil</button>';
    }
    echo '</div>';
}

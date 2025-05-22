<?php
include 'connection.php';

$session_id = $_GET['session_id'] ?? '';

if ($session_id) {
    $stmt = $conn->prepare("SELECT user_name, message, created_at FROM chat_messages WHERE session_id = ? ORDER BY id ASC");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<div class='message'><strong>" . htmlspecialchars($row['user_name']) . ":</strong> " .
            htmlspecialchars($row['message']) . " <small>(" . $row['created_at'] . ")</small></div>";
    }
}

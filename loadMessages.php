<?php
require_once "connection.php";

$session_id = $_GET['session_id'] ?? '';
$is_mod_view = isset($_GET['mod']) && $_GET['mod'] == 1;

if ($session_id) {
    $stmt = $conn->prepare("SELECT id, user_name, message, created_at, is_mod FROM chat_messages WHERE session_id = ? ORDER BY id ASC");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $isMod = $row['is_mod'] == 1;
        $modEtiket = $isMod
            ? '<span style="color:#e03131;font-size:18px;margin-left:4px;">★</span> <span style="color:#14234B;font-size:12px;font-weight:bold;background:#e4eaff;padding:1px 7px;border-radius:5px;margin-left:2px;">Mod</span>'
            : '';
        echo "<div class='message'>";
        echo "<strong>" . htmlspecialchars($row['user_name']) . $modEtiket . ":</strong> ";
        echo htmlspecialchars($row['message']) . " <small>(" . $row['created_at'] . ")</small>";
        if ($is_mod_view) {
            echo " <button class='delete-btn' data-id='" . $row['id'] . "'>Sil</button>";
        }
        echo "</div>";
    }
}

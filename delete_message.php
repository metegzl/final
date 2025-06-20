<?php
require_once("connection.php");

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM chat_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Silindi";
} else {
    echo "Geçersiz istek";
}

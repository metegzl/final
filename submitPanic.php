<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $session_id = $_POST["session_id"] ?? null;
    $feedback_type = $_POST["feedback_type"] ?? null;

    if ($session_id && $feedback_type) {
        $stmt = $conn->prepare("INSERT INTO panic_feedback (session_id, feedback_type) VALUES (?, ?)");
        $stmt->bind_param("is", $session_id, $feedback_type);

        if ($stmt->execute()) {
            echo "Başarıyla gönderildi.";
        } else {
            echo "Hata oluştu.";
        }

        $stmt->close();
    } else {
        echo "Eksik veri.";
    }
}

$conn->close();

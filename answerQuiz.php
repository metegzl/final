<?php
require_once("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = intval($_POST['quiz_id']);
    $user_id = intval($_POST['user_id']);
    $answer = $_POST['answer'];

    // Önceden cevap verdiyse güncelle
    $check = $conn->prepare("SELECT id FROM quiz_answers WHERE quiz_id=? AND user_id=?");
    $check->bind_param("ii", $quiz_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($row = $result->fetch_assoc()) {
        $update = $conn->prepare("UPDATE quiz_answers SET answer=? WHERE id=?");
        $update->bind_param("si", $answer, $row['id']);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO quiz_answers (quiz_id, user_id, answer) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $quiz_id, $user_id, $answer);
        $insert->execute();
    }
    echo "ok";
}

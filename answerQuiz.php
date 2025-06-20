<?php
require_once("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_code = $_POST['session_code'];
    $type = $_POST['type'];
    $question = trim($_POST['question']);
    $correct = $_POST['correct'];

    // session_code'dan session_id çek
    $stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
    $stmt->bind_param("s", $session_code);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$row = $res->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
        exit;
    }
    $session_id = $row['id'];

    // Quiz ekle
    $stmt = $conn->prepare("INSERT INTO quiz (session_id, question, type, correct_answer) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $session_id, $question, $type, $correct);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    // Çoktan seçmeli ise şıkları ekle
    if ($type == "coktan") {
        foreach (['A', 'B', 'C', 'D'] as $key) {
            if (isset($_POST[$key]) && trim($_POST[$key]) != "") {
                $opt = trim($_POST[$key]);
                $stmt2 = $conn->prepare("INSERT INTO quiz_options (quiz_id, option_key, option_text) VALUES (?, ?, ?)");
                $stmt2->bind_param("iss", $quiz_id, $key, $opt);
                $stmt2->execute();
            }
        }
    }
    echo json_encode(['success' => true]);
}

<?php
require_once("connection.php");
$session_code = $_GET['session_code'];
$user_id = $_GET['user_id'];
$stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $session_code);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) exit;
$session_id = $row['id'];

// Quizleri sırala
$quizQ = $conn->prepare("SELECT * FROM quiz WHERE session_id = ? ORDER BY created_at DESC LIMIT 10");
$quizQ->bind_param("i", $session_id);
$quizQ->execute();
$quizRes = $quizQ->get_result();

while ($quiz = $quizRes->fetch_assoc()) {
    echo '<div class="quiz-item">';
    echo '<b>' . htmlspecialchars($quiz['question']) . '</b><br>';
    // User'ın cevabını çek
    $q = $conn->prepare("SELECT answer FROM quiz_answers WHERE quiz_id=? AND user_id=?");
    $q->bind_param("ii", $quiz['id'], $user_id);
    $q->execute();
    $uAns = $q->get_result()->fetch_assoc();
    $selected = $uAns ? $uAns['answer'] : '';

    // Şıklar ve sayaçlar
    if ($quiz['type'] == "coktan") {
        $optQ = $conn->prepare("SELECT * FROM quiz_options WHERE quiz_id = ?");
        $optQ->bind_param("i", $quiz['id']);
        $optQ->execute();
        $opts = $optQ->get_result();

        // Sayaçlar
        $countQ = $conn->prepare("SELECT answer, COUNT(*) as c FROM quiz_answers WHERE quiz_id = ? GROUP BY answer");
        $countQ->bind_param("i", $quiz['id']);
        $countQ->execute();
        $counts = [];
        $r = $countQ->get_result();
        while ($row2 = $r->fetch_assoc()) $counts[$row2['answer']] = $row2['c'];

        while ($opt = $opts->fetch_assoc()) {
            $val = $opt['option_key'];
            $cnt = isset($counts[$val]) ? $counts[$val] : 0;
            $isSel = $selected == $val ? 'selected' : '';
            echo '<button class="option-btn ' . $isSel . '" onclick="sendAnswer(' . $quiz['id'] . ',\'' . $val . '\')">' . htmlspecialchars($val) . ': ' . htmlspecialchars($opt['option_text']) . '<span class="count">' . $cnt . ' kişi</span></button><br>';
        }
    } else {
        $countQ = $conn->prepare("SELECT answer, COUNT(*) as c FROM quiz_answers WHERE quiz_id = ? GROUP BY answer");
        $countQ->bind_param("i", $quiz['id']);
        $countQ->execute();
        $counts = [];
        $r = $countQ->get_result();
        while ($row2 = $r->fetch_assoc()) $counts[$row2['answer']] = $row2['c'];
        foreach (['dogru' => 'Doğru', 'yanlis' => 'Yanlış'] as $key => $label) {
            $cnt = isset($counts[$key]) ? $counts[$key] : 0;
            $isSel = $selected == $key ? 'selected' : '';
            echo '<button class="option-btn ' . $isSel . '" onclick="sendAnswer(' . $quiz['id'] . ',\'' . $key . '\')">' . htmlspecialchars($label) . '<span class="count">' . $cnt . ' kişi</span></button>';
        }
    }
    echo '</div>';
}

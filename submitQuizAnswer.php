<?php
session_start();
require_once("connection.php");

$code = $_GET['code'] ?? '';
if (!$code) die("Oturum kodu eksik.");

$stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$res  = $stmt->get_result();
if (!($row = $res->fetch_assoc())) die("Geçersiz kod");
$session_id = (int)$row['id'];
$stmt->close();

$tokenName = "attendee_token_$session_id";
if (!isset($_COOKIE[$tokenName])) {
    $token = bin2hex(random_bytes(16));
    setcookie($tokenName, $token, time() + 86400, "/");
} else {
    $token = $_COOKIE[$tokenName];
}

$stmt = $conn->prepare("
    SELECT id FROM session_attendees
    WHERE session_id = ? AND user_token = ?
");
$stmt->bind_param("is", $session_id, $token);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $user_id = (int)$row['id'];
} else {
    $ins = $conn->prepare("
        INSERT INTO session_attendees (session_id, user_token)
        VALUES (?, ?)
    ");
    $ins->bind_param("is", $session_id, $token);
    $ins->execute();
    $user_id = $ins->insert_id;
    $ins->close();
}
$stmt->close();

$qQuiz = $conn->prepare("
    SELECT * FROM quiz
    WHERE session_id = ?
    ORDER BY created_at DESC
");
$qQuiz->bind_param("i", $session_id);
$qQuiz->execute();
$quizRes = $qQuiz->get_result();
$quizzes = [];
while ($quiz = $quizRes->fetch_assoc()) $quizzes[] = $quiz;
$qQuiz->close();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f3f3;
            padding: 40px
        }

        .quiz-box {
            background: #fff;
            border-radius: 8px;
            padding: 35px;
            max-width: 420px;
            margin: 45px auto;
            box-shadow: 0 2px 12px #bbb;
            margin-bottom: 35px;
        }

        h2 {
            color: #2d4059
        }

        .answer-btn {
            display: block;
            width: 100%;
            margin-top: 14px;
            padding: 15px 0;
            background: #4285f4;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
            transition: .2s
        }

        .answer-btn:disabled {
            background: #ccc;
            cursor: default
        }

        .chosen {
            background: #4caf50 !important
        }
    </style>
</head>

<body>

    <?php if (empty($quizzes)): ?>
        <div class="quiz-box"><b>Bu oturumda hiç soru yok!</b></div>
    <?php else: ?>
        <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz-box">
                <h2><?= htmlspecialchars($quiz['question']) ?></h2>

                <?php
                $qAns = $conn->prepare("
            SELECT answer FROM quiz_answers
            WHERE quiz_id = ? AND user_id = ?
            LIMIT 1
        ");
                $qAns->bind_param("ii", $quiz['id'], $user_id);
                $qAns->execute();
                $old = $qAns->get_result()->fetch_assoc();
                $qAns->close();
                $options = [];
                if ($quiz['type'] === "coktan") {
                    $optQ = $conn->prepare("
                SELECT option_key, option_text 
                FROM quiz_options 
                WHERE quiz_id = ? 
                ORDER BY option_key
            ");
                    $optQ->bind_param("i", $quiz['id']);
                    $optQ->execute();
                    $optRes = $optQ->get_result();
                    while ($opt = $optRes->fetch_assoc()) {
                        $options[$opt['option_key']] = $opt['option_text'];
                    }
                    $optQ->close();
                }
                ?>

                <?php if ($old): ?>
                    <div style="margin-top:24px;">
                        <b>Cevabınız:</b>
                        <?php
                        if ($quiz['type'] === "coktan") {
                            $k = $old['answer'];
                            echo htmlspecialchars($k . " - " . ($options[$k] ?? ''));
                        } else {
                            echo ($old['answer'] === "dogru" ? "Doğru" : "Yanlış");
                        }
                        ?>
                        <br><span style="color:green;">(Yanıtınız kaydedildi, tekrar değiştiremezsiniz)</span>
                    </div>

                <?php else: ?>
                    <form onsubmit="return false;">
                        <?php if ($quiz['type'] === "coktan"): ?>
                            <?php foreach ($options as $key => $text): ?>
                                <button type="button" class="answer-btn"
                                    onclick="submitQuiz(<?= $quiz['id'] ?>,'<?= htmlspecialchars($key) ?>',this)">
                                    <?= htmlspecialchars($key) . " - " . htmlspecialchars($text) ?>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <button type="button" class="answer-btn" onclick="submitQuiz(<?= $quiz['id'] ?>,'dogru',this)">Doğru</button>
                            <button type="button" class="answer-btn" onclick="submitQuiz(<?= $quiz['id'] ?>,'yanlis',this)">Yanlış</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        function submitQuiz(quiz_id, answer, btn) {
            const parent = btn.parentElement;
            parent.querySelectorAll('.answer-btn').forEach(b => b.disabled = true);

            fetch("submitQuizAnswer.php", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "quiz_id=" + quiz_id + "&answer=" + encodeURIComponent(answer)
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        alert("Cevabınız kaydedildi!");
                        location.reload();
                    } else {
                        alert("Hata: " + (d.message || ''));
                        parent.querySelectorAll('.answer-btn').forEach(b => b.disabled = false);
                    }
                })
        }
    </script>
</body>

</html>
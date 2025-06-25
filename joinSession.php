<?php
require_once("connection.php");

if (!isset($_GET["code"])) {
    echo "Session kodu bulunamadı.";
    exit;
}

$code = $_GET["code"];

$stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('GEÇERSİZ OTURUM KODU!!!'); window.location.href = 'anasayfa.php';</script>";
    exit;
}

$session = $result->fetch_assoc();
$sessionId = $session['id'];
$stmt->close();

if (!isset($_COOKIE["attendee_token_$sessionId"])) {
    $token = bin2hex(random_bytes(16));
    setcookie("attendee_token_$sessionId", $token, time() + 86400, "/");
    $stmt2 = $conn->prepare("INSERT IGNORE INTO session_attendees (session_id, attendee_token) VALUES (?, ?)");
    $stmt2->bind_param("is", $sessionId, $token);
    $stmt2->execute();
    $stmt2->close();
} else {
    $token = $_COOKIE["attendee_token_$sessionId"];
    $stmt2 = $conn->prepare("INSERT IGNORE INTO session_attendees (session_id, attendee_token) VALUES (?, ?)");
    $stmt2->bind_param("is", $sessionId, $token);
    $stmt2->execute();
    $stmt2->close();
}

$stmt = $conn->prepare("SELECT chatwall, quiz FROM sessions WHERE id = ?");
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$result = $stmt->get_result();
$features = [
    "chatwall" => false,
    "quiz" => false
];
if ($row = $result->fetch_assoc()) {
    $features["chatwall"] = (bool)$row["chatwall"];
    $features["quiz"] = (bool)$row["quiz"];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Oturum: <?php echo htmlspecialchars($code); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            background: #f5f1e9;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            color: #333;
        }

        .session-code {
            font-size: 22px;
            color: #555;
        }

        .feature {
            margin-top: 20px;
        }

        .feature a {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            background-color: #4285f4;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
            transition: background 0.3s;
        }

        .feature a:hover {
            background-color: #3367d6;
        }
    </style>
</head>

<body>
    <h1>Oturuma Katıldınız</h1>
    <div class="session-code">Session Kodu: <strong><?php echo htmlspecialchars($code); ?></strong></div>
    <div class="feature">
        <?php if ($features["chatwall"]): ?>
            <a href="userChatwall.php?code=<?php echo urlencode($code); ?>">Chatwall</a>
        <?php endif; ?>
        <?php if ($features["quiz"]): ?>
            <a href="userQuiz.php?code=<?php echo urlencode($code); ?>">Quiz</a>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
require_once("connection.php");

if (!isset($_GET["code"])) {
    echo "Session kodu bulunamadı.";
    exit;
}

$code = $_GET["code"];

// Session ID'yi al
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

// Özellikleri session_features tablosundan al
$stmt = $conn->prepare("SELECT feature_name, is_enabled FROM session_features WHERE session_id = ?");
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$result = $stmt->get_result();

$features = [
    "chatwall" => false,
    "quiz" => false,
    "panic" => false
];

while ($row = $result->fetch_assoc()) {
    $feature = strtolower($row['feature_name']);
    if (isset($features[$feature])) {
        $features[$feature] = (bool)$row['is_enabled'];
    }
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
            <a href="chatwall.php?code=<?php echo urlencode($code); ?>">Chatwall</a>
        <?php endif; ?>

        <?php if ($features["quiz"]): ?>
            <a href="quiz.php?code=<?php echo urlencode($code); ?>">Quiz</a>
        <?php endif; ?>

        <?php if ($features["panic"]): ?>
            <a href="panic.php?code=<?php echo urlencode($code); ?>">Panic</a>
        <?php endif; ?>
    </div>
</body>
</html>
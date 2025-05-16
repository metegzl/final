<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['uye_id'])) {
    echo "<script>alert('Oturum Başlatmak için Önce Giriş Yapmalısınız!!!'); window.location.href = 'anasayfa.php';</script>";
    exit();
}

$createdBy = $_SESSION['uye_id'];

function generateSessionCode($length = 6)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Oturum başlatıldı mı kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chatwall = isset($_POST['chatwall']) ? 1 : 0;
    $quiz = isset($_POST['quiz']) ? 1 : 0;
    $panic = isset($_POST['panic']) ? 1 : 0;

    $sessionCode = generateSessionCode();

    $stmt = $conn->prepare("INSERT INTO sessions (session_code, created_by, chatwall, quiz, panic) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiii", $sessionCode, $createdBy, $chatwall, $quiz, $panic);

    if (!$stmt->execute()) {
        die("Veritabanına kayıt yapılamadı: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Oturum Oluştur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 26px;
        }

        .feature {
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 20px;
            display: flex;
            padding: 20px;
            background-color: #fafafa;
        }

        .feature input {
            margin-right: 20px;
            transform: scale(1.5);
        }

        .feature h3 {
            margin: 0 0 5px 0;
        }

        .code-box {
            margin-top: 40px;
            padding: 20px;
            background: #f3f3f3;
            border: 2px dashed #999;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px;
            color: #333;
        }

        .button {
            display: block;
            margin: 30px auto 0;
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .end-button {
            background-color: #d9534f;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 style="font-size: 185%;">Choose your features</h1>
        <p>Select which features you want to enable for your session:</p>

        <form method="post">
            <div class="feature">
                <input type="checkbox" id="chatwall" name="chatwall" unchecked>
                <div>
                    <h3 style="font-size: 160%;">Chatwall</h3>
                    <p>Participants can communicate issues like "too fast", "example please", etc.</p>
                </div>
            </div>

            <div class="feature">
                <input type="checkbox" id="quiz" name="quiz" unchecked>
                <div>
                    <h3 style="font-size: 160%;">Quiz</h3>
                    <p>Enables the speaker to direct a single-choice question to the audience.</p>
                </div>
            </div>

            <div class="feature">
                <input type="checkbox" id="panic" name="panic" unchecked>
                <div>
                    <h3 style="font-size: 160%;">Panic-Buttons</h3>
                    <p>Participants can communicate issues like "too fast", "example please", etc.</p>
                </div>
            </div>

            <button type="submit" class="button">Oturumu Başlat</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="code-box">
                Oturum Kodu: <?php echo htmlspecialchars($sessionCode); ?>
            </div>

            <form action="endSession.php" method="post" style="text-align: center;">
                <input type="hidden" name="session_code" value="<?php echo htmlspecialchars($sessionCode); ?>">
                <button type="submit" class="button end-button">Oturumu Sonlandır</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
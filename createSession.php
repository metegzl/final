<?php
session_start();
require_once("connection.php");

// Giriş yapılmış mı kontrolü
if (!isset($_SESSION['uye_id'])) {
    die("Oturum oluşturmak için giriş yapmalısınız.");
}

$createdBy = $_SESSION['uye_id']; // oturumu oluşturan kullanıcı

// 6 haneli random session kodu oluştur
function generateSessionCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

$sessionCode = generateSessionCode();

// Veritabanına kayıt et (created_by eklendi)
$stmt = $conn->prepare("INSERT INTO sessions (session_code, created_by) VALUES (?, ?)");
$stmt->bind_param("si", $sessionCode, $createdBy);

if (!$stmt->execute()) {
    die("Veritabanına kayıt yapılamadı: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Session</title>
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>
<div class="container">
    <h1 style="font-size: 185%;">Choose your features</h1>
    <p>Select which features you want to enable for your session:</p>

    <div class="feature">
        <input type="checkbox" id="Chatwall" checked enabled>
        <div>
            <h3 style="font-size: 160%;">Chatwall</h3>
            <p>Participants can communicate issues like "too fast", "example please", etc.</p>
        </div>
    </div>

    <div class="feature">
        <input type="checkbox" id="quiz" checked enabled>
        <div>
            <h3 style="font-size: 160%;">Quiz</h3>
            <p>Enables the speaker to direct a single-choice question to the audience.</p>
        </div>
    </div>

    <div class="feature">
        <input type="checkbox" id="panic" checked enabled>
        <div>
            <h3 style="font-size: 160%;">Panic-Buttons</h3>
            <p>Participants can communicate issues like "too fast", "example please", etc.</p>
        </div>
    </div>

    <div class="code-box">
        Session Code: <?php echo $sessionCode; ?>
    </div>
</div>
<form action="endSession.php" method="post" style="margin-top: 20px; text-align: center;">
    <input type="hidden" name="session_code" value="<?php echo $sessionCode; ?>">
    <button type="submit" style="padding: 10px 20px; background-color: #d9534f; color: white; border: none; border-radius: 6px; cursor: pointer;">
        End Session
    </button>
</form>
</body>
</html>
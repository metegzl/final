<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['uye_id'])) {
    echo "<script>alert('Oturum Başlatmak için Önce Giriş Yapmalısınız!!!'); window.location.href = 'anasayfa.php';</script>";
    exit();
}

$createdBy = $_SESSION['uye_id'];
$sessionCode = null;

// Aktif oturumu kontrol et
$checkStmt = $conn->prepare("SELECT session_code FROM sessions WHERE created_by = ? AND is_active = 1");
$checkStmt->bind_param("i", $createdBy);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($row = $result->fetch_assoc()) {
    $sessionCode = $row['session_code'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Yeni oturum oluşturulabilir

    function generateSessionCode($length = 6)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    $chatwall = isset($_POST['chatwall']) ? 1 : 0;
    $quiz = isset($_POST['quiz']) ? 1 : 0;
    $panic = isset($_POST['panic']) ? 1 : 0;
    $sessionCode = generateSessionCode();

    $stmt = $conn->prepare("INSERT INTO sessions (session_code, created_by, chatwall, quiz, panic, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("siiii", $sessionCode, $createdBy, $chatwall, $quiz, $panic);

    if (!$stmt->execute()) {
        die("Veritabanına kayıt yapılamadı: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <title>Oturum Oluştur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row-reverse;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: #f47c2c;
        }

        .logo-icon {
            font-size: 35px;
            margin-right: 5px;
            line-height: 1;
        }

        .logo-button {
            display: inline-block;
            background-color: rgba(244, 124, 44, 0.82);
            color: whitesmoke;
            padding: 5px 10px;
            margin-left: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .logo-button:hover {
            background-color: rgb(0, 62, 71);
        }

        .sidebar {
            width: 300px;
            background-color: rgb(61, 131, 184);
            border-right: 1px solid #ddd;
            padding: 30px 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            height: 100vh;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
            color: #bbb;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .sidebar ul li::before {
            content: '•';
            margin-right: 8px;
            color: #bbb;
        }

        .main-container {
            flex-grow: 1;
            padding: 40px;
            margin-right: 270px;
            margin-left: 270px;
        }

        .container {
            background-color: #eee9e9;
            color: #333;
            border-left: 8px solid #4285f4;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 50px 0;
            font-size: 15px;
            line-height: 1.6;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 26px;
        }

        .feature {
            border: 2px solid #ccc;
            border-radius: 10px;
            margin-top: 20px;
            display: flex;
            padding: 20px;
            background-color: #fafafa;
        }

        .feature input {
            margin-right: 30px;
            transform: scale(2);
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

        a {
            display: block;
            margin-bottom: 15px;
            color: #007BFF;
            text-decoration: none;
            font-size: 30px;
        }

        a:hover {
            color: #0056b3;
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

        .menu {
            width: 100%;
            border-collapse: collapse;
        }

        .menu td {
            padding: 10px;
        }

        .menu a {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: left;
            border: 3px solid #ccc;
            border-radius: 10px;
            text-decoration: none;
            background-color: #fff;
            font-weight: bold;
            box-sizing: border-box;
            margin-bottom: 3px;
        }

        .menu a:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px" height="55px" class="logo-icon" style="margin-left: 7px; margin-bottom: 50px;" />
            <a href="anasayfa.php" class="logo-button" style="margin-bottom: 50px;">QuestionLive</a>
        </div>

        <div class="menu">
            <table class="menu">
                <tr>
                    <td><a href="chatwall.php">💬 Chatwall</a></td>
                </tr>
                <tr>
                    <td><a href="quiz.php">❔ Quiz</a></td>
                </tr>
                <tr>
                    <td><a href="panic.php">❕ Panic</a></td>
                </tr>
                <tr>
                    <td><a href="createSession.php">🎓 Session</a></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="main-container">
        <?php if ($sessionCode === null): ?>
            <div class="container">
                <h1 style="font-size: 185%;">ÖZELLİKLERİ SEÇİN</h1>
                <p>Tüm özellikler devre dışıdır. Neyi etkinleştireceğinizi seçebilir ve daha sonra "Başlamama izin ver!" düğmesiyle başlayabilirsiniz.</p>


                <form method="post">
                    <div class="feature">
                        <input type="checkbox" id="chatwall" name="chatwall" />
                        <div>
                            <h3 style="font-size: 160%;">Chatwall</h3>
                            <p>Katılımcıların oturum sırasında konuşmacıya soru yöneltmelerine olanak tanır.</p>
                        </div>
                    </div>

                    <div class="feature">
                        <input type="checkbox" id="quiz" name="quiz" />
                        <div>
                            <h3 style="font-size: 160%;">Quiz</h3>
                            <p>Konuşmacının izleyicilere tek seçenekli bir soru yöneltmesini sağlar.</p>
                        </div>
                    </div>

                    <div class="feature">
                        <input type="checkbox" id="panic" name="panic" />
                        <div>
                            <h3 style="font-size: 160%;">Panic-Buttons</h3>
                            <p>Katılımcılar "çok hızlı", "lütfen örnek verin" gibi bildirimlerde bulunabilir.</p>
                        </div>
                    </div>

                    <button type="submit" class="button">Oturumu Başlat</button>
                </form>
            <?php endif; ?>

            <?php if ($sessionCode !== null): ?>
                <div class="code-box">
                    Aktif Oturum Kodu: <?php echo htmlspecialchars($sessionCode); ?>
                </div>
                <form action="endSession.php" method="post" style="text-align: center;">
                    <input type="hidden" name="session_code" value="<?php echo htmlspecialchars($sessionCode); ?>">
                    <button type="submit" class="button end-button">Oturumu Sonlandır</button>
                </form>
            <?php endif; ?>
            </div>
    </div>
</body>

</html>
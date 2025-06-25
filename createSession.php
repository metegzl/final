<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['uye_id'])) {
    echo "<script>alert('Oturum Başlatmak için Önce Giriş Yapmalısınız!!!'); window.location.href = 'anasayfa.php';</script>";
    exit();
}

$createdBy = $_SESSION['uye_id'];
$sessionCode = null;

$checkStmt = $conn->prepare("SELECT session_code FROM sessions WHERE created_by = ? AND is_active = 1");
$checkStmt->bind_param("i", $createdBy);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($row = $result->fetch_assoc()) {
    $sessionCode = $row['session_code'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $sessionCode = generateSessionCode();
    $_SESSION['current_session_code'] = $sessionCode;

    $stmt = $conn->prepare(
            "INSERT INTO sessions (session_code, created_by, chatwall, quiz, is_active) VALUES (?, ?, ?, ?, 1)"
        );
    $stmt->bind_param("siii", $sessionCode, $createdBy, $chatwall, $quiz);

    if (!$stmt->execute()) {
        die("Veritabanına kayıt yapılamadı: " . $stmt->error);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <title>Oturum Oluştur</title>
    <style>
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #faebd7;
            display: flex;
        }

        .sidebar {
            width: 390px;
            background-color: rgb(61, 131, 184);
            border-right: 1px solid #ddd;
            padding: 30px 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: #f47c2c;
            margin-bottom: 60px;
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
            transition: background-color 0.3s;
            font-size: 28px;
        }

        .logo-button:hover {
            background-color: rgb(0, 62, 71);
        }

        .mod-label {
            color: #14234B;
            font-weight: bold;
            font-size: 1em;
            margin-left: 16px;
            background: #d6e4ff;
            padding: 4px 14px;
            border-radius: 8px;
            letter-spacing: 1px;
        }

        .menu {
            width: 100%;
            border-collapse: collapse;
        }

        .menu td {
            padding: 10px;
        }

        .menu a {
            font-size: 30px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 3px solid #ccc;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
            box-sizing: border-box;
            text-decoration: none;
            font-weight: bold;
            color: #007BFF;
            transition: background .2s, box-shadow .2s;
        }

        .menu a:hover {
            background: #e0e0e0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .35);
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 160px;
                padding: 15px 7px;
            }

            .logo-button {
                font-size: 18px;
            }

            .mod-label {
                font-size: .92em;
                padding: 3px 8px;
                margin-left: 8px;
            }

            .menu a {
                font-size: 18px;
                padding: 10px;
            }
        }

        .main-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 40px;
            overflow-y: auto;
            height: 100vh;
            box-sizing: border-box;
        }

        .container {
            background-color: #eee9e9;
            color: #333;
            border-left: 8px solid rgb(244, 241, 66);
            padding: 28px 40px;
            border-radius: 12px;
            margin: 0 auto;
            font-size: 17px;
            line-height: 1.6;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.07);
            width: 100%;
            max-width: 540px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-top: 50px;
        }

        h1 {
            font-size: 28px;
            font-weight: bold;
            color: #14234B;
        }

        .feature {
            border: 2px solid #ccc;
            border-radius: 10px;
            margin-top: 20px;
            display: flex;
            padding: 20px;
            background-color: #fafafa;
            align-items: center;
        }

        .feature input {
            margin-right: 30px;
            transform: scale(2);
        }

        .feature h3 {
            margin: 0 0 5px 0;
        }

        .feature p {
            margin: 0;
            font-size: 0.98em;
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
            padding: 12px 36px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .button:hover {
            background-color: #389638;
        }

        .end-button {
            background-color: #d9534f;
            margin-top: 32px;
        }

        .end-button:hover {
            background: #a52823;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px" height="55px" class="logo-icon" style="margin-left: 7px;" />
            <a href="anasayfa.php" class="logo-button">QuestionLive</a>
            <span class="mod-label">Mod</span>
        </div>
        <div class="menu">
            <table class="menu">
                <tr>
                    <td><a href="modChatwall.php">💬 Chat</a></td>
                </tr>
                <tr>
                    <td><a href="modQuiz.php">❔ Quiz</a></td>
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
                <h1>ÖZELLİKLERİ SEÇİN</h1>
                <p style="margin-bottom: 24px;">Tüm özellikler devre dışıdır. Neyi etkinleştireceğinizi seçebilir ve daha sonra <b>Oturumu Başlat</b> düğmesiyle başlayabilirsiniz.</p>
                <form method="post">
                    <div class="feature">
                        <input type="checkbox" id="chatwall" name="chatwall" />
                        <div>
                            <h3 style="font-size: 112%;">Chatwall</h3>
                            <p>Katılımcıların oturum sırasında konuşmacıya soru yöneltmelerine olanak tanır.</p>
                        </div>
                    </div>
                    <div class="feature">
                        <input type="checkbox" id="quiz" name="quiz" />
                        <div>
                            <h3 style="font-size: 112%;">Quiz</h3>
                            <p>Konuşmacının izleyicilere tek seçenekli bir soru yöneltmesini sağlar.</p>
                        </div>
                    </div>
                    <button type="submit" class="button">Oturumu Başlat</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($sessionCode !== null): ?>
            <div class="container">
                <div class="code-box">
                    Aktif Oturum Kodu: <?php echo htmlspecialchars($sessionCode); ?>
                </div>
                <?php
                $stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
                $stmt->bind_param("s", $sessionCode);
                $stmt->execute();
                $result = $stmt->get_result();
                $sid = null;
                if ($row = $result->fetch_assoc()) {
                    $sid = $row['id'];
                }
                $stmt->close();
                $count = 0;
                if ($sid) {
                    $stmt2 = $conn->prepare("SELECT COUNT(*) as cnt FROM session_attendees WHERE session_id = ?");
                    $stmt2->bind_param("i", $sid);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    if ($row2 = $result2->fetch_assoc()) {
                        $count = $row2['cnt'];
                    }
                    $stmt2->close();
                }
                echo "<div style='margin-top:22px;font-size:20px;color:#1a237e;font-weight:bold;'>Katılımcı Sayısı: $count</div>";
                ?>
                <form action="endSession.php" method="post" style="text-align: center;">
                    <input type="hidden" name="session_code" value="<?php echo htmlspecialchars($sessionCode); ?>">
                    <button type="submit" class="button end-button">Oturumu Sonlandır</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php
$conn->close();
?>
<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['uye_id'])) {
    die("Giriş yapmalısınız.");
}

$createdBy = $_SESSION['uye_id'];
$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    $stmt = $conn->prepare("SELECT session_code FROM sessions WHERE created_by = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param("i", $createdBy);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $session_id = $row['session_code'];
    } else {
        die("Aktif oturum bulunamadı.");
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Panic Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row;
            height: 100vh;
            overflow: hidden;
        }

        .main-container {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .button-grid {
            display: grid;
            grid-template-columns: repeat(3, 180px);
            gap: 20px;
        }

        .panic-button {
            background-color: #f0b37e;
            border: none;
            border-radius: 6px;
            padding: 30px;
            text-align: center;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }

        .panic-button:hover {
            background-color: #e9984f;
        }

        .sidebar {
            width: 300px;
            background-color: rgb(61, 131, 184);
            border-left: 1px solid #ddd;
            padding: 30px 15px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05);
            height: 100vh;
            position: relative;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: #f47c2c;
            margin-bottom: 50px;
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
        }

        .logo-button:hover {
            background-color: rgb(0, 62, 71);
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

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="button-grid">
            <button class="panic-button" onclick="sendFeedback('too_fast')">Too fast</button>
            <button class="panic-button" onclick="sendFeedback('too_slow')">Too slow</button>
            <button class="panic-button" onclick="sendFeedback('too_quiet')">Too quiet</button>
            <button class="panic-button" onclick="sendFeedback('example')">An example please</button>
            <button class="panic-button" onclick="sendFeedback('last_slide')">Last slide again</button>
            <button class="panic-button" onclick="sendFeedback('panic')">Panic</button>
        </div>
    </div>

    <div class="sidebar">
        <div class="logo">
            <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px" height="55px" class="logo-icon" style="margin-left: 7px;" />
            <a href="anasayfa.php" class="logo-button">QuestionLive</a>
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

    <script>
        function sendFeedback(type) {
            const sessionId = new URLSearchParams(window.location.search).get('session_id');
            if (!sessionId) {
                alert("Session ID bulunamadı.");
                return;
            }

            fetch('submit_panic.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `session_id=${encodeURIComponent(sessionId)}&feedback_type=${encodeURIComponent(type)}`
                })
                .then(response => response.text())
                .then(data => {
                    alert("Geri bildiriminiz gönderildi!");
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert("Gönderim sırasında bir hata oluştu.");
                });
        }
    </script>
</body>

</html>
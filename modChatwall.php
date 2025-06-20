<?php
session_start();
require_once("connection.php");

/* Oturum kodu yoksa */
if (!isset($_SESSION['current_session_code'])) {
    echo "<script>
            alert('Oturum kodu belirtilmedi.');
            window.location.href = 'createSession.php';
          </script>";
    exit;
}

$sessionCode = $_SESSION['current_session_code'];

/* chatwall yetkisini sorgula */
$stmt = $conn->prepare("SELECT chatwall FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $sessionCode);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['chatwall'] != 1) {
        echo "<script>
                alert('Bu özellik bu oturumda aktif değil.');
                window.location.href = 'createSession.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Geçersiz oturum kodu.');
            window.location.href = 'createSession.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>ChatWall (Mod)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row;
            min-height: 100vh;
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

        .main-container {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #chat-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        #chat-box {
            width: 70%;
            height: 500px;
            border: 2px solid #ccc;
            overflow-y: scroll;
            padding: 20px;
            background-color: rgb(255, 252, 240);
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .message {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .delete-btn {
            background: #222a50;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 5px 12px;
            cursor: pointer;
            margin-left: 12px;
            font-size: 0.98em;
            transition: background 0.15s;
        }

        .delete-btn:hover {
            background: #f47c2c;
        }

        #chat-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            width: 65%;
            margin-bottom: 100px;
        }

        #chat-form input {
            padding: 10px;
            font-size: 16px;
            width: 30%;
        }

        #chat-form button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        #chat-form button:hover {
            background-color: #4cae4c;
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
        <h2>Chat - Oturum: <?php echo htmlspecialchars($sessionCode); ?></h2>
        <div id="chat-container">
            <div id="chat-box"></div>
            <form id="chat-form">
                <input type="text" id="user_name" placeholder="Adınız" required>
                <input type="text" id="message" placeholder="Mesajınız" required>
                <button type="submit">Gönder</button>
            </form>
        </div>
    </div>
    <script>
        const sessionId = "<?php echo htmlspecialchars($sessionCode); ?>";

        function loadMessages() {
            fetch('loadMessages.php?session_id=' + sessionId + '&mod=1')
                .then(res => res.text())
                .then(data => {
                    const box = document.getElementById('chat-box');
                    box.innerHTML = data;
                    box.scrollTop = box.scrollHeight;
                });
        }
        loadMessages();
        setInterval(loadMessages, 3000);
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('user_name').value.trim();
            const msg = document.getElementById('message').value.trim();
            // JS kontrol: isimde mod veya yıldız olmasın
            if (name.toLowerCase().includes('mod') || name.includes('★')) {
                alert('Kullanıcı adında MOD veya yıldız sembolü kullanamazsınız!');
                return;
            }
            fetch('sendMessage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'session_id=' + encodeURIComponent(sessionId) +
                        '&user_name=' + encodeURIComponent(name) +
                        '&message=' + encodeURIComponent(msg) +
                        '&is_mod=1'
                }).then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        document.getElementById('message').value = '';
                        loadMessages();
                    } else {
                        alert(resp.message || "Bir hata oluştu.");
                    }
                });
        });
        // Sil butonları için
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                const msgId = e.target.getAttribute('data-id');
                if (confirm('Mesajı silmek istediğine emin misin?')) {
                    fetch('deleteMessage.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'message_id=' + encodeURIComponent(msgId) + '&session_id=' + encodeURIComponent(sessionId)
                    }).then(() => {
                        loadMessages();
                    });
                }
            }
        });
    </script>
</body>

</html>
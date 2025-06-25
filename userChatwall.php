<?php
session_start();
require_once("connection.php");

$code = $_GET['code'] ?? null;
if (!$code) {
    die("Oturum kodu eksik.");
}
$sessionCode = $code;

$stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Geçersiz oturum kodu.");
}
$row = $result->fetch_assoc();
$sessionId = $row['id'];
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
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Chatwall</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        .main-container {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100vw;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #leave-session-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px;
        }

        #leave-session-btn:hover {
            background-color: #c9302c;
        }

        #username-select-form {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        #username-input {
            padding: 10px;
            font-size: 16px;
            width: 200px;
        }

        #set-username-btn {
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        #set-username-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        #username-locked-msg {
            display: none;
            margin-bottom: 22px;
            color: #333;
            font-weight: bold;
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
            justify-content: flex-start;
        }

        #chat-form {
            display: none;
            justify-content: center;
            gap: 15px;
            width: 65%;
            margin-bottom: 100px;
        }

        #chat-form input,
        #chat-form button {
            padding: 10px;
            font-size: 16px;
        }

        #chat-form input {
            width: 70%;
        }

        #chat-form button {
            width: 25%;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        #chat-form button:hover {
            background-color: #4cae4c;
        }

        .star {
            color: #e53935;
            font-size: 18px;
            margin-left: 5px;
        }

        .disabled {
            background-color: #ddd !important;
            cursor: not-allowed !important;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h2>Chatwall - Oturum: <?php echo htmlspecialchars($sessionCode); ?></h2>
        <button id="leave-session-btn">Oturumdan Ayrıl</button>
        <form id="username-select-form" autocomplete="off">
            <input type="text" id="username-input" maxlength="32" placeholder="Adınızı girin..." required>
            <button type="submit" id="set-username-btn">Seç</button>
        </form>
        <div id="username-locked-msg"></div>
        <div id="chat-container">
            <div id="chat-box"></div>
            <form id="chat-form" autocomplete="off">
                <input type="text" id="message" maxlength="255" placeholder="Mesajınız" required>
                <button type="submit">Gönder</button>
            </form>
        </div>
    </div>
    <script>
        const sessionId = "<?php echo htmlspecialchars($sessionId); ?>";
        const usernameKey = "cw_username_" + sessionId;
        const leaveBtn = document.getElementById('leave-session-btn');

        leaveBtn.addEventListener('click', function() {
            if (confirm('Oturumdan ayrılmak istediğinize emin misiniz?')) {
                // Remove attendee cookie
                document.cookie = `attendee_token_${sessionId}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/`;
                // Clear stored username
                localStorage.removeItem(usernameKey);
                // Redirect to homepage or session selection
                window.location.href = 'anasayfa.php';
            }
        });

        window.addEventListener('DOMContentLoaded', function() {
            let uname = localStorage.getItem(usernameKey);
            if (uname) {
                lockUsername(uname);
            }
        });

        document.getElementById('username-select-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let uname = document.getElementById('username-input').value.trim();
            if (!uname || /mod|admin|yönetici|★/i.test(uname)) {
                alert("Bu ismi kullanamazsınız!");
                return;
            }
            if (uname.length < 2 || uname.length > 32) {
                alert("Adınız 2-32 karakter arası olmalı!");
                return;
            }
            localStorage.setItem(usernameKey, uname);
            lockUsername(uname);
        });

        function lockUsername(uname) {
            document.getElementById('username-select-form').style.display = "none";
            document.getElementById('username-locked-msg').style.display = "block";
            document.getElementById('username-locked-msg').innerText = "Kullanıcı adınız: " + uname;
            document.getElementById('chat-form').style.display = "flex";
        }

        function loadMessages() {
            fetch('loadUserMessages.php?session_id=' + sessionId)
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
            let uname = localStorage.getItem(usernameKey);
            if (!uname) {
                alert("Önce adınızı seçmelisiniz!");
                return;
            }
            let msg = document.getElementById('message').value.trim();
            if (!msg) return;

            fetch('sendUserMessage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'session_id=' + encodeURIComponent(sessionId) +
                    '&user_name=' + encodeURIComponent(uname) +
                    '&message=' + encodeURIComponent(msg)
            }).then(() => {
                document.getElementById('message').value = '';
                loadMessages();
            });
        });

        // Periodically check if session is alive
        function checkSessionAlive() {
            fetch('isSessionAlive.php?code=<?php echo htmlspecialchars($sessionCode); ?>')
                .then(r => r.json())
                .then(data => {
                    if (!data.exists) {
                        document.body.innerHTML = `
                        <div style="text-align:center;padding:120px;font-size:2rem;color:#c00;">
                          Oturum sonlandırıldı. Ana sayfaya yönlendiriliyorsunuz...
                        </div>`;
                        setTimeout(function() {
                            window.location.href = 'anasayfa.php';
                        }, 3000);
                    }
                });
        }
        setInterval(checkSessionAlive, 2500);
    </script>
</body>

</html>
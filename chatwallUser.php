<?php
$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    echo "Oturum kodu belirtilmedi.";
    exit;
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
            background-color: #f4f4f4;
            padding: 20px;
        }

        #chatBox {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            overflow-y: scroll;
            background: white;
            padding: 10px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<h2>Chatwall (Oturum: <?= htmlspecialchars($session_id) ?>)</h2>

<div id="chatBox"></div>

<form id="messageForm">
    <input type="text" id="user_name" placeholder="Adınız" required>
    <input type="text" id="message" placeholder="Mesajınız" required>
    <button type="submit">Gönder</button>
</form>

<script>
    const sessionId = <?= json_encode($session_id) ?>;

    function loadMessages() {
        fetch(`get_chat_messages.php?session_id=${sessionId}`)
            .then(res => res.json())
            .then(data => {
                const chatBox = document.getElementById("chatBox");
                chatBox.innerHTML = "";
                data.forEach(msg => {
                    const div = document.createElement("div");
                    div.textContent = `${msg.user_name}: ${msg.message}`;
                    chatBox.appendChild(div);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    document.getElementById("messageForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const userName = document.getElementById("user_name").value;
        const message = document.getElementById("message").value;

        fetch("submit_message.php", {
            method: "POST",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `session_id=${sessionId}&user_name=${encodeURIComponent(userName)}&message=${encodeURIComponent(message)}`
        })
        .then(res => res.text())
        .then(() => {
            document.getElementById("message").value = "";
            loadMessages();
        });
    });

    // Her saniyede bir mesajları yenile
    setInterval(loadMessages, 1000);
    loadMessages();
</script>

</body>
</html>
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
    <title>Chatwall - Moderatör</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 30px;
        }

        #chatBox {
            width: 100%;
            height: 400px;
            border: 2px solid #999;
            background-color: #fff;
            overflow-y: scroll;
            padding: 15px;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 12px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .message span {
            font-weight: bold;
        }

        .delete-btn {
            float: right;
            color: red;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <h2>Chatwall - Moderatör (Oturum: <?= htmlspecialchars($session_id) ?>)</h2>

    <div id="chatBox"></div>

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
                        div.classList.add("message");
                        div.innerHTML = `<span>${msg.user_name}:</span> ${msg.message} 
                        <span class="delete-btn" onclick="deleteMessage(${msg.id})">Sil</span>`;
                        chatBox.appendChild(div);
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        }

        function deleteMessage(messageId) {
            if (confirm("Bu mesajı silmek istediğinize emin misiniz?")) {
                fetch("delete_message.php", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${messageId}`
                    })
                    .then(res => res.text())
                    .then(() => loadMessages());
            }
        }

        setInterval(loadMessages, 1000);
        loadMessages();
    </script>

</body>

</html>
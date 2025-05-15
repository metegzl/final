<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Panic Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 40px;
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
    </style>
</head>
<body>
    <div class="button-grid">
        <button class="panic-button" onclick="sendFeedback('too_fast')">Too fast</button>
        <button class="panic-button" onclick="sendFeedback('too_slow')">Too slow</button>
        <button class="panic-button" onclick="sendFeedback('too_quiet')">Too quiet</button>
        <button class="panic-button" onclick="sendFeedback('example')">An example please</button>
        <button class="panic-button" onclick="sendFeedback('last_slide')">Last slide again</button>
        <button class="panic-button" onclick="sendFeedback('panic')">Panic</button>
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
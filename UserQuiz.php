<?php
session_start();
require_once("connection.php");
if (!isset($_SESSION['uye_id'])) {
    echo "<script>alert('Giriş yapmalısınız!');window.location.href='anasayfa.php';</script>";
    exit;
}
$user_id = $_SESSION['uye_id'];
$sessionCode = $_SESSION['current_session_code'];
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        h2 {
            margin-bottom: 40px;
        }

        #quiz-list {
            margin-top: 32px;
        }

        .quiz-item {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .10);
            padding: 22px 18px;
        }

        .option-btn {
            background: #4285f4;
            color: #fff;
            font-size: 17px;
            padding: 10px 20px;
            margin: 5px 7px 0 0;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            min-width: 100px;
        }

        .option-btn .count {
            font-size: 15px;
            background: #fff;
            color: #4285f4;
            border-radius: 20px;
            padding: 2px 10px;
            margin-left: 8px;
        }

        .option-btn.selected {
            background: #333;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h2>Quiz</h2>
        <div id="quiz-list"></div>
    </div>
    <script>
        function loadQuizList() {
            fetch('userQuizList.php?session_code=<?php echo htmlspecialchars($sessionCode); ?>&user_id=<?php echo $user_id; ?>')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('quiz-list').innerHTML = html;
                });
        }
        window.loadQuizList = loadQuizList; // callback için
        loadQuizList();
        setInterval(loadQuizList, 3000);

        function sendAnswer(quiz_id, val) {
            fetch('answerQuiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'quiz_id=' + quiz_id + '&answer=' + encodeURIComponent(val) + '&user_id=<?php echo $user_id; ?>'
            }).then(res => res.text()).then(function(r) {
                loadQuizList();
            });
        }
    </script>
</body>

</html>
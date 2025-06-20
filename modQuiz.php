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
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Quiz (Mod)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #faebd7;
            margin: 0;
            padding: 0;
            display: flex;
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
            align-items: center;
            justify-content: flex-start;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #add-question-btn {
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 22px;
            padding: 18px 38px;
            cursor: pointer;
            margin-top: 30px;
        }

        #add-question-btn:hover {
            background: #409a40;
        }

        #question-form,
        #select-type {
            display: none;
            margin: 24px 0;
            background: #f2f6fa;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(61, 131, 184, 0.06);
        }

        #question-form input[type="text"],
        #question-form textarea {
            width: 98%;
            padding: 10px;
            margin-bottom: 14px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #bbb;
        }

        .options-row {
            display: flex;
            gap: 8px;
        }

        .options-row input[type="text"] {
            width: 85%;
        }

        .answer-radio {
            margin-right: 6px;
        }

        .submit-btn {
            background: #ff8500;
            color: #fff;
            font-weight: bold;
            padding: 12px 38px;
            border-radius: 7px;
            font-size: 19px;
            border: none;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #e06309;
        }

        #quiz-list {
            margin-top: 32px;
            width: 100%;
            max-width: 600px;
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
        <h2>Quiz - Oturum: <?php echo htmlspecialchars($sessionCode); ?></h2>
        <button id="add-question-btn">Soru Ekle</button>
        <div id="select-type">
            <label>
                <input type="radio" name="question-type" value="coktan" checked> Çoktan Seçmeli
            </label>
            <label style="margin-left: 32px;">
                <input type="radio" name="question-type" value="dogruyanlis"> Doğru / Yanlış
            </label>
            <br>
            <button id="continue-btn" class="submit-btn" style="margin-top:18px;">Devam Et</button>
        </div>
        <form id="question-form">
            <div id="question-area"></div>
            <button type="submit" class="submit-btn">Gönder</button>
        </form>
        <div id="quiz-list"></div>
    </div>
    <script>
        const addBtn = document.getElementById('add-question-btn');
        const selectType = document.getElementById('select-type');
        const questionForm = document.getElementById('question-form');
        const questionArea = document.getElementById('question-area');
        let currentType = 'coktan';

        addBtn.onclick = function() {
            addBtn.style.display = "none";
            selectType.style.display = "block";
        }
        document.getElementById('continue-btn').onclick = function() {
            selectType.style.display = "none";
            questionForm.style.display = "block";
            currentType = document.querySelector('input[name="question-type"]:checked').value;
            showQuestionForm(currentType);
        }

        function showQuestionForm(type) {
            let html = `<textarea id="soru" placeholder="Soru yazınız..." required></textarea>`;
            if (type === 'coktan') {
                html += `
        <div class="options-row"><input type="text" id="optA" placeholder="A şıkkı" required> <input type="radio" name="correct" value="A" class="answer-radio" checked>Doğru</div>
        <div class="options-row"><input type="text" id="optB" placeholder="B şıkkı" required> <input type="radio" name="correct" value="B" class="answer-radio">Doğru</div>
        <div class="options-row"><input type="text" id="optC" placeholder="C şıkkı"> <input type="radio" name="correct" value="C" class="answer-radio">Doğru</div>
        <div class="options-row"><input type="text" id="optD" placeholder="D şıkkı"> <input type="radio" name="correct" value="D" class="answer-radio">Doğru</div>
        `;
            } else {
                html += `<div class="options-row" style="margin-top:15px;">
            <label><input type="radio" name="correct" value="dogru" checked> Doğru</label>
            <label style="margin-left:40px;"><input type="radio" name="correct" value="yanlis"> Yanlış</label>
        </div>`;
            }
            questionArea.innerHTML = html;
        }
        questionForm.onsubmit = function(e) {
            e.preventDefault();
            let data = new FormData();
            data.append('session_code', "<?php echo htmlspecialchars($sessionCode); ?>");
            data.append('type', currentType);
            data.append('question', document.getElementById('soru').value);
            data.append('correct', document.querySelector('input[name="correct"]:checked').value);
            if (currentType === 'coktan') {
                data.append('A', document.getElementById('optA').value);
                data.append('B', document.getElementById('optB').value);
                data.append('C', document.getElementById('optC').value);
                data.append('D', document.getElementById('optD').value);
            }
            fetch('addQuiz.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.success) {
                        questionForm.style.display = "none";
                        addBtn.style.display = "block";
                        loadQuizList();
                    } else {
                        alert("Soru eklenemedi: " + (resp.message || ""));
                    }
                });
        };

        function loadQuizList() {
            fetch('loadQuizList.php?session_code=<?php echo htmlspecialchars($sessionCode); ?>&mod=1')
                .then(res => res.text())
                .then(html => {
                    document.getElementById('quiz-list').innerHTML = html;
                });
        }
        loadQuizList();
        setInterval(loadQuizList, 3000);
    </script>
</body>

</html>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcı çıkışa onay verdi
    $_SESSION = array();
    session_destroy();
    header("Location: anasayfa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Çıkış Onayı</title>
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 350px;
            width: 90%;
        }

        h2 {
            margin-bottom: 25px;
            font-weight: 600;
            color: #222;
        }

        form button {
            background-color: #f47c2c;
            border: none;
            padding: 12px 25px;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 15px;
        }

        form button:hover {
            background-color: #d96a1c;
        }

        a {
            color: #f47c2c;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            vertical-align: middle;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Çıkmak istediğinize emin misiniz?</h2>
        <form method="post" action="logout.php">
            <button type="submit">Evet, çıkış yap</button>
            <a href="anasayfa.php">Hayır, geri dön</a>
        </form>
    </div>
</body>

</html>
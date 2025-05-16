<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['uye_id'])) {
    header("Location: uyeGiris.php");
    exit;
}

$uye_id = $_SESSION['uye_id'];
$mesaj = "";
$mesaj_turu = "";

// Kullanıcı bilgilerini al
$sql = "SELECT uye_adi, uye_soyadi, uye_mail, uye_sifre FROM uyeler WHERE uye_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uye_id);
$stmt->execute();
$result = $stmt->get_result();
$uye = $result->fetch_assoc();

// Şifre güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mevcutSifre = $_POST['mevcut_sifre'] ?? '';
    $yeniSifre = $_POST['yeni_sifre'] ?? '';
    $sifreTekrar = $_POST['sifre_tekrar'] ?? '';

    if (!password_verify($mevcutSifre, $uye['uye_sifre'])) {
        $mesaj = "Mevcut şifre yanlış.";
        $mesaj_turu = "error";
    } elseif (strlen($yeniSifre) < 6) {
        $mesaj = "Yeni şifre en az 6 karakter olmalıdır.";
        $mesaj_turu = "error";
    } elseif ($yeniSifre !== $sifreTekrar) {
        $mesaj = "Yeni şifreler uyuşmuyor.";
        $mesaj_turu = "error";
    } elseif (password_verify($yeniSifre, $uye['uye_sifre'])) {
        $mesaj = "Yeni şifre, mevcut şifre ile aynı olamaz.";
        $mesaj_turu = "error";
    } else {
        $sifreHash = password_hash($yeniSifre, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE uyeler SET uye_sifre = ? WHERE uye_id = ?");
        $update->bind_param("si", $sifreHash, $uye_id);
        if ($update->execute()) {
            $mesaj = "Şifre başarıyla güncellendi.";
            $mesaj_turu = "success";
        } else {
            $mesaj = "Şifre güncellenirken bir hata oluştu.";
            $mesaj_turu = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .menu {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .menu input[type="text"] {
            padding: 6px 10px;
            border: 1px solid #666;
            border-radius: 6px;
            background-color: <?= $theme === 'dark' ? '#2a2a2a' : '#fff' ?>;
            color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
        }

        .menu button {
            padding: 7px 14px;
            border: 1px solid #f47c2c;
            background-color: transparent;
            color: #f47c2c;
            border-radius: 6px;
            cursor: pointer;
        }

        .menu button:hover {
            background-color: #f47c2c;
            color: #000;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: #f47c2c;
        }

        .logo-icon {
            font-size: 36px;
            margin-right: 12px;
            line-height: 1;
        }

        .logo-button {
            display: inline-block;
            background-color: rgba(244, 124, 44, 0.82);

            /* Buton rengi */
            color: whitesmoke;
            padding: 7.5px 20px;
            margin-left: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logo-button:hover {
            background-color: rgb(0, 62, 71);
        }

        header h1 {
            margin: 0;
            font-size: 22px;
        }

        header .logout {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 16px;
        }

        header .logout:hover {
            text-decoration: underline;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .container {
            max-width: 500px;
            background: white;
            margin: 50px auto 100px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .info {
            margin: 20px 0;
            font-size: 16px;
        }

        .info label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        form {
            margin-top: 30px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            background: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #2980b9;
        }

        .login-box a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #f47c2c;
            text-decoration: none;
        }

        .login-box a:hover {
            text-decoration: underline;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            padding: 10px;
            border-radius: 6px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px" height="55px" class="logo-icon" style="margin-left: 50px;" />
            <a href="anasayfa.php" class="logo-button">QuestionLive</a>
        </div>
        <h1 style="margin-right: 290px;">Hoş Geldiniz, <?= htmlspecialchars($uye['uye_adi'] . ' ' . $uye['uye_soyadi']) ?></h1>
        <a href="cikis.php" class="logout"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </header>

    <div class="container">
        <h2>Profil Bilgilerim</h2>

        <div class="info">
            <label>Ad:</label> <?= htmlspecialchars($uye['uye_adi']) ?><br>
            <label>Soyad:</label> <?= htmlspecialchars($uye['uye_soyadi']) ?><br>
            <label>E-posta:</label> <?= htmlspecialchars($uye['uye_mail']) ?>
        </div>

        <form method="post">
            <h3>Şifre Güncelle</h3>
            <input type="password" name="mevcut_sifre" placeholder="Mevcut şifreniz" required>
            <input type="password" name="yeni_sifre" placeholder="Yeni şifre" required>
            <input type="password" name="sifre_tekrar" placeholder="Yeni şifre (tekrar)" required>
            <button type="submit" class="btn">Şifreyi Güncelle</button>
            <div class="login-box">
                <a href="anaSayfa.php" style="margin-top:-25px; margin-left:350px;">Ana Sayfaya Dön</a>
            </div>

            <?php if ($mesaj): ?>
                <div class="message <?= $mesaj_turu ?>"><?= htmlspecialchars($mesaj) ?></div>
            <?php endif; ?>
        </form>
    </div>

    <footer>
        &copy; <?= date("Y") ?> Tüm Hakları Saklıdır. | Canlı Geri Bildirim Sistemi
    </footer>

</body>

</html>
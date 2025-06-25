<?php
session_start();
require_once("connection.php"); // veritabanı baglantisi

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // composer ile PHPMailer yuklediysen burasi dogru olmali

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Kullanicinin e-posta adresi veritabaninda var mi kontrol et
    $stmt = $conn->prepare("SELECT uye_id FROM uyeler WHERE uye_mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Token olustur (random ve benzersiz)
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 1800; // 30 dakika gecerli

        // Veritabanina token kaydet
        $stmt = $conn->prepare("INSERT INTO password_resets (uye_id, token, expires) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['uye_id'], $token, $expires);
        $stmt->execute();

        $resetLink = "http://localhost/final/reset_password.php?token=" . $token;



        // PHPMailer ile mail gonder
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'destek.questionlive@gmail.com'; // Gmail adresin
            $mail->Password   = 'zqcv thsq jdhy jrdw'; // Gmail uygulama sifren
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('destek.questionlive@gmail.com', 'QuestionLive Destek');
            $mail->addAddress($email);

            $mail->isHTML(false);
            $mail->Subject = 'Sifre Sifirlama Talebi';
            $mail->Body    = "Merhaba, sifrenizi sifirlamak icin asagidaki linke tiklayin :\n\n" . $resetLink;

            $mail->send();
            $message = "Sifre sifirlama linki e-posta adresinize gonderildi, mail gözükmüyor ise lütfen spam kutusunu kontrol edin.";
        } catch (Exception $e) {
            $message = "Mail gonderilemedi: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Bu e-posta adresine kayitli kullanici bulunamadi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <title>Sifremi Unuttum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .forgot-box {
            background-color: #1e1e1e;
            padding: 40px 60px;
            border-radius: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.7);
            text-align: center;
        }

        h2 {
            color: #f47c2c;
            margin-bottom: 25px;
        }

        input[type="email"] {
            width: 92%;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: #2a2a2a;
            color: #fff;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #f47c2c;
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #da6d23;
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
        }

        .success {
            background-color: #4caf50;
            color: #000;
        }

        .error {
            background-color: #ff3a3a;
            color: #000;
        }

        a {
            display: inline-block;
            margin-top: 25px;
            color: #f47c2c;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="forgot-box">
        <h2>Sifremi Unuttum</h2>
        <form action="" method="post">
            <input type="email" name="email" placeholder="E-posta adresiniz" required />
            <button type="submit">Sifre Sifirlama Linki Gonder</button>
        </form>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'gonderildi') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <a href="uyeGiris.php">Giris Sayfasina Don</a>
    </div>
</body>

</html>
<?php
session_start();
require_once("connection.php");

$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'tr';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $language = $_SESSION['language'] = $_POST['language'];
}

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark';
if (isset($_POST['theme'])) {
    $theme = $_SESSION['theme'] = $_POST['theme'];
}

$message = "";
if (isset($_POST["giris"])) {
    $uye_mail = $_POST["uye_mail"];
    $uye_sifre = $_POST["uye_sifre"];

    $sql = "SELECT * FROM uyeler WHERE uye_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uye_mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($uye_sifre, $user['uye_sifre'])) {
            $_SESSION["uye_id"] = $user["uye_id"];
            $_SESSION["uye_adi"] = $user["uye_adi"];
            $_SESSION["uye_soyadi"] = $user["uye_soyadi"];
            $_SESSION["uye_mail"] = $user["uye_mail"];
            header("Location: anaSayfa.php");
            exit;
        } else {
            $message = $language === 'tr' ? "Şifre yanlış." : "Incorrect password.";
        }
    } else {
        $message = $language === 'tr' ? "Kullanıcı bulunamadı." : "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
  <meta charset="UTF-8">
  <title>QuestionLive - <?= $language === 'tr' ? 'Giriş Yap' : 'Login' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: <?= $theme === 'dark' ? '#121212' : '#f0f0f0' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
      transition: background-color 0.3s, color 0.3s;
    }

    header {
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#fff' ?>;
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
      flex-wrap: wrap;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #f47c2c;
      text-decoration: none;
    }

    .logo:hover {
      opacity: 0.8;
    }

    .menu form {
      display: inline;
    }

    .menu button {
      padding: 15px 20px;
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

    .main {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 80px 20px;
    }

    .login-box {
      margin-top: 65px;
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#fff' ?>;
      border: 5px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
      border-radius: 35px;
      padding: 70px;
      width: 100%;
      max-width: 385px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .login-box h2 {
      margin-bottom: 25px;
      color: #f47c2c;
      text-align: center;
    }

    .login-box input[type="email"],
    .login-box input[type="password"] {
      width: 94%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      background-color: <?= $theme === 'dark' ? '#2a2a2a' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
    }

    .login-box button {
      width: 100%;
      padding: 12px;
      background-color: #f47c2c;
      color: #000;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }

    .login-box button:hover {
      background-color: #da6d23;
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

    .error {
      background-color: #ffcccc;
      color: #900;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      text-align: center;
    }

    footer {
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#aaa' : '#333' ?>;
      text-align: center;
      padding: 30px 10px;
      margin-top: 80px;
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header>
  <a href="anaSayfa.php" class="logo"><i class="fa-solid fa-block-question"></i> QuestionLive</a>
  <div class="menu">
    <form action="" method="post" style="display:inline;">
      <button type="submit" name="language" value="<?= $language === 'tr' ? 'en' : 'tr' ?>">
        <i class="fas fa-language"></i>
      </button>
    </form>
    <form action="" method="post" style="display:inline;">
      <button type="submit" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark' ?>">
        <i class="<?= $theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon' ?>"></i>
      </button>
    </form>
  </div>
</header>

<!-- MAIN -->
<div class="main">
  <div class="login-box">
    <h2><?= $language === 'tr' ? 'Üye Giriş' : 'Member Login' ?></h2>
    <?php if (!empty($message)): ?>
      <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form action="" method="post">
      <input type="email" name="uye_mail" placeholder="<?= $language === 'tr' ? 'E-posta adresiniz' : 'Your email address' ?>" required>
      <input type="password" name="uye_sifre" placeholder="<?= $language === 'tr' ? 'Şifreniz' : 'Your password' ?>" required>
      <button type="submit" name="giris"><?= $language === 'tr' ? 'Giriş Yap' : 'Login' ?></button>
    </form>
    <a href="uyeKayit.php"><?= $language === 'tr' ? 'Hesabınız yok mu? Kayıt olun' : "Don't have an account? Sign up" ?></a>
    <a href="anaSayfa.php"><?= $language === 'tr' ? 'Ana Sayfaya Dön' : 'Back to Home' ?></a>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <h3><?= $language === 'tr' ? 'İletişim' : 'Contact' ?></h3>
  <p><?= $language === 'tr' ? 'E-posta: destek@questionlive.com' : 'Email: support@questionlive.com' ?></p>
  <p><?= $language === 'tr' ? 'Telefon: +90 555 123 4567' : 'Phone: +90 555 123 4567' ?></p>
  <p><?= $language === 'tr' ? 'Adres: İstanbul, Türkiye' : 'Address: Istanbul, Turkey' ?></p>
</footer>

</body>
</html>
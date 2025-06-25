<?php
session_start();
require_once("connection.php");

$language = $_SESSION['language'] ?? 'tr';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
  $language = $_SESSION['language'] = $_POST['language'];
}

$theme = $_SESSION['theme'] ?? 'dark';
if (isset($_POST['theme'])) {
  $theme = $_SESSION['theme'] = $_POST['theme'];
}

$message = "";
$messageType = "";

if (isset($_POST["kaydet"])) {
  $uye_adi    = trim($_POST["uye_adi"]);
  $uye_soyadi = trim($_POST["uye_soyadi"]);
  $uye_mail   = $_POST["uye_mail"];
  $uye_sifre  = password_hash($_POST["uye_sifre"], PASSWORD_DEFAULT);

  $onlyLetters = '/^[\p{L}\s]+$/u';
  if (!preg_match($onlyLetters, $uye_adi) || !preg_match($onlyLetters, $uye_soyadi)) {
    $message     = "Ad ve Soyad alanlarına yalnızca harf girebilirsiniz.";
    $messageType = "error";
  } else {
    $sql = "SELECT 1 FROM uyeler WHERE uye_mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uye_mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $message     = "Bu kullanıcı zaten mevcut.";
      $messageType = "error";
    } else {
      $sql = "INSERT INTO uyeler (uye_adi, uye_soyadi, uye_mail, uye_sifre)
                  VALUES (?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssss", $uye_adi, $uye_soyadi, $uye_mail, $uye_sifre);

      if ($stmt->execute()) {
        $message     = "Kullanıcı başarıyla eklendi.";
        $messageType = "success";
      } else {
        $message     = "Hata: " . $stmt->error;
        $messageType = "error";
      }
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">

<head>
  <meta charset="UTF-8">
  <title><?= $language === 'tr' ? 'Kayıt Ol' : 'Sign Up' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: <?= $theme === 'dark' ? '#121212' : '#ffffe0' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
      transition: background-color 0.3s, color 0.3s;
    }

    header {
      background-color: <?= $theme === 'dark' ? '#001f24' : '#2e8b57' ?>;
      padding: 30px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
      flex-wrap: wrap;
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

    .logo:hover {
      opacity: 0.8;
    }

    .logo-button {
      display: inline-block;
      background-color: rgba(244, 124, 44, 0.82);
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

    .menu form {
      display: inline;
    }

    .menu button {
      padding: 7px 14px;
      border: 2px solid #f47c2c;
      background-color: transparent;
      color: #f47c2c;
      border-radius: 6px;
      cursor: pointer;
    }

    .menu button:hover {
      background-color: #f47c2c;
      color: #000;
    }

    .language-switch,
    .theme-switch {
      background: none;
      color: #f47c2c;
      border: none;
      cursor: pointer;
      font-size: 20px;
    }

    .main {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 80px 20px;
    }

    .login-box {
      margin-top: 65px;
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#eeeed1' ?>;
      border: 5px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
      border-radius: 35px;
      padding: 70px;
      width: 100%;
      max-width: 385px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .login-box h2 {
      margin-bottom: 25px;
      color: #f47c2c;
      text-align: center;
    }

    .login-box input[type="text"],
    .login-box input[type="text"],
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
      background-color: rgb(255, 0, 0);
      color: #000000;
      padding: 10px 20px;
      margin: 20px auto;
      border-radius: 6px;
      text-align: center;
      width: fit-content;
      max-width: 90%;
      box-shadow: 0 2px 6px rgba(255, 0, 0, 0.1);
      font-size: 14px;
    }

    .success {
      background-color: rgb(26, 255, 0);
      color: rgb(103, 93, 93);
      padding: 10px 20px;
      margin: 20px auto;
      border-radius: 6px;
      text-align: center;
      width: fit-content;
      max-width: 90%;
      box-shadow: 0 2px 6px rgba(0, 255, 13, 0.15);
      font-size: 14px;
    }

    #messageBox {
      transition: opacity 0.5s ease, max-height 0.5s ease;
      max-height: 200px;
      overflow: hidden;
    }

    #messageBox.hide {
      opacity: 0;
      max-height: 0;
      padding: 0;
      margin: 0;
    }



    footer {
      background-color: <?= $theme === 'dark' ? '#001f24' : '#2e8b57' ?>;
      color: <?= $theme === 'dark' ? '#da6d23' : '#333' ?>;
      text-align: center;
      padding: 30px 10px;
      margin-top: 100px;
    }
  </style>
  <script>
    function validateForm() {
      const nameRegex = /^[\p{L}\s]+$/u;
      const form = document.forms[0];
      const ad = form.uye_adi.value.trim();
      const soyad = form.uye_soyadi.value.trim();

      if (!nameRegex.test(ad) || !nameRegex.test(soyad)) {
        alert('Ad ve Soyad alanlarına yalnızca harf girebilirsiniz.');
        return false;
      }
      return true;
    }
  </script>
</head>

<body>
  <header> … </header>

  <div class="main">
    <div class="login-box">
      <h2><?= $language === 'tr' ? 'Üye Kayıt' : 'Sign Up' ?></h2>

      <?php if ($message): ?>
        <div id="messageBox" class="<?= htmlspecialchars($messageType) ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form action="" method="post" onsubmit="return validateForm();">
        <input type="text" name="uye_adi"
          pattern="[\p{L}\s]+"
          title="Lütfen yalnızca harf kullanın"
          placeholder="<?= $language === 'tr' ? 'Adınız' : 'First Name' ?>" required>

        <input type="text" name="uye_soyadi"
          pattern="[\p{L}\s]+"
          title="Lütfen yalnızca harf kullanın"
          placeholder="<?= $language === 'tr' ? 'Soyadınız' : 'Last Name' ?>" required>

        <input type="email" name="uye_mail"
          placeholder="<?= $language === 'tr' ? 'E-posta adresiniz' : 'Your email address' ?>" required>

        <input type="password" name="uye_sifre"
          placeholder="<?= $language === 'tr' ? 'Şifreniz' : 'Your password' ?>" required>

        <button type="submit" name="kaydet"><?= $language === 'tr' ? 'Kayıt Ol' : 'Sign Up' ?></button>
      </form>

      <a href="uyeGiris.php"><?= $language === 'tr'
                                ? 'Hesabınız var mı? Giriş Yapın'
                                : "Already have an account? Log in" ?></a>
      <a href="anaSayfa.php"><?= $language === 'tr' ? 'Ana Sayfaya Dön' : 'Back to Home' ?></a>
    </div>
  </div>

  <footer> … </footer>

  <?php if ($message): ?>
    <script>
      setTimeout(() => {
        document.querySelector('#messageBox')?.classList.add('hide');
        <?php if ($messageType === "success"): ?>
          setTimeout(() => location.href = "uyeGiris.php", 600);
        <?php endif; ?>
      }, 4000);
    </script>
  <?php endif; ?>
</body>

</html>
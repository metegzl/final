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
$messageType = "";

if (isset($_POST["kaydet"])) {
  $uye_adi = $_POST["uye_adi"];
  $uye_soyadi = $_POST["uye_soyadi"];
  $uye_mail = $_POST["uye_mail"];
  $uye_sifre = password_hash($_POST["uye_sifre"], PASSWORD_DEFAULT);

  // E-posta kontrolü
  $sql = "SELECT * FROM uyeler WHERE uye_mail = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $uye_mail);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $message = "Bu Kullanıcı Zaten Mevcut.";
    $messageType = "error";
  } else {
    // Yeni kullanıcı ekleme
    $sql = "INSERT INTO uyeler (uye_adi, uye_soyadi, uye_mail, uye_sifre)
                VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $uye_adi, $uye_soyadi, $uye_mail, $uye_sifre);

    if ($stmt->execute()) {
      $message = "Kullanıcı başarıyla eklendi.";
      $messageType = "success";
    } else {
      $message = "Hata: " . $stmt->error;
      $messageType = "error";
    }
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">

<head>
  <meta charset="UTF-8">
  <title><?= $language === 'tr' ? 'Kayıt Ol' : 'Sign Up' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>
    window.addEventListener("DOMContentLoaded", function() {
      const messageBox = document.getElementById("messageBox");
      if (messageBox) {
        setTimeout(() => {
          messageBox.style.transition = "opacity 0.5s ease";
          messageBox.style.opacity = "0";
          setTimeout(() => messageBox.remove(), 500); // tamamen silinsin
        }, 4000); // 4 saniye sonra başlasın
      }
    });
  </script>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: <?= $theme === 'dark' ? '#121212' : '#f0f0f0' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
      transition: background-color 0.3s, color 0.3s;
    }

    header {
      background-color: <?= $theme === 'dark' ? '#001f24' : '#fff' ?>;
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
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#fff' ?>;
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
      /* Ortalamak için */
      border-radius: 6px;
      text-align: center;
      width: fit-content;
      /* İçeriğe göre genişlik */
      max-width: 90%;
      /* Taşmayı önlemek için */
      box-shadow: 0 2px 6px rgba(255, 0, 0, 0.1);
      /* Daha hoş görünüm */
      font-size: 14px;
    }

    .success {
      background-color: rgb(26, 255, 0);
      color: rgb(103, 93, 93);
      padding: 10px 20px;
      margin: 20px auto;
      /* Ortalamak için */
      border-radius: 6px;
      text-align: center;
      width: fit-content;
      /* İçeriğe göre genişlik */
      max-width: 90%;
      /* Taşmayı önlemek için */
      box-shadow: 0 2px 6px rgba(0, 255, 13, 0.15);
      /* Daha hoş görünüm */
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
      background-color: <?= $theme === 'dark' ? '#001f24' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#da6d23' : '#333' ?>;
      text-align: center;
      padding: 30px 10px;
      margin-top: 100px;
    }
  </style>
</head>

<body>

  <!-- <?php if (isset($message)): ?>
    <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?> -->

  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px" height="55px" class="logo-icon" style="margin-left: 50px;" />
      <a href="anasayfa.php" class="logo-button">QuestionLive</a>
    </div>
    <div class="menu">
      <form action="" method="post">
        <button type="submit" name="language" value="<?= $language === 'tr' ? 'en' : 'tr' ?>" class="language-switch">
          <?= '🌍​' ?>
        </button>
      </form>
      <form action="" method="post">
        <button type="submit" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark' ?>" class="theme-switch">
          <?= $theme === 'dark' ? '🌞' : '🌙' ?>
        </button>
      </form>
    </div>
  </header>

  <!-- MAIN -->
  <div class="main">
    <div class="login-box">
      <h2><?= $language === 'tr' ? 'Üye Kayıt' : 'Sign Up' ?></h2>
      <?php if (!empty($message)): ?>
        <div id="messageBox" class="<?= htmlspecialchars($messageType) ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form action="" method="post">
        <input type="text" name="uye_adi" placeholder="<?= $language === 'tr' ? 'Adınız' : 'First Name' ?>" required>
        <input type="text" name="uye_soyadi" placeholder="<?= $language === 'tr' ? 'Soyadınız' : 'Last Name' ?>" required>
        <input type="email" name="uye_mail" placeholder="<?= $language === 'tr' ? 'E-posta adresiniz' : 'Your email address' ?>" required>
        <input type="password" name="uye_sifre" placeholder="<?= $language === 'tr' ? 'Şifreniz' : 'Your password' ?>" required>
        <button type="submit" name="kaydet"><?= $language === 'tr' ? 'Kayıt Ol' : 'Sign Up' ?></button>
      </form>
      <a href="uyeGiris.php"><?= $language === 'tr' ? 'Hesabınız var mı? Giriş Yapın' : "Don't have an account? Sign up" ?></a>
      <a href="anaSayfa.php"><?= $language === 'tr' ? 'Ana Sayfaya Dön' : 'Back to Home' ?></a>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <h3><?= $language === 'tr' ? 'İletişim' : 'Contact' ?></h3>
    <p><?= $language === 'tr' ? 'E-posta: destek@questionlive.com' : 'Email: destek@questionlive.com' ?></p>
    <p><?= $language === 'tr' ? 'Telefon: +90 555 123 4567' : 'Phone: +90 555 123 4567' ?></p>
    <p><?= $language === 'tr' ? 'Adres: İstanbul, Türkiye' : 'Address: Istanbul, Turkey' ?></p>
    <div>
      <a href="https://x.com/cristiano" target="_blank" style="margin-right: 20px;">
        <i class="fab fa-x-twitter" style="font-size: 40px; color:rgb(0, 0, 0);"></i>
      </a>

      <a href="https://www.instagram.com/cristiano" target="_blank" style="margin-right: 20px;">
        <i class="fab fa-instagram" style="font-size: 40px; color: #E4405F;"></i>
      </a>

      <a href="https://www.facebook.com/cristiano" target="_blank">
        <i class="fab fa-facebook" style="font-size: 40px; color: #1877F2;"></i>
      </a>
    </div>
  </footer>

  <?php if ($message): ?>
    <div class="notification <?php echo $messageType; ?>">
      <?php echo $message; ?>
    </div>
    <script>
      setTimeout(() => {
        document.querySelector('.notification').style.display = 'none';
        <?php if ($messageType === "success"): ?>
          window.location.href = "uyeGiris.php";
        <?php endif; ?>
      }, 3000);
    </script>
  <?php endif; ?>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script>
    function validateForm() {
      const form = document.forms[0];
      const inputs = form.getElementsByTagName('input');
      for (let input of inputs) {
        if (input.value.trim() === '') {
          alert('Lütfen tüm alanları doldurunuz.');
          return false;
        }
      }
      return true;
    }

    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    togglePassword.addEventListener("click", function() {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      this.name = this.name === "eye-outline" ? "eye-off-outline" : "eye-outline";
    });
  </script>
  <script>
    window.addEventListener("DOMContentLoaded", function() {
      const messageBox = document.getElementById("messageBox");
      if (messageBox) {
        setTimeout(() => {
          messageBox.classList.add("hide");
        }, 5000);
      }
    });
  </script>

</body>

</html>
<?php
session_start();
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'tr';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) 
{
    $language = $_SESSION['language'] = $_POST['language'];
}

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark';
if (isset($_POST['theme'])) 
{
    $theme = $_SESSION['theme'] = $_POST['theme'];
}
?>

<?php
$girisBasarili = false;
if (isset($_SESSION["giris_basarili"]) && $_SESSION["giris_basarili"] === true) 
{
    $girisBasarili = true;
    unset($_SESSION["giris_basarili"]);
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ana Sayfa</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body 
    {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: <?= $theme === 'dark' ? '#121212' : '#f0f0f0' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
      transition: background-color 0.3s, color 0.3s;
    }

    header {
  background-color: <?= $theme === 'dark' ? '#2b3741' : '#fff' ?>;
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

    .menu 
    {
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }

    .menu input[type="text"] 
    {
      padding: 6px 10px;
      border: 1px solid #666;
      border-radius: 6px;
      background-color: <?= $theme === 'dark' ? '#2a2a2a' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
    }

    .menu button 
    {
      padding: 7px 14px;
      border: 1px solid #f47c2c;
      background-color: transparent;
      color: #f47c2c;
      border-radius: 6px;
      cursor: pointer;
    }

    .menu button:hover 
    {
      background-color: #f47c2c;
      color: #000;
    }

    .language-switch, .theme-switch 
    {
      background: none;
      color: #f47c2c;
      border: none;
      cursor: pointer;
      font-size: 20px;
    }

    .language-switch:hover, .theme-switch:hover 
    {
      color: #fff;
    }

    .main 
    {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 80px 20px;
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    .main h1 
    {
      font-size: 36px;
      color: #f47c2c;
      margin-top: 80px;
    }

    .main p 
    {
      font-size: 18px;
      color: <?= $theme === 'dark' ? '#ccc' : '#333' ?>;
      max-width: 700px;
    }

    .main .discover-btn 
    {
      margin-top: 30px;
      padding: 15px 30px;
      background-color: #f47c2c;
      color: #000;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .discover-btn:hover 
    {
      background-color: #da6d23;
    }

    .actions 
    {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 60px;
      flex-wrap: wrap;
      animation: fadeIn 1s ease-out;
    }

    .action-box 
    {
      background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#fff' ?>;
      border: 1px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
      border-radius: 8px;
      padding: 20px;
      width: 280px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      margin-bottom: 10px;
    }

    .action-box h3 
    {
      margin-top: 0;
      font-size: 20px;
      color: #f47c2c;
    }

    .action-box p 
    {
      font-size: 14px;
      color: <?= $theme === 'dark' ? '#aaa' : '#555' ?>;
    }

    .action-box input[type="text"] 
    {
      width: 90%;
      padding: 8px;
      margin-top: 10px;
      margin-bottom: 15px;
      border: 1px solid #666;
      border-radius: 8px;
      background-color: <?= $theme === 'dark' ? '#2a2a2a' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
    }

    .action-box button 
    {
      width: 95%;
      padding: 10px;
      background-color: #f47c2c;
      color: #000;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .action-box button:hover 
    {
      background-color: #da6d23;
    }

    .toast 
    {
      position: fixed;
      top: 30px; /* sayfanın en üstünden boşluk */
      left: 50%;
      transform: translateX(-50%);
      background-color: #4CAF50; /* yeşil hoşgeldin kutusu */
      color: white;
      padding: 15px 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      font-size: 18px;
      z-index: 9999;
      opacity: 0;
      transition: opacity 0.4s ease-in-out;
    }
    .toast.show 
    {
      opacity: 1;
    }

    footer 
    {
      background-color: <?= $theme === 'dark' ? '#2b3741' : '#fff' ?>;
      color: <?= $theme === 'dark' ? '#da6d23' : '#333' ?>;
      text-align: center;
      padding: 30px 10px;
      margin-top: 100px;
    }

    @keyframes fadeIn 
    {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @media (max-width: 700px) 
    {
      .actions 
      {
        flex-direction: column;
        align-items: center;
      }
      header 
      {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

<?php if (isset($_GET['invalid']) && $_GET['invalid'] == '1'): ?>
  <script>
    window.onload = function() 
    {
      alert("<?= $language === 'tr' ? 'Geçersiz oturum.' : 'Invalid session.' ?>");
    };
  </script>
<?php endif; ?>

<header>
  <div class="logo">
    <img src="https://cdn.creazilla.com/emojis/49577/monkey-emoji-clipart-xl.png" width="55px;" height="55  px;" class="logo-icon" style="margin-left: 50px;"/>
    <span class="logo-text">QuestionLive</span>
  </div>

  <div class="menu">
    <form action="joinSession.php" method="get">
      <input type="text" name="code" placeholder="<?= $language === 'tr' ? 'Oturum Kodu ...' : 'Session Code ...' ?>" required>
      <button type="submit"><?= $language === 'tr' ? 'Katıl' : 'Join' ?></button>
    </form>
    <form action="createSession.php" method="get">
      <button type="submit"><?= $language === 'tr' ? 'Oturum Oluştur' : 'Create Session' ?></button>
    </form>
    
    <?php if (isset($_SESSION['uye_adi'])): ?>
      <form action="profil.php" method="get">
        <button type="submit"><?= $language === 'tr' ? 'Profilim' : 'My Profile' ?> (<?= htmlspecialchars($_SESSION['uye_adi']) ?>)</button>
      </form>
      <form action="logout.php" method="post">
        <button type="submit"><?= $language === 'tr' ? 'Çıkış Yap' : 'Logout' ?></button>
      </form>
    <?php else: ?>
      <form action="uyeGiris.php" method="get">
        <button type="submit"><?= $language === 'tr' ? 'Giriş Yap' : 'Login' ?></button>
      </form>
    <?php endif; ?>

    <form action="" method="post">
      <button type="submit" name="language" value="<?= $language === 'tr' ? 'en' : 'tr' ?>" class="language-switch">
        <i class="fas fa-language"></i>
      </button>
    </form>
    <form action="" method="post">
      <button type="submit" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark' ?>" class="theme-switch">
        <i class="<?= $theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon' ?>"></i>
      </button>
    </form>
  </div>
</header>

<div class="main">
  <h1><?= $language === 'tr' ? 'Dijital etkileşimin yeni boyutunu keşfet' : 'Discover the new dimension of digital interaction' ?></h1>
  <p><?= $language === 'tr' ? 'Etkinliklerde, işletmelerde ve eğitim ortamlarında anonim geri bildirim almanın en kolay yolu.' : 'The easiest way to receive anonymous feedback at events, businesses, and educational environments.' ?></p>
  <form action="#">
  <button class="discover-btn" style="margin-top: 35px;"><?= $language === 'tr' ? 'Olanakları Keşfet' : 'Discover the possibilities' ?></button>
  </form>
  <div class="actions">
    <div class="action-box">
      <h3><?= $language === 'tr' ? 'Bir Oturuma Katıl' : 'Join a Session' ?></h3>
      <p><?= $language === 'tr' ? 'Bir oturuma katılmak mı istiyorsun? Oturum kodunu gir ve Katıl\'a tıkla.' : 'Want to join a session? Just enter the session code and click Join.' ?></p>
      <form action="joinSession.php" method="get">
        <input type="text" name="code" placeholder="<?= $language === 'tr' ? 'Oturum Kodu Gir ...' : 'Enter Session Code ...' ?>" required>
        <button type="submit"><?= $language === 'tr' ? 'Katıl' : 'Join' ?></button>
      </form>
    </div>

    <div class="action-box">
      <h3><?= $language === 'tr' ? 'Yeni Oturum Oluştur' : 'Create a Session' ?></h3>
      <p><?= $language === 'tr' ? 'QuestionLive\'ı ücretsiz bir şekilde kullanın. Katılımcılar otomatik kodla giriş yapar. Eğer sen de bir oturum oluşturmak istiyorsan ⬇Hemen Tıkla⬇' : 'Use QuestionLive for free and without registration. Participants join with an auto-generated code.' ?></p>
      <form action="createSession.php" method="get">
        <button type="submit"><?= $language === 'tr' ? 'Oturum Oluştur' : 'Create Session' ?></button>
      </form>
    </div>
  </div>
</div>

<?php if ($girisBasarili): ?>
  <div id="toast" class="toast">
    <i>✔️</i> GİRİŞ BAŞARILI, HOŞGELDİNİZ 
  </div>
  <script>
    const toast = document.getElementById("toast");
    toast.classList.add("show");
    setTimeout(() => {
      toast.classList.remove("show");
    }, 2200);
  </script>
<?php endif; ?>

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

</body>
</html>
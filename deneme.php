<?php
session_start();
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'tr'; // Varsayılan dil 'tr'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
    $language = $_SESSION['language'] = $_POST['language'];
}

$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark'; // varsayılan tema 'dark'
if (isset($_POST['theme'])) {
    $theme = $_SESSION['theme'] = $_POST['theme'];
}
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QuestionLive</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Global Styles */
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: <?= $theme === 'dark' ? '#121212' : '#f0f0f0' ?>;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
      transition: background-color 0.3s, color 0.3s;
    }

    /* Header */
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
    }

    .menu {
      display: flex;
      gap: 15px;
      align-items: center;
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

    .language-switch, .theme-switch {
      background: none;
      color: #f47c2c;
      border: none;
      cursor: pointer;
      font-size: 20px;
    }

    .language-switch:hover, .theme-switch:hover {
      color: #fff;
    }

    /* Theme Toggle */
    .theme-toggle {
      background-color: <?= $theme === 'dark' ? '#333' : '#fff' ?>;
      border: 1px solid #fff;
      border-radius: 6px;
      padding: 5px 10px;
      cursor: pointer;
      color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
    }

    .theme-toggle:hover {
      background-color: #f47c2c;
    }

    /* Main Section */
    .main {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 80px 20px;
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @media (max-width: 700px) {
      .actions {
        flex-direction: column;
        align-items: center;
      }

      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .menu {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
      }

      .theme-toggle {
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>

<?php if (isset($_GET['invalid']) && $_GET['invalid'] == '1'): ?>
  <script>
    window.onload = function() {
      alert("Geçersiz oturum.");
    };
  </script>
<?php endif; ?>

<!-- Header -->
<header>
  <div class="logo">🧠 QuestionLive</div>
  <div class="menu">
    <form action="joinSession.php" method="get" style="display:inline;">
      <input type="text" name="code" placeholder="<?= $language === 'tr' ? 'Oturum Kodu ...' : 'Session Code ...' ?>" required>
      <button type="submit"><?= $language === 'tr' ? 'Katıl' : 'Join' ?></button>
    </form>
    <form action="createSession.php" method="get" style="display:inline;">
      <button type="submit"><?= $language === 'tr' ? 'Oturum Oluştur' : 'Create Session' ?></button>
    </form>
    <form action="uyeGiris.php" method="get" style="display:inline;">
      <button type="submit"><?= $language === 'tr' ? 'Giriş Yap' : 'Login' ?></button>
    </form>
    <form action="" method="post" style="display:inline;">
      <button type="submit" name="language" value="<?= $language === 'tr' ? 'en' : 'tr' ?>" class="language-switch">
        <i class="fas fa-language"></i>
      </button>
    </form>
    <form action="" method="post" style="display:inline;">
      <button type="submit" name="theme" value="<?= $theme === 'dark' ? 'light' : 'dark' ?>" class="theme-switch">
        <i class="<?= $theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon' ?>"></i>
      </button>
    </form>
  </div>
</header>

</body>
</html>
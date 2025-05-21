<?php
session_start();
require_once("connection.php");

$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'tr';
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'dark';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if ($new_password !== $confirm_password) {
        $error = $language === 'tr' ? "Şifreler uyuşmuyor." : "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT uye_id FROM password_resets WHERE token = ? AND expires > ?");
        $now = date("U");
        $stmt->bind_param("si", $token, $now);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = $language === 'tr' ? "Token geçersiz veya süresi dolmuş." : "Token is invalid or expired.";
        } else {
            $data = $result->fetch_assoc();
            $uye_id = $data['uye_id'];

            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE uyeler SET uye_sifre = ? WHERE uye_id = ?");
            $stmt->bind_param("si", $password_hash, $uye_id);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $success = $language === 'tr' ? "Şifreniz başariyla güncellendi. Ana sayfaya yönlendiriliyorsunuz..." : "Your password has been updated successfully. Redirecting to homepage...";
            header("Refresh: 3; url=anaSayfa.php");
        }
    }
} elseif (!isset($_GET['token'])) {
    die("Geçersiz istek.");
} else {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT uye_id, expires FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0 || $result->fetch_assoc()['expires'] < date("U")) {
        die($language === 'tr' ? "Token süresi dolmuş veya geçersiz." : "Token is expired or invalid.");
    }
}
?>

<!DOCTYPE html>
<meta charset="UTF-8">
<html lang="<?= $language ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $language === 'tr' ? 'Şifre Sıfırlama' : 'Reset Password' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: <?= $theme === 'dark' ? '#121212' : '#ffffe0' ?>;
            color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-box {
            background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#eeeed1' ?>;
            border: 5px solid <?= $theme === 'dark' ? '#333' : '#ccc' ?>;
            border-radius: 35px;
            padding: 50px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .reset-box h2 {
            margin-bottom: 25px;
            color: #f47c2c;
            text-align: center;
        }

        .reset-box input[type="password"] {
            width: 94%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: <?= $theme === 'dark' ? '#2a2a2a' : '#fff' ?>;
            color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
        }

        .reset-box button {
            width: 100%;
            padding: 12px;
            background-color: #f47c2c;
            color: #000;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        .reset-box button:hover {
            background-color: #da6d23;
        }

        .success,
        .error {
            background-color: <?= isset($error) ? 'rgb(255, 35, 35)' : 'rgb(24, 200, 24)' ?>;
            color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
            padding: 15px 20px;
            border-left: 8px solid <?= isset($error) ? 'rgb(200, 0, 0)' : 'rgb(10, 160, 10)' ?>;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            margin-bottom: 20px;
            animation: fadein 0.5s ease-in-out;
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="reset-box">
        <h2><?= $language === 'tr' ? 'Yeni Şifre Belirle' : 'Set New Password' ?></h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success">
                <?= htmlspecialchars($success) ?><br>
                <small><?= $language === 'tr' ? 'Lütfen bekleyiniz, yönlendiriliyorsunuz...' : 'Please wait, redirecting...' ?></small>
            </div>
        <?php endif; ?>

        <form action="reset_password.php" method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="new_password" placeholder="<?= $language === 'tr' ? 'Yeni şifre' : 'New Password' ?>" required>
            <input type="password" name="confirm_password" placeholder="<?= $language === 'tr' ? 'Yeni şifre (tekrar)' : 'Confirm Password' ?>" required>
            <button type="submit"><?= $language === 'tr' ? 'Şifreyi Sıfırla' : 'Reset Password' ?></button>
        </form>
    </div>
</body>

</html>
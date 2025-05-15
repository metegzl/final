<?php
require_once("connection.php");

$message = "";
$messageType = "";

if (isset($_POST["kaydet"])) {
    $uye_adi = $_POST["uye_adi"];
    $uye_soyadi = $_POST["uye_soyadi"];
    $uye_mail = $_POST["uye_mail"];
    $uye_sifre = password_hash($_POST["uye_sifre"], PASSWORD_DEFAULT);

    try {
        $sql = "SELECT * FROM uyeler WHERE uye_mail = :uye_mail";
        $query = $connection->prepare($sql);
        $query->bindParam(':uye_mail', $uye_mail);
        $query->execute();

        if ($query->rowCount() > 0) {
            $message = "Bu kullanıcı zaten mevcut.";
            $messageType = "error";
        } else {
            $sql = "INSERT INTO uyeler (uye_adi, uye_soyadi, uye_mail, uye_sifre)
                    VALUES (:uye_adi, :uye_soyadi, :uye_mail, :uye_sifre)";
            $query = $connection->prepare($sql);
            $query->bindParam(':uye_adi', $uye_adi);
            $query->bindParam(':uye_soyadi', $uye_soyadi);
            $query->bindParam(':uye_mail', $uye_mail);
            $query->bindParam(':uye_sifre', $uye_sifre);
            $query->execute();

            $message = "Kullanıcı başarıyla eklendi.";
            $messageType = "success";
        }
    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Üye Kayıt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <style>
        /* Tüm CSS kodları buraya */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: none;
        }
        body {
            background: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            display: flex;
            width: 60%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .container::before {
            content: "";
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 1px;
            height: 60%;
            background-color: black;
            z-index: 1;
        }
        .left-section, .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .left-section {
            flex-direction: column;
        }
        .right-section {
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login__header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1rem;
        }
        .login__header h2 {
            color: #808080;
            margin-bottom: 1rem;
        }
        .login__box {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            border-radius: 4px;
            padding: 0.875rem;
            margin-bottom: 1rem;
            width: 100%;
        }
        .login__box input {
            border: none;
            outline: none;
            padding: 0.45rem 0.875rem;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            background: transparent;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3a3a3a;
            color: #fff;
            padding: 1rem;
            width: 15rem;
            box-shadow: 4px 4px #2a2a2a;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: 300ms;
            margin-bottom: 1rem;
            text-decoration: none;
            font-size: 16px;
        }
        .btn:hover {
            transform: translateY(7px);
            transition: 300ms;
        }
        .btn span {
            padding-left: 10px;
        }
        .fs-20 {
            font-size: 24px;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .logo-img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            border-radius: 50%;
            overflow: hidden;
        }
        .notification {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 500px;
            text-align: center;
            font-size: 1rem;
            z-index: 1000;
        }
        .notification.error {
            background-color: #f44336;
        }
        .password-container {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            border-radius: 4px;
            padding: 0.875rem;
            margin-bottom: 1rem;
            width: 100%;
        }
        .password-container input {
            border: none;
            outline: none;
            padding: 0.45rem 0.875rem;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            background: transparent;
        }
        .password-container ion-icon {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <form action="" method="POST" onsubmit="return validateForm()">
                <div class="login__header">
                    <h2>KAYIT OL</h2>
                </div>
                <div class="login__box">
                    <ion-icon class="fs-20" name="person-outline"></ion-icon>
                    <input type="text" name="uye_adi" placeholder="Ad" autocomplete="off">
                </div>
                <div class="login__box">
                    <ion-icon class="fs-20" name="person-outline"></ion-icon>
                    <input type="text" name="uye_soyadi" placeholder="Soyad" autocomplete="off">
                </div>
                <div class="login__box">
                    <ion-icon class="fs-20" name="mail-outline"></ion-icon>
                    <input type="email" name="uye_mail" placeholder="E-mail" autocomplete="off">
                </div>
                <div class="password-container">
                    <ion-icon class="fs-20" name="lock-closed-outline"></ion-icon>
                    <input type="password" name="uye_sifre" placeholder="Şifre" id="password">
                    <ion-icon name="eye-outline" class="fs-20" id="togglePassword"></ion-icon>
                </div>
                <div class="btn-container">
                    <button class="btn" name="kaydet">
                        <ion-icon name="log-in-outline"></ion-icon>
                        <span>Kaydol</span>
                    </button>
                    <a class="btn" href="uyeGiris.php">
                        <ion-icon name="log-in-outline"></ion-icon>
                        <span>Zaten Hesabım Var</span>
                    </a>
                </div>
            </form>
        </div>
        <div class="right-section">
            <img src="images/logo.png" alt="Site Logo" class="logo-img">
        </div>
    </div>

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

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.name = this.name === "eye-outline" ? "eye-off-outline" : "eye-outline";
        });
    </script>
</body>
</html>
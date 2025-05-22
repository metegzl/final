<?php
session_start();
require_once("connection.php");

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["session_code"])) {
  $code = $_POST["session_code"];

  // chat mesajlarını sil
  $stmt1 = $conn->prepare("DELETE FROM chat_messages WHERE session_code = ?");
  $stmt1->bind_param("s", $code);

  // session kaydını sil
  $stmt2 = $conn->prepare("DELETE FROM sessions WHERE session_code = ?");
  $stmt2->bind_param("s", $code);

  if ($stmt1->execute() && $stmt2->execute()) {
    $success = true;
  } else {
    $errorMsg = "Bir hata oluştu: " . $conn->error;
  }

  $stmt1->close();
  $stmt2->close();
  $conn->close();
} else {
  $errorMsg = "Geçersiz istek.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Session Ended</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f8f8;
      margin: 0;
      padding: 50px;
      text-align: center;
    }

    .toast {
      position: fixed;
      top: -100px;
      left: 50%;
      transform: translateX(-50%);
      min-width: 300px;
      max-width: 90%;
      background-color: #28a745;
      color: white;
      padding: 16px 20px;
      border-radius: 8px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
      font-size: 17px;
      z-index: 1000;
      display: flex;
      align-items: center;
      gap: 10px;
      opacity: 0;
      transition: all 0.5s ease;
    }

    .toast.show {
      top: 20px;
      opacity: 1;
    }

    .toast.error {
      background-color: #dc3545;
    }

    .toast i {
      font-style: normal;
      font-weight: bold;
      font-size: 22px;
    }
  </style>
</head>

<body>

  <?php if ($success): ?>
    <div id="toast" class="toast">
      <i>✔️</i> Ders oturumu sonlanmıştır
    </div>
    <script>
      const toast = document.getElementById("toast");
      toast.classList.add("show");

      <?php if ($success): ?>
        setTimeout(() => {
          toast.classList.remove("show");
        }, 2200);

        setTimeout(() => {
          window.location.href = "anasayfa.php";
        }, 2500);
      <?php endif; ?>
    </script>

  <?php endif; ?>

</body>

</html>
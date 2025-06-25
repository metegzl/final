<?php
session_start();
require_once("connection.php");

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["session_code"])) {
  $code = $_POST["session_code"];

  $stmt = $conn->prepare("SELECT id FROM sessions WHERE session_code = ?");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $result = $stmt->get_result();
  $session_id = $result->fetch_assoc()["id"] ?? null;
  $stmt->close();

  if ($session_id) {
    $conn->query("DELETE FROM session_attendees WHERE session_id = $session_id");
    $conn->query("DELETE FROM chat_messages WHERE session_id = $session_id");
    $conn->query("DELETE FROM sessions WHERE id = $session_id");
    $success = true;
  } else {
    $errorMsg = "Oturum bulunamadı.";
  }
  $conn->close();
} else {
  $errorMsg = "Geçersiz istek.";
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <title>Session Ended</title>
  <style>
    .toast {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background: #28a745;
      color: #fff;
      padding: 16px 24px;
      border-radius: 8px;
      font-size: 18px;
      z-index: 1000;
    }

    .toast.error {
      background: #dc3545;
    }
  </style>
</head>

<body>

  <?php if (!empty($success) || isset($errorMsg)): ?>
    <div class="toast<?= isset($errorMsg) ? ' error' : '' ?>">
      <?= isset($errorMsg) ? '❌ ' . htmlspecialchars($errorMsg) : '✔️ Oturum sonlandırıldı.' ?>
    </div>
    <script>
      <?php if (!empty($success)): ?>
        setTimeout(function() {
          window.location.href = "anasayfa.php";
        }, 2200);
      <?php endif; ?>
    </script>
  <?php endif; ?>

</body>

</html>
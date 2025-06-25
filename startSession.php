<?php
require_once("connection.php");
session_start();

if (isset($_POST['session_code'])) {
    $code = $_POST['session_code'];
    $u = $conn->prepare("UPDATE sessions SET is_active=1 WHERE session_code=? LIMIT 1");
    $u->bind_param("s", $code);
    $u->execute();
}
header("Location: chatwall.php?code=" . $code);

<?php
session_start();

$_SESSION = array();

session_destroy();

header("Location: anasayfa.php");
exit;
?>
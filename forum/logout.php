<?php
// Oturum başlat
session_start();

// Oturumu sonlandır
session_unset();
session_destroy();

// Kullanıcıyı ana sayfaya yönlendir
header("Location: index.php");
exit();
?>

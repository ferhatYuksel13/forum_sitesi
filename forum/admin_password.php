<?php
// Admin şifresi
$password = 'admin123';  // Admin için belirlediğiniz şifre

// Şifreyi hash'le
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Ekranda hash'i göster
echo $hashed_password;
?>
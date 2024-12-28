<?php
include('db_connect.php'); // Veritabanı bağlantısı

// Admin kullanıcı oluşturma işlemi (bu sayfa sadece bir defa çalıştırılacak)
$username = 'admin_username';  // Admin kullanıcı adı
$email = 'admin_email@example.com';  // Admin e-posta adresi
$password = 'admin_password';  // Admin şifresi

// Şifreyi hashle
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Admin kullanıcısını ekle
$sql = "INSERT INTO users (username, email, password, is_admin) VALUES ('$username', '$email', '$hashed_password', 1)";
if ($conn->query($sql) === TRUE) {
    echo "Admin kullanıcısı başarıyla eklendi.";
} else {
    echo "Hata: " . $conn->error;
}

$conn->close();
?>

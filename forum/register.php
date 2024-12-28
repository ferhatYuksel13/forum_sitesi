<?php
include('db_connect.php'); // Veritabanı bağlantısını dahil et

// Form gönderildiyse işlemi başlat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Parolayı şifrele
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kullanıcıyı ekle
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        echo "Yeni kullanıcı başarıyla eklendi.";
        // Başarılı ekleme sonrası giriş sayfasına yönlendirilebilir
        header("Location: login.php");
    } else {
        echo "Hata: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="register.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Hasbihal - Kayıt Ol</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li>
                <li><a href="register.php"><i class="fa-solid fa-user-plus"></i></a></li>
                
            </ul>
        </nav>
    </header>

    <main>
        <section class="register-form">
            <h2>Hesap Oluşturun</h2>
            <form action="register.php" method="POST">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" id="username" placeholder="Kullanıcı adınızı girin" required>
                
                <label for="email">E-posta Adresi:</label>
                <input type="email" name="email" id="email" placeholder="E-posta adresinizi girin" required>
                
                <label for="password">Parola:</label>
                <input type="password" name="password" id="password" placeholder="Parolanızı girin" required>
                
                <button type="submit">Kayıt Ol</button>
            </form>
        </section>
    </main>
    
</body>
</html>

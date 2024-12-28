<?php
session_start();
include('db_connect.php'); // Veritabanı bağlantısını dahil et

// Giriş kontrolü
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Eğer kullanıcı zaten giriş yaptıysa ana sayfaya yönlendir
    exit();
}

// Form gönderildiyse işlemi başlat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanında ara
    $sql = "SELECT id, username, password, is_admin FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Parolayı doğrula
        if (password_verify($password, $row['password'])) {
            // Başarıyla giriş yaptı
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['is_admin'] = $row['is_admin'];

            // Kullanıcı admin ise admin paneline yönlendir
            if ($row['is_admin'] == 1) {
                header("Location: admin_panel.php");
            } else {
                header("Location: index.php"); // Normal kullanıcıyı ana sayfaya yönlendir
            }
            exit();
        } else {
            $error_message = "Geçersiz parola.";
        }
    } else {
        $error_message = "E-posta adresi bulunamadı.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

</head>
<body>
    <header>
        <h1>Hasbihal - Giriş Yap</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li>
                <li><a href="register.php"><i class="fa-solid fa-user-plus"></i></a></li>
                
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-form">
            <h2>Hesabınıza Giriş Yapın</h2>

            <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

            <form action="login.php" method="POST">
                <label for="email">E-posta Adresi:</label>
                <input type="email" name="email" id="email" placeholder="E-posta adresinizi girin" required>
                
                <label for="password">Parola:</label>
                <input type="password" name="password" id="password" placeholder="Parolanızı girin" required>
                
                <button type="submit">Giriş Yap</button>
            </form>
        </section>
    </main>
</body>
</html>

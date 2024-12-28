<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yapmamışsa yönlendirme
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Giriş sayfasına yönlendir
    exit();
}

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "forum_db"; // Veritabanı adı

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Giriş yapmış kullanıcının ID'sini al
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini al
$sql_user = "SELECT username, email, created_at FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user && $result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
} else {
    echo "Kullanıcı bilgileri bulunamadı!";
    exit();
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user_data['username']); ?> - Profil</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Hasbihal - Profil</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="post.php"><i class="fa-solid fa-square-plus"></i></a></li>
                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li><!-- <i class="fa-solid fa-right-from-bracket"> -->
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="profile">
            <h2>Profil Bilgileriniz</h2>
            <p><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
            <p><strong>E-posta:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p><strong>Hesap Oluşturulma Tarihi:</strong> <?php echo htmlspecialchars($user_data['created_at']); ?></p>
            <a href="edit_profile.php" class="btn">Profilini Düzenle</a>
        </section>
    </main>

    <?php $conn->close(); ?>
</body>
</html>

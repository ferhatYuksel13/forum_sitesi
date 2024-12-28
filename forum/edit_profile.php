<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yapmamışsa yönlendirme
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "forum_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Giriş yapmış kullanıcının ID'sini al
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini al
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    // Kullanıcı bilgilerini güncelle
    $sql_update = "UPDATE users SET username = '$new_username', email = '$new_email' WHERE id = $user_id";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "Profil başarıyla güncellendi.";
        header("Location: profile.php");
        exit();
    } else {
        echo "Hata: " . $conn->error;
    }
} else {
    // Mevcut kullanıcı bilgilerini al
    $sql_user = "SELECT username, email FROM users WHERE id = $user_id";
    $result_user = $conn->query($sql_user);
    
    if ($result_user && $result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
    } else {
        echo "Kullanıcı bilgileri bulunamadı!";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Düzenle</title>
    <link rel="stylesheet" href="edit_profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

</head>
<body>
    <header>
        <h1>Hasbihal - Profil Düzenle</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <li><a href="post.php"><i class="fa-solid fa-square-plus"></i></a></li>
                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="edit-profile">
            <h2>Profilini Düzenle</h2>
            <form action="edit_profile.php" method="POST">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                
                <label for="email">E-posta:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                
                <button type="submit">Güncelle</button>
            </form>
        </section>
    </main>


    <?php $conn->close(); ?>
</body>
</html>

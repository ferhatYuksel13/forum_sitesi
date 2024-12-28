<?php
session_start(); // Oturum başlat

// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "forum_db"; // Veritabanı adı

// MySQL bağlantısı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kategorileri veritabanından çekme
$sql = "SELECT id, category_name FROM categories"; // veya title
$result = $conn->query($sql);

// Kullanıcının giriş yapıp yapmadığını kontrol et
$is_logged_in = isset($_SESSION['user_id']); // Eğer session'da 'user_id' varsa, kullanıcı giriş yapmış demektir
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Ana Sayfa</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Hasbihal Forum Sayfası</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li> <!-- anasayfa -->
                <?php if ($is_logged_in) { ?>
                    <li><a href="post.php"><i class="fa-solid fa-square-plus"></i></a></li> <!-- Konu Ekle Butonu -->
                <?php } ?>

                <?php if ($is_logged_in) { ?>
                    <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li><!-- çıkış -->
                    <li><a href="profile.php"><i class="fa-solid fa-user"></i></a></li><!-- profil -->

                <?php } else { ?>
                    <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li><!-- giriş -->
                    <li><a href="register.php"><i class="fa-solid fa-user-plus"></i></a></li><!-- kayıt ol -->
                <?php } ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section>
            <h2>Kategoriler</h2>
            <?php
            if ($result->num_rows > 0) {
                // Kategorileri listeleme
                while($row = $result->fetch_assoc()) {
                    echo "<div class='category'>";
                    echo "<h3><a href='category.php?id=" . $row["id"] . "'>" . $row["category_name"] . "</a></h3>";
                    echo "</div>";
                }
            } else {
                echo "<p>Henüz kategori bulunmamaktadır.</p>";
            }
            ?>
        </section>
    </main>

    <footer>
        <a href="https://github.com/ferhatYuksel13"><i class="fa-brands fa-github "></i></a>
        <a href=https://www.youtube.com/@ferhatyuksel7367><i class="fa-brands fa-youtube"></i></a>
    </footer>

    <?php $conn->close(); ?>
</body>
</html>


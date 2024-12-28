<?php
session_start(); // Oturum başlatma

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

// Kategori ID'sini al
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id == 0) {
    echo "Geçersiz kategori!";
    exit;
}

// Kategorinin adını almak için sorgu
$sql_category = "SELECT category_name FROM categories WHERE id = $category_id";
$result_category = $conn->query($sql_category);

if ($result_category && $result_category->num_rows > 0) {
    $category_name = $result_category->fetch_assoc()['category_name'];
} else {
    echo "Kategori bulunamadı!";
    exit;
}

// Kategoriye ait konuları çekme
$sql_topics = "SELECT id, topic_title, created_at FROM topics WHERE category_id = $category_id";
$result_topics = $conn->query($sql_topics);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - Forum</title>
    <link rel="stylesheet" href="category.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

</head>
<body>
    <header>
        <h1>Hasbihal - <?php echo htmlspecialchars($category_name); ?></h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
                    <li><a href="profil.php"><i class="fa-solid fa-user"></i></a></li>
                <?php } else { ?>
                    <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i></a></li>
                    <li><a href="register.php"><i class="fa-solid fa-user-plus"></i></a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section>
            <h2><?php echo htmlspecialchars($category_name); ?> Kategorisi</h2>
            
            <?php
            if ($result_topics && $result_topics->num_rows > 0) {
                while ($row = $result_topics->fetch_assoc()) {
                    echo "<div class='topic'>";
                    echo "<h3><a href='topic.php?id=" . $row["id"] . "'>" . htmlspecialchars($row["topic_title"]) . "</a></h3>";
                    echo "<p>Oluşturulma Tarihi: " . htmlspecialchars($row["created_at"]) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Bu kategoride henüz konu bulunmamaktadır.</p>";
            }
            ?>
            
            <a href="post.php?category_id=<?php echo $category_id; ?>" class="btn">Yeni Konu Başlat</a>
        </section>
    </main>

    <?php $conn->close(); ?>
</body>
</html>

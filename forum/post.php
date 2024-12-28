<?php
session_start(); // Oturum başlatma

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Giriş yapılmadıysa login.php sayfasına yönlendir
    exit;
}

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

// Kategorileri al
$sql_categories = "SELECT id, category_name FROM categories";
$result_categories = $conn->query($sql_categories);

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $topic_title = $conn->real_escape_string($_POST['topic_title']);
    $topic_content = $conn->real_escape_string($_POST['topic_content']);
    $user_id = (int)$_SESSION['user_id']; // Giriş yapan kullanıcının ID'si

    // Veritabanına yeni konu ekle
    $sql_insert = "INSERT INTO topics (category_id, topic_title, topic_content, user_id, created_at) 
                   VALUES ('$category_id', '$topic_title', '$topic_content', '$user_id', NOW())";
    if ($conn->query($sql_insert) === TRUE) {
        echo "Yeni konu başarıyla eklendi.";
    } else {
        echo "Hata: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Konu Başlat</title>
    <link rel="stylesheet" href="post.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
</head>
<body>
    <header>
        <h1>Hasbihal - Yeni Konu Başlat</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-house"></i></a></li>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li><!-- <i class="fa-solid fa-right-from-bracket"> -->
                    <li><a href="profile.php"><i class="fa-solid fa-user"></i></a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="post-form">
            <h2>Yeni Konu Başlat</h2>
            <form action="post.php" method="POST">
                <label for="category_id">Kategori Seçin:</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Kategori Seçin</option>
                    <?php
                    if ($result_categories->num_rows > 0) {
                        while($row = $result_categories->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Kategori bulunamadı</option>";
                    }
                    ?>
                </select>
                
                <label for="topic_title">Konu Başlığı:</label>
                <input type="text" name="topic_title" id="topic_title" placeholder="Konu başlığını girin" required>
                
                <label for="topic_content">Konu İçeriği:</label>
                <textarea class="form-control" name="topic_content" id="summernote"  required></textarea>
                <button type="submit" class="btn btn-primary">Konuyu Başlat</button>
            </form>
        </section>
    </main>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $('#summernote ').summernote({
            placeholder : "Konu içeriğini girin",
            height: 300

            
        });
    </script>
    <?php $conn->close(); ?>
</body>
</html>

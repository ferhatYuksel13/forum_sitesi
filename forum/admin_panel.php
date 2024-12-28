<?php
session_start();
include('db_connect.php'); // Veritabanı bağlantısını dahil et

// Oturum ve admin kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$sql = "SELECT is_admin FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if ($user['is_admin'] != 1) {
        die("Bu sayfaya erişim izniniz yok.");
    }
} else {
    die("Kullanıcı bilgisi bulunamadı.");
}

// Tablo işlemleri
$message = "";

// Kullanıcı güncelleme
if (isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $sql_update_user = "UPDATE users SET username = '$username', email = '$email', is_admin = $is_admin WHERE id = $user_id";
    if ($conn->query($sql_update_user)) {
        $message = "Kullanıcı başarıyla güncellendi.";
    } else {
        $message = "Kullanıcı güncellenirken hata oluştu.";
    }
}

// Kategori güncelleme
if (isset($_POST['update_category'])) {
    $category_id = (int)$_POST['category_id'];
    $category_name = $conn->real_escape_string($_POST['category_name']);

    $sql_update_category = "UPDATE categories SET category_name = '$category_name' WHERE id = $category_id";
    if ($conn->query($sql_update_category)) {
        $message = "Kategori başarıyla güncellendi.";
    } else {
        $message = "Kategori güncellenirken hata oluştu.";
    }
}

// Konu güncelleme
if (isset($_POST['update_topic'])) {
    $topic_id = (int)$_POST['topic_id'];
    $topic_title = $conn->real_escape_string($_POST['topic_title']);
    $topic_content = $conn->real_escape_string($_POST['topic_content']);

    $sql_update_topic = "UPDATE topics SET topic_title = '$topic_title', topic_content = '$topic_content' WHERE id = $topic_id";
    if ($conn->query($sql_update_topic)) {
        $message = "Konu başarıyla güncellendi.";
    } else {
        $message = "Konu güncellenirken hata oluştu.";
    }
}

// Tabloları listeleme
$users = $conn->query("SELECT * FROM users");
$categories = $conn->query("SELECT * FROM categories");
$topics = $conn->query("SELECT topics.*, categories.category_name FROM topics LEFT JOIN categories ON topics.category_id = categories.id");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

</head>
<body>
    <header>
        <h1>Admin Paneli</h1>
        <p>Hoş geldiniz, Admin!</p>
    </header>

    <main>
        <!-- Yeni Admin Ekleme Formu -->
        <section>
            <h2>Yeni Admin Ekle</h2>
            <?php if ($message) echo "<p class='message'>$message</p>"; ?>
            <form method="POST">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" id="username" required>

                <label for="email">E-posta Adresi:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Parola:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit" name="add_admin">Admin Ekle</button>
            </form>
        </section>

        <!-- Kullanıcı Yönetimi -->
        <section>
            <h2>Kullanıcı Yönetimi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Admin</th>
                        <th>Güncelle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST">
                            <td><?php echo $user['id']; ?></td>
                            <td><input type="text" name="username" value="<?php echo $user['username']; ?>"></td>
                            <td><input type="email" name="email" value="<?php echo $user['email']; ?>"></td>
                            <td><input type="checkbox" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>></td>
                            <td>
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="update_user">Güncelle</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <!-- Kategori Yönetimi -->
        <section>
            <h2>Kategori Yönetimi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategori Adı</th>
                        <th>Güncelle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $categories->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST">
                            <td><?php echo $category['id']; ?></td>
                            <td><input type="text" name="category_name" value="<?php echo $category['category_name']; ?>"></td>
                            <td>
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="update_category">Güncelle</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <!-- Konu Yönetimi -->
        <section>
            <h2>Konu Yönetimi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Konu Başlığı</th>
                        <th>Kategori</th>
                        <th>İçerik</th>
                        <th>Güncelle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($topic = $topics->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST">
                            <td><?php echo $topic['id']; ?></td>
                            <td><input type="text" name="topic_title" value="<?php echo $topic['topic_title']; ?>"></td>
                            <td><input type="text" name="category_name" value="<?php echo $topic['category_name']; ?>" disabled></td>
                            <td><textarea name="topic_content"><?php echo $topic['topic_content']; ?></textarea></td>
                            <td>
                                <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                                <button type="submit" name="update_topic">Güncelle</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>

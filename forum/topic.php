<?php
session_start();
include('db_connect.php'); // Veritabanı bağlantısını dahil et

// Hata raporlamayı aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Konu ID'sini al
$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($topic_id == 0) {
    die("Geçersiz konu.");
}

// Veritabanı sorgularını başlat
$sql_topic = "SELECT topics.*, users.username 
              FROM topics 
              LEFT JOIN users ON topics.user_id = users.id 
              WHERE topics.id = $topic_id";
$result_topic = $conn->query($sql_topic);

if ($result_topic && $result_topic->num_rows > 0) {
    $topic = $result_topic->fetch_assoc();
} else {
    die("Konu bulunamadı.");
}

// Yorumları al
$sql_posts = "SELECT posts.*, users.username, posts.id AS post_id, 
                     (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS likes_count 
              FROM posts 
              LEFT JOIN users ON posts.user_id = users.id 
              WHERE posts.topic_id = $topic_id 
              ORDER BY posts.created_at DESC";
$result_posts = $conn->query($sql_posts);

// Yorum ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['content'])) {
    $user_id = (int)$_SESSION['user_id'];
    $content = $conn->real_escape_string($_POST['content']);

    if (!empty($content)) {
        // Yorum ekle
        $sql_insert_post = "INSERT INTO posts (topic_id, user_id, post_content, created_at) 
                            VALUES ($topic_id, $user_id, '$content', NOW())";
        if ($conn->query($sql_insert_post) === TRUE) {
            header("Location: topic.php?id=$topic_id"); // Yorum eklendikten sonra aynı konuya yönlendir
            exit;
        } else {
            $error_message = "Yorum eklenirken bir hata oluştu: " . $conn->error;
        }
    } else {
        $error_message = "Yorum içeriği boş olamaz.";
    }
}

// Yorum beğenisi ekleme
if (isset($_POST['like']) && isset($_SESSION['user_id']) && isset($_POST['post_id'])) {
    $post_id = (int)$_POST['post_id'];
    $user_id = (int)$_SESSION['user_id'];
    
    // Beğeni ekle veya kaldır
    $sql_check_like = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id";
    $result_like = $conn->query($sql_check_like);
    
    if ($result_like->num_rows == 0) {
        $sql_insert_like = "INSERT INTO likes (post_id, user_id) VALUES ($post_id, $user_id)";
        $conn->query($sql_insert_like);
    } else {
        $sql_delete_like = "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id";
        $conn->query($sql_delete_like);
    }
    
    header("Location: topic.php?id=$topic_id"); // Sayfayı yenile
    exit;
}

// Yorumlara cevap ekleme
if (isset($_POST['reply_content']) && isset($_POST['reply_to']) && isset($_SESSION['user_id'])) {
    $reply_content = $conn->real_escape_string($_POST['reply_content']);
    $reply_to = (int)$_POST['reply_to'];
    $user_id = (int)$_SESSION['user_id'];
    
    if (!empty($reply_content)) {
        $sql_insert_reply = "INSERT INTO posts (topic_id, user_id, post_content, created_at, reply_to) 
                             VALUES ($topic_id, $user_id, '$reply_content', NOW(), $reply_to)";
        $conn->query($sql_insert_reply);
    }
    
    header("Location: topic.php?id=$topic_id"); // Sayfayı yenile
    exit;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($topic['topic_title']); ?> - Forum</title>
    <link rel="stylesheet" href="topic.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <header>
        <h1>Hasbihal - <?php echo htmlspecialchars($topic['topic_title']); ?></h1>
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
        <section class="topic-details">
            <h2><?php echo htmlspecialchars($topic['topic_title']); ?></h2>
            <p><strong>Yazar:</strong> <?php echo htmlspecialchars($topic['username'] ?? 'Bilinmiyor'); ?> | 
               <strong>Tarih:</strong> <?php echo htmlspecialchars($topic['created_at'] ?? 'Tarih bilgisi yok'); ?></p>
            <p><?php echo nl2br(htmlspecialchars($topic['topic_content'] ?? 'İçerik bulunmuyor')); ?></p>
        </section>

        <section class="comments">
            <h3>Yorumlar</h3>

            <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

            <?php 
            if ($result_posts && $result_posts->num_rows > 0) {
                while ($post = $result_posts->fetch_assoc()) { ?>
                    <div class="comment">
                        <p><strong><?php echo htmlspecialchars($post['username']); ?></strong> - 
                           <?php echo htmlspecialchars($post['created_at']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($post['post_content'])); ?></p>
                        <form action="topic.php?id=<?php echo $topic_id; ?>" method="POST">
                            <button type="submit" name="like" value="1">
                                👍 <?php echo $post['likes_count']; ?> Beğen
                            </button>
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                        </form>
                        <form action="topic.php?id=<?php echo $topic_id; ?>" method="POST">
                            <textarea name="reply_content"  id="summernote" rows="2"></textarea>
                            <button type="submit">Cevapla</button>
                            <input type="hidden" name="reply_to" value="<?php echo $post['post_id']; ?>">
                        </form>
                        <?php 
                        // Cevapları göster
                        $sql_replies = "SELECT posts.*, users.username 
                                        FROM posts 
                                        LEFT JOIN users ON posts.user_id = users.id 
                                        WHERE posts.reply_to = " . $post['post_id'];
                        $result_replies = $conn->query($sql_replies);

                        if ($result_replies && $result_replies->num_rows > 0) {
                            while ($reply = $result_replies->fetch_assoc()) { ?>
                                <div class="reply">
                                    <p><strong><?php echo htmlspecialchars($reply['username'] ?? 'Bilinmiyor'); ?></strong> - 
                                    <?php echo htmlspecialchars($reply['created_at'] ?? 'Tarih bilgisi yok'); ?></p>
                                    <p><?php echo nl2br(htmlspecialchars($reply['post_content'] ?? 'İçerik bulunmuyor')); ?></p>
                                </div>
                            <?php }
                        } ?>
                    </div>
                <?php }
            } else {
                echo "<p>Bu konuda henüz yorum yapılmamış.</p>";
            }
            ?>

            <?php if (isset($_SESSION['user_id'])) { ?>
                <form action="topic.php?id=<?php echo $topic_id; ?>" method="POST">
                    <textarea name="content" id="summernote"  rows="3"></textarea>
                    <button type="submit">Yorum Yap</button>
                </form>
            <?php } else { ?>
                <p>Yorum yapabilmek için giriş yapmalısınız.</p>
            <?php } ?>
        </section>
    </main>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script type="text/javascript">
        $('#summernote ').summernote({
            height: 300,

            
        });
    </script>
</body>
</html>

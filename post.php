<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$postId = $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT title, content, author, created_at FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    $pageTitle = "Không tìm thấy bài viết";
} else {
    $pageTitle = htmlspecialchars($post['title']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Blog Lập Trình</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1><a href="index.php">Blog Lập Trình</a></h1>
    </header>

    <main class="container">
        <?php if ($post): ?>
            <article class="single-post">
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <p class="post-meta">bởi <?php echo htmlspecialchars($post['author']); ?> vào <?php echo date("d/m/Y H:i", strtotime($post['created_at'])); ?></p>
                <div class="post-content">
                    <?php
                        echo nl2br(htmlspecialchars($post['content']));
                    ?>
                </div>
            </article>
        <?php else: ?>
            <h2>404 - Không tìm thấy bài viết</h2>
            <p>Bài viết bạn đang tìm kiếm không tồn tại. Vui lòng quay lại <a href="index.php">trang chủ</a>.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Blog Lập Trình Của Tôi</p>
    </footer>
    
    <script src="main.js"></script>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<?php
// file: post.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'database.php';

// ... (phần code lấy thông tin bài viết giữ nguyên) ...
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit;
}
$post_id = $_GET['id'];

$sql_post = "SELECT p.id, p.title, p.content, p.author, p.image, p.created_at FROM posts p WHERE p.id = ?";
$stmt_post = mysqli_prepare($conn, $sql_post);
mysqli_stmt_bind_param($stmt_post, "i", $post_id);
mysqli_stmt_execute($stmt_post);
$result_post = mysqli_stmt_get_result($stmt_post);
$post = mysqli_fetch_assoc($result_post);

if (!$post) {
    header("location: index.php");
    exit;
}


// Xử lý khi người dùng gửi bình luận mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (isset($_SESSION['loggedin'])) { 
        $comment_text = trim($_POST['comment']);
        if (!empty($comment_text)) {
            // THAY ĐỔI Ở ĐÂY: Thêm cột `created_at` vào câu lệnh SQL
            $sql_comment = "INSERT INTO comments (post_id, user_id, author, comment, created_at) VALUES (?, ?, ?, ?, ?)";
            $stmt_comment = mysqli_prepare($conn, $sql_comment);

            // THAY ĐỔI Ở ĐÂY: Lấy giờ hiện tại của Việt Nam
            $current_time = date("Y-m-d H:i:s");

            // THAY ĐỔI Ở ĐÂY: Thêm biến $current_time và đổi kiểu dữ liệu thành "iisss"
            mysqli_stmt_bind_param($stmt_comment, "iisss", $post_id, $_SESSION['user_id'], $_SESSION['username'], $comment_text, $current_time);
            mysqli_stmt_execute($stmt_comment);
            
            header("Location: post.php?id=$post_id");
            exit;
        }
    }
}

// ... (phần code lấy danh sách bình luận và HTML giữ nguyên) ...
$comments = [];
$sql_get_comments = "SELECT author, comment, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC";
$stmt_get_comments = mysqli_prepare($conn, $sql_get_comments);
if ($stmt_get_comments === false) {
    die("LỖI CHUẨN BỊ SQL (comments): " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_get_comments, "i", $post_id);
mysqli_stmt_execute($stmt_get_comments);
$result_comments = mysqli_stmt_get_result($stmt_get_comments);
while ($row = mysqli_fetch_assoc($result_comments)) {
    $comments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Blog Công Nghệ</a></h1>
    </header>
    <main class="container">
        <article class="post-full">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></p>
             <?php if ($post['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image-full">
            <?php endif; ?>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </article>
        <section class="comments-section">
            <h2>Bình luận</h2>
            <?php if (isset($_SESSION['loggedin'])): ?>
                <form action="post.php?id=<?php echo $post_id; ?>" method="POST" class="comment-form">
                    <div class="form-group">
                        <label for="comment">Viết bình luận của bạn:</label>
                        <textarea name="comment" id="comment" rows="4" required></textarea>
                    </div>
                    <button type="submit">Gửi bình luận</button>
                </form>
            <?php else: ?>
                <p>Vui lòng <a href="dangnhap.php">đăng nhập</a> để bình luận.</p>
            <?php endif; ?>
            <div class="comment-list">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <p><strong><?php echo htmlspecialchars($comment['author']); ?></strong> <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span></p>
                            <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html> 
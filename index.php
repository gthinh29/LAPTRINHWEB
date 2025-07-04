<?php
session_start();
require 'database.php'; // Kết nối CSDL của bạn

$posts = [];
$search_query = "";
// Câu lệnh SQL mặc định để lấy tất cả bài viết
$sql = "SELECT id, title, author, content, image, created_at FROM posts ORDER BY created_at DESC";

// Nếu có từ khóa tìm kiếm
if (!empty($_GET['q'])) {
    $search_query = trim($_GET['q']);
    // Thay đổi câu lệnh SQL để tìm kiếm
    $sql = "SELECT id, title, author, content, image, created_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like_query = '%' . $search_query . '%';
    mysqli_stmt_bind_param($stmt, "ss", $like_query, $like_query);
} else {
    $stmt = mysqli_prepare($conn, $sql);
}

// Thực thi và lấy kết quả
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Blog Công Nghệ</a></h1>
        <nav>
            <?php if (isset($_SESSION['loggedin'])): ?>
                <span>Chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php">Trang Admin</a>
                <?php endif; ?>
                <a href="logout.php">Đăng xuất</a>
            <?php else: ?>
                <a href="dangnhap.php">Đăng nhập</a>
                <a href="dangky.php">Đăng ký</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <section class="search-bar">
            <form action="index.php" method="GET">
                <input type="text" name="q" placeholder="Tìm kiếm bài viết..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
        </section>

        <section class="post-list">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-summary">
                        <h2><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></p>
                        <?php if ($post['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
                        <?php endif; ?>
                        <div class="post-excerpt">
                            <?php // Hiển thị 300 ký tự đầu tiên của nội dung
                                echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))); 
                            ?>...
                        </div>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm &rarr;</a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không tìm thấy bài viết nào.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
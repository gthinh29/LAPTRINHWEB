<?php
session_start();
// Kiểm tra nếu người dùng đã đăng nhập và có phải là admin không
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('location: ../index.php'); // Chuyển về trang chủ nếu không phải admin
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <span>Chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
            <a href="../index.php">Xem trang chủ</a>
            <a href="../logout.php">Đăng xuất</a>
        </nav>
    </header>
    <main class="container">
        <h2>Bảng điều khiển</h2>
        <ul>
            <li><a href="manage_posts.php">Quản lý bài viết</a></li>
            <li><a href="add_post.php">Thêm bài viết mới</a></li>
            </ul>
    </main>
</body>
</html>
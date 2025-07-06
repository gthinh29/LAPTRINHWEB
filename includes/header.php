<?php
// file: includes/header.php (đã bỏ hoàn toàn role)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Blog Công Nghệ'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Blog Công Nghệ</a></h1>
        <nav>
            <?php if (isset($_SESSION['loggedin'])): ?>
                <span>Chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                <a href="admin/manage_posts.php">Trang Admin</a>
                <a href="logout.php">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
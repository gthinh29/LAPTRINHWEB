<?php
// file: includes/header.php
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
            <form action="search.php" method="get" class="search-form">
                <input type="search" name="query" placeholder="Tìm kiếm bài viết..." required>
                <button type="submit">Tìm</button>
            </form>
            <?php if (isset($_SESSION['loggedin'])): ?>
                <span class="welcome-message">Chào,
                    <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
                <a href="admin/manage_posts.php">Trang Admin</a>
                <a href="logout.php">Đăng xuất</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
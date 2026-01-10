<?php
// file: includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';

$sql_nav_categories = "SELECT id, name FROM categories ORDER BY name ASC";
$result_nav_categories = mysqli_query($conn, $sql_nav_categories);
$nav_categories = [];
if ($result_nav_categories) {
    while ($row = mysqli_fetch_assoc($result_nav_categories)) {
        $nav_categories[] = $row;
    }
}

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
            <?php if (!empty($nav_categories)): ?>
                <div class="dropdown">
                    <a href="#" class="dropbtn">Danh Mục</a>
                    <div class="dropdown-content">
                        <?php foreach ($nav_categories as $category): ?>
                            <a href="category.php?id=<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
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
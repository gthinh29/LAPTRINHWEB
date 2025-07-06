<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

$posts = [];
$sql = "SELECT id, title, author, created_at FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header>
        <h1>Quản lý bài viết</h1>
        <nav>
            <span>Chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
            <a href="categories/manage_categories.php">Quản lý Danh mục</a>
            <a href="../index.php">Xem trang chủ</a>
            <a href="../logout.php">Đăng xuất</a>
        </nav>
    </header>
    <main class="container">
        <a href="add_post.php" class="button-add-new">Thêm bài viết mới</a>
        <br><br>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Tác giả</th>
                    <th>Ngày đăng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="edit-btn">Sửa</a>
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="delete-btn"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Chưa có bài viết nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>

</html>
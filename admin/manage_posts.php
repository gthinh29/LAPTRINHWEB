<?php
session_start();
require '../database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('location: ../index.php');
    exit;
}

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
    <header><h1>Quản lý bài viết</h1></header>
    <main class="container">
        <a href="dashboard.php">&larr; Quay lại Dashboard</a> | <a href="add_post.php">Thêm bài viết mới</a>
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
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                    <td>
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="edit-btn">Sửa</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
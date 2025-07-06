<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../../dangnhap.php");
    exit;
}
require '../../includes/database.php';

$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
    <link rel="stylesheet" href="../../style.css">
</head>

<body>
    <header>
        <h1>Quản lý Danh mục</h1>
        <nav>
            <a href="../manage_posts.php">Quản lý Bài viết</a>
            <a href="../../logout.php">Đăng xuất</a>
        </nav>
    </header>
    <main class="container">
        <a href="add_category.php" class="button-add-new">Thêm Danh mục mới</a>
        <br><br>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tên Danh mục</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($category['created_at'])); ?></td>
                            <td>
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="edit-btn">Sửa</a>
                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="delete-btn"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không? Các bài viết thuộc danh mục này sẽ không bị xóa.');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">Chưa có danh mục nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>

</html>
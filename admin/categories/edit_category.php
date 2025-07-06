<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../../dangnhap.php");
    exit;
}
require '../../includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: manage_categories.php");
    exit;
}
$id = $_GET['id'];
$error = '';

// Lấy thông tin danh mục hiện tại
$sql_get = "SELECT name FROM categories WHERE id = ?";
$stmt_get = mysqli_prepare($conn, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$category = mysqli_fetch_assoc($result_get);

if (!$category) {
    header("location: manage_categories.php");
    exit;
}
$name = $category['name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    if (empty($new_name)) {
        $error = "Tên danh mục không được để trống.";
    } else {
        $sql_update = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $new_name, $id);
        if (mysqli_stmt_execute($stmt_update)) {
            header("location: manage_categories.php");
            exit;
        } else {
            $error = "Cập nhật thất bại. Tên danh mục có thể đã tồn tại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Danh mục</title>
    <link rel="stylesheet" href="../../style.css">
</head>

<body>
    <header>
        <h1>Sửa Danh mục</h1>
    </header>
    <main class="container">
        <a href="manage_categories.php">← Quay lại</a>
        <form class="admin-form" action="edit_category.php?id=<?php echo $id; ?>" method="post">
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="name">Tên Danh mục</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Cập nhật">
            </div>
        </form>
    </main>
</body>

</html>
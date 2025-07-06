<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../../dangnhap.php");
    exit;
}
require '../../includes/database.php';

$name = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $error = "Tên danh mục không được để trống.";
    } else {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_categories.php");
            exit;
        } else {
            $error = "Thêm thất bại. Tên danh mục có thể đã tồn tại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Danh mục</title>
    <link rel="stylesheet" href="../../style.css">
</head>

<body>
    <header>
        <h1>Thêm Danh mục mới</h1>
    </header>
    <main class="container">
        <a href="manage_categories.php">← Quay lại</a>
        <form class="admin-form" action="add_category.php" method="post">
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="name">Tên Danh mục</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Thêm mới">
            </div>
        </form>
    </main>
</body>

</html>
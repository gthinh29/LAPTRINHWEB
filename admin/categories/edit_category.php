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
    <?php require_once '../../includes/header.php'; ?>
    <main class="container">
        <div class="form-container" style="max-width: 700px; margin: 3rem auto;">
            <div class="form-header">
                <h1>Sửa Danh mục</h1>
                <a href="manage_categories.php" class="back-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"></path>
                    </svg>
                    <span>Quay lại</span>
                </a>
            </div>

            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form class="admin-form" action="edit_category.php?id=<?php echo $id; ?>" method="post">
                <div class="form-group">
                    <label for="name">Tên Danh mục</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Cập nhật">
                </div>
            </form>
        </div>
    </main>
</body>

</html>
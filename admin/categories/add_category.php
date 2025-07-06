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
    <?php require_once '../../includes/header.php'; ?>
    <main class="container">
        <div class="form-container" style="max-width: 700px; margin: 3rem auto;">
            <div class="form-header">
                <h1>Thêm Danh mục mới</h1>
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

            <form class="admin-form" action="add_category.php" method="post">
                <div class="form-group">
                    <label for="name">Tên Danh mục</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-actions">
                    <input type="submit" value="Thêm mới">
                </div>
            </form>
        </div>
    </main>
</body>

</html>
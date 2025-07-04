<?php
// file: admin/add_post.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('location: ../index.php');
    exit;
}

$title = $content = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_name = '';

    if (empty($title) || empty($content)) {
        $error = "Tiêu đề và nội dung không được để trống.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $image_name = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        // THAY ĐỔI Ở ĐÂY: Thêm cột `created_at` vào câu lệnh SQL
        $sql = "INSERT INTO posts (user_id, author, title, content, image, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt === false) {
            die("LỖI CHUẨN BỊ SQL: " . mysqli_error($conn));
        }
        
        // THAY ĐỔI Ở ĐÂY: Lấy giờ hiện tại của Việt Nam
        $current_time = date("Y-m-d H:i:s");

        // THAY ĐỔI Ở ĐÂY: Thêm biến $current_time và đổi kiểu dữ liệu thành "isssss"
        mysqli_stmt_bind_param($stmt, "isssss", $_SESSION['user_id'], $_SESSION['username'], $title, $content, $image_name, $current_time);
        mysqli_stmt_execute($stmt);

        header("location: manage_posts.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm bài viết mới</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header><h1>Thêm bài viết mới</h1></header>
    <main class="container">
        <a href="dashboard.php">&larr; Quay lại Dashboard</a>
        <form action="add_post.php" method="post" enctype="multipart/form-data">
            <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="content">Nội dung</label>
                <textarea name="content" id="content" rows="10" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Ảnh đại diện (tùy chọn)</label>
                <input type="file" name="image" id="image">
            </div>
            <div class="form-group">
                <input type="submit" value="Đăng bài">
            </div>
        </form>
    </main>
</body>
</html>
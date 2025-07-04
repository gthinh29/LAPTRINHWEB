<?php
session_start();
require '../database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('location: ../index.php');
    exit;
}

$post_id = $_GET['id'];
$error = '';

// Lấy thông tin bài viết để sửa
$sql_get = "SELECT title, content FROM posts WHERE id = ?";
$stmt_get = mysqli_prepare($conn, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $post_id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$post = mysqli_fetch_assoc($result_get);

if (!$post) {
    header("location: manage_posts.php");
    exit;
}

$title = $post['title'];
$content = $post['content'];

// Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Tiêu đề và nội dung không được để trống.";
    } else {
        $image_name_update = "";
        // Xử lý upload ảnh mới (nếu có)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0 && !empty($_FILES['image']['name'])) {
            $target_dir = "../uploads/";
            $image_name_update = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name_update;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            
            $sql_update = "UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "sssi", $title, $content, $image_name_update, $post_id);
        } else {
            // Không cập nhật ảnh
            $sql_update = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "ssi", $title, $content, $post_id);
        }
        
        mysqli_stmt_execute($stmt_update);
        header("location: manage_posts.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa bài viết</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header><h1>Sửa bài viết</h1></header>
    <main class="container">
        <a href="manage_posts.php">&larr; Quay lại</a>
        <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post" enctype="multipart/form-data">
            <?php if($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Nội dung</label>
                <textarea name="content" id="content" rows="10" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Thay ảnh đại diện (để trống nếu không muốn thay)</label>
                <input type="file" name="image" id="image">
            </div>
            <div class="form-group">
                <input type="submit" value="Cập nhật">
            </div>
        </form>
    </main>
</body>
</html>
<?php
// file: admin/delete_post.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

// Kiểm tra ID bài viết có hợp lệ không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: manage_posts.php");
    exit;
}

$post_id = $_GET['id'];

// (Tùy chọn) Xóa file ảnh khỏi thư mục uploads trước khi xóa trong CSDL
$sql_get_image = "SELECT image FROM posts WHERE id = ?";
$stmt_get_image = mysqli_prepare($conn, $sql_get_image);
mysqli_stmt_bind_param($stmt_get_image, "i", $post_id);
mysqli_stmt_execute($stmt_get_image);
$result_image = mysqli_stmt_get_result($stmt_get_image);
$post = mysqli_fetch_assoc($result_image);
if ($post && !empty($post['image'])) {
    $image_path = '../uploads/' . $post['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Xóa bài viết khỏi CSDL
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $post_id);

if(mysqli_stmt_execute($stmt)) {
    header("location: manage_posts.php?status=deleted");
} else {
    header("location: manage_posts.php?status=error");
}
exit;
?>
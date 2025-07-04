<?php
session_start();
require '../database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('location: ../index.php');
    exit;
}

$post_id = $_GET['id'];
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

header("location: manage_posts.php");
exit;
?><?php
session_start();
require '../database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('location: ../index.php');
    exit;
}

$post_id = $_GET['id'];
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

header("location: manage_posts.php");
exit;
?>  
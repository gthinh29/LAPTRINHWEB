<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

if (isset($_POST['selected_posts']) && is_array($_POST['selected_posts'])) {
    $ids_to_delete = $_POST['selected_posts'];

    $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));

    $types = str_repeat('i', count($ids_to_delete));

    $sql = "DELETE FROM posts WHERE id IN ($placeholders)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$ids_to_delete);

    mysqli_stmt_execute($stmt);
}

header("location: manage_posts.php");
exit;
?>
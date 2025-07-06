<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../../dangnhap.php");
    exit;
}

require '../../includes/database.php';

if (isset($_POST['selected_categories']) && is_array($_POST['selected_categories'])) {
    $ids_to_delete = $_POST['selected_categories'];

    $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
    $types = str_repeat('i', count($ids_to_delete));

    $sql = "DELETE FROM categories WHERE id IN ($placeholders)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$ids_to_delete);
    mysqli_stmt_execute($stmt);

    $sql_update_posts = "UPDATE posts SET category_id = NULL WHERE category_id IN ($placeholders)";
    $stmt_update = mysqli_prepare($conn, $sql_update_posts);
    mysqli_stmt_bind_param($stmt_update, $types, ...$ids_to_delete);
    mysqli_stmt_execute($stmt_update);

}

header("location: manage_categories.php");
exit;
?>
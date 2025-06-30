<?php
require_once '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $content = htmlspecialchars($_POST['content']);

    if (empty($title) || empty($author) || empty($content)) {
        die("Lỗi: Vui lòng điền đầy đủ thông tin.");
    }

    $sql = "INSERT INTO posts (title, author, content) VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $title, $author, $content);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../index.php?status=success");
            exit();
        } else {
            echo "Lỗi: Không thể thực thi câu lệnh. " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Lỗi: Không thể chuẩn bị câu lệnh. " . mysqli_error($conn);
    }

    mysqli_close($conn);

} else {
    header("Location: ../index.php");
    exit();
}
?>
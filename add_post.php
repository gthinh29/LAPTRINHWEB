<?php
include 'database.php';
$title = $_POST['title'];
$content = $_POST['content'];

$query = "INSERT INTO Posts (title, content, created_at) VALUES (?, ?, GETDATE())";
$params = array($title, $content);
sqlsrv_query($conn, $query, $params);
header("Location: admin.php");

$targetDir = "images/";
$imageName = basename($_FILES["image"]["name"]);
$targetFile = $targetDir . $imageName;
move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

$query = "INSERT INTO Posts (title, content, image, created_at) VALUES (?, ?, ?, GETDATE())";
$params = [$title, $content, $imageName];
sqlsrv_query($conn, $query, $params);
?>


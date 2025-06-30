<?php
include 'db.php';

$sql = "SELECT id, title, author, created_at FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Lập Trình Của Tôi</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <header>
        <h1>Blog Lập Trình</h1>
        <p>Nơi chia sẻ kiến thức và kinh nghiệm</p>
        <a href="admin/add_post.php" style="color: white; background: #007bff; padding: 5px 10px; border-radius: 4px; text-decoration: none;">+ Tạo bài viết mới</a>
    </header>

    <main class="container">
        <h2>Các bài viết mới nhất</h2>
        <div class="post-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<article class="post-item">';
                    echo '<h3><a href="post.php?id=' . $row["id"] . '">' . htmlspecialchars($row["title"]) . '</a></h3>';
                    echo '<p class="post-meta">bởi ' . htmlspecialchars($row["author"]) . ' vào ' . date("d/m/Y", strtotime($row["created_at"])) . '</p>';
                    echo '</article>';
                }
            } else {
                echo "<p>Chưa có bài viết nào.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Blog Lập Trình Của Tôi</p>
    </footer>

    <button id="scrollTopBtn" title="Lên đầu trang">▲</button>

    <script src="main.js"></script> </body>
</html>

<?php
mysqli_close($conn);
?>
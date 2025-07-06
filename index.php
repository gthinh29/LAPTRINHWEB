<?php

$page_title = "Trang chủ - Blog Công Nghệ";

require 'includes/header.php';

$posts_per_page = 5;
$count_sql = "SELECT COUNT(id) AS total FROM posts";
$count_result = mysqli_query($conn, $count_sql);
$total_posts = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_posts / $posts_per_page);
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($current_page > $total_pages && $total_pages > 0)
    $current_page = $total_pages;
if ($current_page < 1)
    $current_page = 1;
$offset = ($current_page - 1) * $posts_per_page;

$sql = "SELECT id, title, author, content, image, created_at FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $posts_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$posts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}
?>

<section class="post-list">
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <article class="post-summary">
                <h2><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></p>
                <?php if ($post['image']): ?>
                    <a href="post.php?id=<?php echo $post['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
                    </a>
                <?php endif; ?>

                <div class="post-excerpt">
                    <?php
                    // === LOGIC MỚI, AN TOÀN ĐỂ TẠO TÓM TẮT ===
                    // 1. Lột bỏ toàn bộ thẻ HTML để lấy văn bản thuần túy.
                    $plain_text = strip_tags($post['content']);
                    // 2. Cắt 300 ký tự từ văn bản thuần túy đó.
                    $excerpt = substr($plain_text, 0, 300);
                    // 3. Hiển thị tóm tắt đã được làm sạch.
                    echo nl2br(htmlspecialchars($excerpt));
                    ?>...
                </div>

                <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm &rarr;</a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy bài viết nào.</p>
    <?php endif; ?>
</section>

<nav class="pagination">
    <?php if ($total_pages > 1): ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="index.php?page=<?php echo $i; ?>" class="<?php if ($i == $current_page)
                   echo 'active'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    <?php endif; ?>
</nav>

<?php
require 'includes/footer.php';
?>
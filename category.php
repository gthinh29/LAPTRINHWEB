<?php
require 'includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit;
}
$category_id = $_GET['id'];

$sql_cat_name = "SELECT name FROM categories WHERE id = ?";
$stmt_cat_name = mysqli_prepare($conn, $sql_cat_name);
mysqli_stmt_bind_param($stmt_cat_name, "i", $category_id);
mysqli_stmt_execute($stmt_cat_name);
$result_cat_name = mysqli_stmt_get_result($stmt_cat_name);
$category_info = mysqli_fetch_assoc($result_cat_name);

if (!$category_info) {
    header("location: index.php");
    exit;
}
$page_title = "Các bài viết trong danh mục '" . htmlspecialchars($category_info['name']) . "'";

require 'includes/header.php';

$sql_posts = "SELECT p.id, p.title, p.author, p.content, p.image, p.created_at, c.id as category_id, c.name as category_name 
              FROM posts p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.category_id = ?
              ORDER BY p.created_at DESC";
$stmt_posts = mysqli_prepare($conn, $sql_posts);
mysqli_stmt_bind_param($stmt_posts, "i", $category_id);
mysqli_stmt_execute($stmt_posts);
$result_posts = mysqli_stmt_get_result($stmt_posts);
$posts = [];
while ($row = mysqli_fetch_assoc($result_posts)) {
    $posts[] = $row;
}
?>

<section class="post-list">
    <h2><?php echo $page_title; ?></h2>

    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <article class="post-summary">
                <div class="summary-grid">
                    <?php if ($post['image']): ?>
                        <div class="summary-thumbnail">
                            <a href="post.php?id=<?php echo $post['id']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
                                    alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="summary-content">
                        <h2><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                        </h2>
                        <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
                            <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                        </p>
                        <div class="post-excerpt">
                            <?php

                            $content_for_excerpt = html_entity_decode(str_replace('&nbsp;', ' ', $post['content']));

                            $content_with_newlines = preg_replace('/<br\s?\/?>/i', "\n", $content_for_excerpt);
                            $content_with_newlines = preg_replace('/(<\/p>|<\/div>)/i', "\n", $content_with_newlines);

                            $plain_text = strip_tags($content_with_newlines);

                            $url_pattern = '/https?:\/\/[^\s<]+/';
                            $text_without_links = preg_replace($url_pattern, '', $plain_text);

                            $excerpt = mb_substr($text_without_links, 0, 150, 'UTF-8');
                            echo htmlspecialchars($excerpt);
                            ?>...
                        </div>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm →</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không có bài viết nào trong danh mục này.</p>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
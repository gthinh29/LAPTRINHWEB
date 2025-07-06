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


$sql = "SELECT p.id, p.title, p.author, p.content, p.image, p.created_at, c.id as category_id, c.name as category_name 
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC 
        LIMIT ? OFFSET ?";

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
                        <p class="post-meta">
                            Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
                            <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                            <?php if (!empty($post['category_name'])): ?>
                                trong <a href="category.php?id=<?php echo $post['category_id']; ?>"
                                    class="post-category"><?php echo htmlspecialchars($post['category_name']); ?></a>
                            <?php endif; ?>
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
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm &rarr;</a>
                    </div>
                </div>
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
                   echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    <?php endif; ?>
</nav>

<?php require 'includes/footer.php'; ?>
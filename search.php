<?php
require 'includes/header.php';

$search_query = '';
$posts = [];
$search_error = '';

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    $search_term = "%" . $search_query . "%";

    $sql = "SELECT p.id, p.title, p.author, p.content, p.image, p.created_at, c.id as category_id, c.name as category_name 
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.title LIKE ? OR p.content LIKE ? 
            ORDER BY p.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
} else {
    $search_error = "Vui lòng nhập từ khóa để tìm kiếm.";
}

$page_title = "Kết quả tìm kiếm cho '" . htmlspecialchars($search_query) . "'";
?>

<section class="search-results">
    <h2><?php echo $page_title; ?></h2>

    <?php if (!empty($search_error)): ?>
        <p><?php echo $search_error; ?></p>
    <?php elseif (count($posts) > 0): ?>
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
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm →</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy bài viết nào phù hợp với từ khóa
            "<strong><?php echo htmlspecialchars($search_query); ?></strong>".</p>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
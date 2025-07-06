<?php
// file: search.php

require 'includes/header.php';

$search_query = '';
$posts = [];
$search_error = '';

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);

    // Chuẩn bị từ khóa để dùng trong câu lệnh LIKE
    $search_term = "%" . $search_query . "%";

    // Truy vấn các bài viết có tiêu đề hoặc nội dung chứa từ khóa
    $sql = "SELECT id, title, author, content, image, created_at 
            FROM posts 
            WHERE title LIKE ? OR content LIKE ? 
            ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
} else {
    // Nếu người dùng truy cập search.php mà không có từ khóa
    $search_error = "Vui lòng nhập từ khóa để tìm kiếm.";
}

// Đặt tiêu đề cho trang kết quả tìm kiếm
$page_title = "Kết quả tìm kiếm cho '" . htmlspecialchars($search_query) . "'";
?>

<section class="search-results">
    <h2><?php echo $page_title; ?></h2>

    <?php if (!empty($search_error)): ?>
        <p><?php echo $search_error; ?></p>
    <?php elseif (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <article class="post-summary">
                <h3><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></p>

                <div class="post-excerpt">
                    <?php
                    $plain_text = strip_tags($post['content']);
                    $excerpt = mb_substr($plain_text, 0, 300, 'UTF-8');
                    echo nl2br(htmlspecialchars($excerpt));
                    ?>...
                </div>

                <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Đọc thêm &rarr;</a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy bài viết nào phù hợp với từ khóa
            "<strong><?php echo htmlspecialchars($search_query); ?></strong>".</p>
    <?php endif; ?>
</section>

<?php
require 'includes/footer.php';
?>
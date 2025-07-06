<?php
// file: post.php (Đã cập nhật để hiển thị danh mục)
require 'includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit;
}
$post_id = $_GET['id'];

// === NÂNG CẤP SQL: Dùng LEFT JOIN để lấy cả tên danh mục ===
$sql_post = "SELECT p.title, p.content, p.author, p.image, p.created_at, c.id as category_id, c.name as category_name 
             FROM posts p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = ?";
$stmt_post = mysqli_prepare($conn, $sql_post);
mysqli_stmt_bind_param($stmt_post, "i", $post_id);
mysqli_stmt_execute($stmt_post);
$result_post = mysqli_stmt_get_result($stmt_post);
$post = mysqli_fetch_assoc($result_post);

if (!$post) {
    header("location: index.php");
    exit;
}

function process_post_content($html)
{
    if (empty(trim($html))) {
        return '';
    }

    $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
    $html = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $html);
    $html = str_replace("\xc2\xa0", ' ', $html);

    $doc = new DOMDocument();
    @$doc->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);

    $allowed_tags = ['b', 'i', 'u', 'br', 'span', 'a', 'ul', 'ol', 'li', 'div', 'p'];
    $allowed_styles = [
        'font-size' => '/^(\d+|[\d\.]+)px$/i',
        'color' => '/^(#([0-9a-f]{3}){1,2}|rgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\))$/i',
        'text-align' => '/^(left|center|right|justify)$/i',
    ];

    $elements = $doc->getElementsByTagName('*');
    for ($i = $elements->length - 1; $i > -1; $i--) {
        $element = $elements->item($i);
        if (!$element || !isset($element->nodeName) || !in_array($element->nodeName, $allowed_tags)) {
            continue;
        }

        if ($element->hasAttributes()) {
            foreach (iterator_to_array($element->attributes) as $attr) {
                $attrName = strtolower($attr->name);
                if ($attrName === 'style') {
                    $styles = explode(';', $element->getAttribute('style'));
                    $safe_styles = [];
                    foreach ($styles as $style) {
                        if (trim($style) === '')
                            continue;
                        $parts = explode(':', $style, 2);
                        if (count($parts) < 2)
                            continue;
                        list($property, $value) = $parts;
                        $property = trim(strtolower($property));
                        $value = trim($value);
                        if (isset($allowed_styles[$property]) && preg_match($allowed_styles[$property], $value)) {
                            $safe_styles[] = $property . ': ' . $value;
                        }
                    }
                    if (!empty($safe_styles)) {
                        $element->setAttribute('style', implode('; ', $safe_styles));
                    } else {
                        $element->removeAttribute('style');
                    }
                } elseif ($element->nodeName === 'a' && $attrName === 'href') {
                    continue;
                } else {
                    $element->removeAttribute($attrName);
                }
            }
        }
    }

    $body = $doc->getElementsByTagName('body')->item(0);
    $output = '';
    if ($body && $body->hasChildNodes()) {
        foreach ($body->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }
    }
    return $output;
}

$page_title = $post['title'];
require 'includes/header.php';
?>

<article class="post-full">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p class="post-meta">
        Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
        <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>

        <?php if (!empty($post['category_name'])): ?>
            trong <a href="category.php?id=<?php echo $post['category_id']; ?>"
                class="post-category"><?php echo htmlspecialchars($post['category_name']); ?></a>
        <?php endif; ?>
    </p>
    <?php if ($post['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
            alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image-full">
    <?php endif; ?>

    <div class="post-content">
        <?php
        $clean_html = process_post_content($post['content']);

        $youtube_pattern = '/https?:\/\/(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|shorts\/|watch\?.+&v=))([\w-]{11})[^\s<]*/';

        $final_content = preg_replace_callback(
            $youtube_pattern,
            function ($matches) {
                $video_id = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
                $iframe_src = "https://www.youtube.com/embed/" . $video_id;

                return '<div class="video-container">' .
                    '<iframe src="' . $iframe_src . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' .
                    '</div>';
            },
            $clean_html
        );

        echo $final_content;
        ?>
    </div>
</article>

<a href="index.php" class="back-link">← Quay lại trang chủ</a>

<?php require 'includes/footer.php'; ?>
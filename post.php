<?php
// file: post.php (Phiên bản cuối cùng - Sử dụng phương pháp load HTML chuẩn)

require 'includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit;
}
$post_id = $_GET['id'];

$sql_post = "SELECT title, content, author, image, created_at FROM posts WHERE id = ?";
$stmt_post = mysqli_prepare($conn, $sql_post);
mysqli_stmt_bind_param($stmt_post, "i", $post_id);
mysqli_stmt_execute($stmt_post);
$result_post = mysqli_stmt_get_result($stmt_post);
$post = mysqli_fetch_assoc($result_post);

if (!$post) {
    header("location: index.php");
    exit;
}

/**
 * Hàm làm sạch và xử lý nội dung bài viết một cách triệt để.
 * @param string $html Nội dung HTML thô từ cơ sở dữ liệu.
 * @return string Nội dung HTML đã được làm sạch và xử lý.
 */
function process_post_content($html)
{
    if (empty(trim($html))) {
        return '';
    }

    // Bước 1: Làm sạch sâu dữ liệu đầu vào
    $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
    $html = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $html);
    $html = str_replace("\xc2\xa0", ' ', $html);

    // Bước 2: Phân tích HTML bằng DOMDocument
    $doc = new DOMDocument();

    // === PHẦN NÂNG CẤP QUAN TRỌNG NHẤT ===
    // Sử dụng thẻ <meta> để khai báo encoding, đây là cách chuẩn và ổn định nhất
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
            continue; // Bỏ qua các thẻ không được phép như <html>, <body>, <meta>
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

    // Bước 3: Xuất ra nội dung sạch từ bên trong thẻ <body>
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
    <p class="post-meta">Đăng bởi <strong><?php echo htmlspecialchars($post['author']); ?></strong> vào lúc
        <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
    </p>
    <?php if ($post['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
            alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image-full">
    <?php endif; ?>

    <div class="post-content">
        <?php
        // Quy trình xử lý 3 bước an toàn
        // 1. "Rửa" và chuẩn hóa HTML
        $clean_html = process_post_content($post['content']);

        // 2. Tìm và nhúng video trên chuỗi HTML đã sạch
        $youtube_pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})(?:\S+)?/';
        $youtube_replacement = '<div class="video-container"><iframe src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div>';
        $final_content = preg_replace($youtube_pattern, $youtube_replacement, $clean_html);

        // 3. Hiển thị nội dung cuối cùng
        echo $final_content;
        ?>
    </div>
</article>

<a href="index.php" class="back-link">← Quay lại trang chủ</a>

<?php
require 'includes/footer.php';
?>
<?php
// file: admin/add_post.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

$title = $content = $author = '';
$error = '';
// Gán giá trị mặc định cho tác giả là username đang đăng nhập
$author = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']); // Dữ liệu bây giờ đã có HTML từ editor
    $author_post = trim($_POST['author']);
    $image_name = '';

    if (empty($title) || empty($content) || empty($author_post)) {
        $error = "Tiêu đề, nội dung và tác giả không được để trống.";
    } else {
        // Xử lý upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0 && !empty($_FILES['image']['name'])) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        $sql = "INSERT INTO posts (author, title, content, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $author_post, $title, $content, $image_name);

        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_posts.php?status=added");
            exit;
        } else {
            $error = "Đã có lỗi xảy ra. Vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm bài viết mới</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header>
        <h1>Thêm bài viết mới</h1>
    </header>
    <main class="container">
        <a href="manage_posts.php">← Quay lại danh sách</a>
        <form class="admin-form" action="add_post.php" method="post" enctype="multipart/form-data">
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Tên tác giả</label>
                <input type="text" name="author" id="author" value="<?php echo $author; ?>" required>
            </div>

            <div class="form-group">
                <label for="content-editor">Nội dung</label>
                <div class="editor-toolbar">
                    <button type="button" title="Hoàn tác" onclick="formatDoc('undo');">↩</button>
                    <button type="button" title="Làm lại" onclick="formatDoc('redo');">↪</button>
                    <button type="button" onclick="formatDoc('bold');"><b>B</b></button>
                    <button type="button" onclick="formatDoc('italic');"><i>I</i></button>
                    <button type="button" onclick="formatDoc('underline');"><u>U</u></button>
                    <button type="button" title="Chèn liên kết" onclick="insertLink();">🔗</button>
                    <button type="button" title="Danh sách gạch đầu dòng"
                        onclick="formatDoc('insertUnorderedList');">●</button>
                    <button type="button" title="Danh sách có thứ tự"
                        onclick="formatDoc('insertOrderedList');">1.</button>
                    <input type="color" id="fontColorPicker" title="Màu chữ">

                    <select id="fontSizeSelector" title="Cỡ chữ">
                        <option value="14">Nhỏ</option>
                        <option value="16" selected>Bình thường</option>
                        <option value="20">Hơi lớn</option>
                        <option value="30">Lớn</option>
                        <option value="44">Rất lớn</option>
                    </select>

                    <button type="button" title="Căn trái" onclick="formatDoc('justifyLeft');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,4H21V6H3V4M3,9H15V11H3V9M3,14H21V16H3V14M3,19H15V21H3V19Z" />
                        </svg>
                    </button>
                    <button type="button" title="Căn giữa" onclick="formatDoc('justifyCenter');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,4H21V6H3V4M7,9H17V11H7V9M3,14H21V16H3V14M7,19H17V21H7V19Z" />
                        </svg>
                    </button>
                    <button type="button" title="Căn phải" onclick="formatDoc('justifyRight');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3,4H21V6H3V4M9,9H21V11H9V9M3,14H21V16H3V14M9,19H21V21H9V19Z" />
                        </svg>
                    </button>
                </div>
                <div id="content-editor" contenteditable="true" class="wysiwyg-editor"></div>
                <textarea name="content" id="content" style="display:none;"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Ảnh đại diện (tùy chọn)</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            <div class="form-group">
                <input type="submit" value="Đăng bài">
            </div>
        </form>
    </main>
    <script src="../admin_editor.js"></script>
</body>

</html>
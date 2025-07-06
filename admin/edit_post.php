<?php
// file: admin/edit_post.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: manage_posts.php");
    exit;
}

$post_id = $_GET['id'];
$error = '';

// Lấy thông tin bài viết hiện tại để điền vào form
$sql_get = "SELECT title, content, author, image FROM posts WHERE id = ?";
$stmt_get = mysqli_prepare($conn, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $post_id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$post = mysqli_fetch_assoc($result_get);

if (!$post) {
    header("location: manage_posts.php");
    exit;
}

// Xử lý khi admin gửi form cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $current_image = $post['image']; // Giữ lại ảnh cũ phòng trường hợp không upload ảnh mới

    if (empty($title) || empty($content) || empty($author)) {
        $error = "Tiêu đề, nội dung và tác giả không được để trống.";
    } else {
        $image_name = $current_image; // Mặc định là ảnh cũ

        // Kiểm tra nếu có file mới được upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0 && !empty($_FILES['image']['name'])) {
            $target_dir = "../uploads/";

            // (Tùy chọn) Xóa ảnh cũ nếu tồn tại
            if (!empty($current_image) && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }

            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        $sql_update = "UPDATE posts SET title = ?, content = ?, author = ?, image = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ssssi", $title, $content, $author, $image_name, $post_id);

        if (mysqli_stmt_execute($stmt_update)) {
            header("location: manage_posts.php?status=updated");
            exit;
        } else {
            $error = "Cập nhật thất bại, vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa bài viết</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header>
        <h1>Sửa bài viết</h1>
    </header>
    <main class="container">
        <a href="manage_posts.php">← Quay lại danh sách</a>
        <form class="admin-form" action="edit_post.php?id=<?php echo $post_id; ?>" method="post"
            enctype="multipart/form-data">
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p><?php endif; ?>
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="author">Tên tác giả</label>
                <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($post['author']); ?>"
                    required>
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
                <div id="content-editor" contenteditable="true" class="wysiwyg-editor">
                    <?php echo $post['content']; // Hiển thị nội dung HTML trực tiếp ?>
                </div>
                <textarea name="content" id="content" style="display:none;"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Thay ảnh đại diện (để trống nếu không muốn thay đổi)</label>
                <input type="file" name="image" id="image" accept="image/*">
                <?php if ($post['image']): ?>
                    <p>Ảnh hiện tại: <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>"
                            alt="Ảnh hiện tại" width="100"></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <input type="submit" value="Cập nhật bài viết">
            </div>
        </form>
    </main>
    <script src="../admin_editor.js"></script>
</body>

</html>
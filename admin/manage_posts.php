<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../dangnhap.php");
    exit;
}

require '../includes/database.php';

$posts_per_page = 10;
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

$posts = [];
$sql = "SELECT id, title, author, created_at FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $posts_per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header>
        <h1><a href="../index.php">Blog Công Nghệ</a></h1>
        <nav>
            <span class="welcome-message">Chào,
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
            <a href="categories/manage_categories.php">Quản lý Danh mục</a>
            <a href="../logout.php">Đăng xuất</a>
        </nav>
    </header>

    <main class="container">
        <div class="admin-toolbar">
            <a href="add_post.php" class="button-add-new btn-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                </svg>
                <span>Thêm bài viết mới</span>
            </a>
            <button type="button" id="edit-post-btn" class="edit-btn btn-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M20.71,7.04C21.1,6.65 21.1,6.02 20.71,5.63L18.37,3.29C17.98,2.9 17.35,2.9 16.96,3.29L15.13,5.12L18.88,8.87M3,17.25V21H6.75L17.81,9.94L14.06,6.19L3,17.25Z" />
                </svg>
                <span>Sửa Bài Viết</span>
            </button>
            <button type="button" id="delete-selected-btn" class="delete-btn btn-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M9,3V4H4V6H5V19C5,20.1 5.9,21 7,21H17C18.1,21 19,20.1 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" />
                </svg>
                <span>Xoá Mục Đã Chọn</span>
            </button>
        </div>

        <form id="bulk-action-form" action="bulk_delete_posts.php" method="post">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;"><input type="checkbox" id="select-all-checkbox" />
                        </th>
                        <th>Tiêu đề</th>
                        <th style="width: 20%;">Tác giả</th>
                        <th style="width: 15%;">Ngày đăng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td style="text-align: center;"><input type="checkbox" name="selected_posts[]"
                                        class="post-checkbox" value="<?php echo $post['id']; ?>"></td>
                                <td><span class="post-title"><?php echo htmlspecialchars($post['title']); ?></span></td>
                                <td><span class="post-author"><?php echo htmlspecialchars($post['author']); ?></span></td>
                                <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-posts-message">Chưa có bài viết nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>

        <nav class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="manage_posts.php?page=<?php echo $i; ?>" class="<?php if ($i == $current_page)
                           echo 'active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            <?php endif; ?>
        </nav>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editBtn = document.getElementById('edit-post-btn');
            const deleteBtn = document.getElementById('delete-selected-btn');
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const postCheckboxes = document.querySelectorAll('.post-checkbox');
            const bulkActionForm = document.getElementById('bulk-action-form');

            selectAllCheckbox.addEventListener('change', function () {
                postCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            editBtn.addEventListener('click', function () {
                const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Vui lòng chọn một bài viết để sửa.');
                } else if (selectedCheckboxes.length > 1) {
                    alert('Chỉ có thể sửa một bài viết mỗi lần. Vui lòng chọn lại.');
                } else {
                    const postId = selectedCheckboxes[0].value;
                    window.location.href = `edit_post.php?id=${postId}`;
                }
            });

            deleteBtn.addEventListener('click', function () {
                const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Vui lòng chọn ít nhất một bài viết để xóa.');
                } else {
                    if (confirm(`Bạn có chắc chắn muốn xóa ${selectedCheckboxes.length} bài viết đã chọn không?`)) {
                        bulkActionForm.submit();
                    }
                }
            });
        });
    </script>
</body>

</html>
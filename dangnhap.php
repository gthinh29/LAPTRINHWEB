<?php
// file: dangnhap.php (đã bỏ hoàn toàn role)
session_start();

// Nếu đã đăng nhập, chuyển thẳng vào trang admin để không phải đăng nhập lại
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: admin/manage_posts.php");
    exit;
}

require 'includes/database.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    } else {
        // Câu lệnh SQL không cần lấy cột 'role'
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Chỉ cần xác thực mật khẩu
        if ($user && password_verify($password, $user['password'])) {
            // Đặt session đăng nhập
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];

            // Luôn chuyển hướng đến trang quản trị
            header("location: admin/manage_posts.php");
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page-body">
    <div class="login-container">
        <h1>Đăng nhập Admin</h1>
        <form action="dangnhap.php" method="post" autocomplete="off">
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" name="username" id="username" required placeholder=" " autocomplete="off">
                <label for="username">Tên đăng nhập</label>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" required placeholder=" " autocomplete="off">
                <label for="password">Mật khẩu</label>
            </div>
            <input type="submit" value="Đăng nhập">
        </form>
    </div>
</body>
</html>
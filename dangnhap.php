<?php
// file: dangnhap.php

session_start();
require 'database.php'; // Sử dụng file kết nối của bạn

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
        $error = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    } else {
        // Sửa lại theo cú pháp mysqli
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("location: admin/dashboard.php");
            } else {
                header("location: index.php");
            }
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
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Đăng nhập</h1></header>
    <main class="container">
        <form action="dangnhap.php" method="post">
            <?php if($error): ?>
                <p class="error" style="color: red; padding: 10px; border: 1px solid red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Đăng nhập">
            </div>
            <p>Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a>.</p>
        </form>
    </main>
</body>
</html>
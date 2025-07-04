<?php
session_start();
require 'database.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_fetch_assoc($result_check)) {
            $error = 'Tên đăng nhập đã tồn tại.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "ss", $username, $hashed_password);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                header("location: dangnhap.php");
                exit;
            } else {
                $error = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Đăng ký tài khoản</h1></header>
    <main class="container">
        <form action="dangky.php" method="post">
            <?php if($error): ?>
                <p class="error"><?php echo $error; ?></p>
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
                <input type="submit" value="Đăng ký">
            </div>
            <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập ngay</a>.</p>
        </form>
    </main>
</body>
</html>
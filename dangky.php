<?php
session_start();

// Bao gồm file kết nối CSDL (đã được cấu hình cho TiDB Cloud)
require_once 'database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Kiểm tra các trường không được để trống
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = '<p style="color: red; text-align: center;">Vui lòng điền đầy đủ tất cả các trường.</p>';
    }
    // 2. Kiểm tra mật khẩu và xác nhận mật khẩu có khớp nhau không
    else if ($password !== $confirm_password) {
        $message = '<p style="color: red; text-align: center;">Mật khẩu và xác nhận mật khẩu không khớp.</p>';
    }
    // 3. Kiểm tra độ dài mật khẩu
    else if (strlen($password) < 6) {
        $message = '<p style="color: red; text-align: center;">Mật khẩu phải có ít nhất 6 ký tự.</p>';
    }
    else {
        // 4. Kiểm tra xem tên người dùng đã tồn tại chưa
        $sql_check_user = "SELECT id FROM posts WHERE username = ?";
        $stmt_check_user = mysqli_prepare($conn, $sql_check_user);
        mysqli_stmt_bind_param($stmt_check_user, "s", $username);
        mysqli_stmt_execute($stmt_check_user);
        $result_check_user = mysqli_stmt_get_result($stmt_check_user);

        if (mysqli_num_rows($result_check_user) > 0) {
            $message = '<p style="color: red; text-align: center;">Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.</p>';
        } else {
            // 5. Hash mật khẩu để bảo mật
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 6. Chèn người dùng mới vào CSDL
            $sql_insert_user = "INSERT INTO posts (username, password) VALUES (?, ?)";
            $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
            mysqli_stmt_bind_param($stmt_insert_user, "ss", $username, $hashed_password);

            if (mysqli_stmt_execute($stmt_insert_user)) {
                $message = '<p style="color: green; text-align: center;">Đăng ký thành công! Bạn có thể <a href="dangnhap.php">đăng nhập</a> ngay bây giờ.</p>';
            } else {
                $message = '<p style="color: red; text-align: center;">Đăng ký không thành công! Lỗi: ' . mysqli_error($conn) . '</p>';
            }
            mysqli_stmt_close($stmt_insert_user);
        }
        mysqli_stmt_close($stmt_check_user);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .register-container { background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); width: 380px; max-width: 90%; box-sizing: border-box; }
        h2 { text-align: center; color: #007bff; margin-bottom: 30px; font-size: 2em; letter-spacing: 0.5px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; font-size: 1.05em; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 1em; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); outline: none; }
        .button-group { display: flex; justify-content: space-between; gap: 15px; margin-top: 25px; }
        button, .back-to-login { flex: 1; padding: 12px 20px; border: none; border-radius: 6px; font-size: 1.1em; cursor: pointer; transition: all 0.3s ease; text-align: center; text-decoration: none; display: inline-block; }
        button[type="submit"] { background-color: #28a745; color: white; box-shadow: 0 2px 5px rgba(40, 167, 69, 0.2); }
        button[type="submit"]:hover { background-color: #218838; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3); }
        .back-to-login { background-color: #6c757d; color: white; box-shadow: 0 2px 5px rgba(108, 117, 125, 0.2); }
        .back-to-login:hover { background-color: #5a6268; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3); }
        .message a { color: #007bff; text-decoration: none; }
        .message a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký Tài Khoản</h2>
        <?php echo $message; ?>
        <form action="dangky.php" method="POST" novalidate>
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="button-group">
                <button type="submit">Đăng ký</button>
                <a href="dangnhap.php" class="back-to-login">Quay lại</a>
            </div>
        </form>
    </div>
</body>
</html>
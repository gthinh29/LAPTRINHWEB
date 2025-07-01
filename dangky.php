<?php
session_start(); // Bắt đầu phiên làm việc (có thể không cần thiết cho trang đăng ký, nhưng giữ cho nhất quán)

// Bao gồm file database.php để thiết lập kết nối đến CSDL
require_once 'database.php';

$message = ''; // Biến để lưu trữ thông báo lỗi hoặc thành công

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? ''); // Lấy tên người dùng và loại bỏ khoảng trắng
    $password = $_POST['password'] ?? ''; // Lấy mật khẩu
    $confirm_password = $_POST['confirm_password'] ?? ''; // Lấy xác nhận mật khẩu

    // 1. Kiểm tra các trường không được để trống
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = '<p style="color: red; text-align: center;">Vui lòng điền đầy đủ tất cả các trường.</p>';
    }
    // 2. Kiểm tra mật khẩu và xác nhận mật khẩu có khớp nhau không
    else if ($password !== $confirm_password) {
        $message = '<p style="color: red; text-align: center;">Mật khẩu và xác nhận mật khẩu không khớp.</p>';
    }
    // 3. Kiểm tra độ dài mật khẩu (ví dụ: tối thiểu 6 ký tự)
    else if (strlen($password) < 6) {
        $message = '<p style="color: red; text-align: center;">Mật khẩu phải có ít nhất 6 ký tự.</p>';
    }
    else {
        // 4. Kiểm tra xem tên người dùng đã tồn tại trong CSDL chưa
        $sql_check_user = "SELECT id FROM posts WHERE username = ?";
        $params_check_user = array($username);
        $stmt_check_user = sqlsrv_query($conn, $sql_check_user, $params_check_user);

        if ($stmt_check_user === false) {
            $message = '<p style="color: red; text-align: center;">Lỗi truy vấn CSDL khi kiểm tra người dùng: ' . print_r(sqlsrv_errors(), true) . '</p>';
        } else {
            if (sqlsrv_has_rows($stmt_check_user)) {
                $message = '<p style="color: red; text-align: center;">Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.</p>';
            } else {
                // 5. Hash mật khẩu trước khi lưu vào CSDL (QUAN TRỌNG VỀ BẢO MẬT)
                // KHÔNG BAO GIỜ LƯU MẬT KHẨU DƯỚI DẠNG VĂN BẢN THÔ!
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 6. Chuẩn bị câu truy vấn SQL để thêm người dùng mới
                $sql_insert_user = "INSERT INTO posts (username, password) VALUES (?, ?)";
                $params_insert_user = array($username, $hashed_password);

                $stmt_insert_user = sqlsrv_query($conn, $sql_insert_user, $params_insert_user);

                if ($stmt_insert_user === false) {
                    $message = '<p style="color: red; text-align: center;">Đăng ký không thành công! Lỗi: ' . print_r(sqlsrv_errors(), true) . '</p>';
                } else {
                    $message = '<p style="color: green; text-align: center;">Đăng ký thành công! Bạn có thể <a href="dangnhap.php">đăng nhập</a> ngay bây giờ.</p>';
                    // Tùy chọn: Chuyển hướng ngay lập tức sau khi đăng ký thành công
                    // header('Location: dangnhap.php?registered=true');
                    // exit;
                }
                sqlsrv_free_stmt($stmt_check_user);
                if (isset($stmt_insert_user)) {
                    sqlsrv_free_stmt($stmt_insert_user);
                }
            }
        }
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
        /* Thiết lập CSS cơ bản cho body */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Container cho form đăng ký */
        .register-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 380px; /* Rộng hơn một chút so với form đăng nhập */
            max-width: 90%;
            box-sizing: border-box;
        }

        /* Tiêu đề form */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            letter-spacing: 0.5px;
            color: #007bff; /* Màu xanh dương cho tiêu đề đăng ký */
        }

        /* Nhóm form cho mỗi input */
        .form-group {
            margin-bottom: 20px;
        }

        /* Nhãn của input */
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
            font-size: 1.05em;
        }

        /* Input text và password */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        /* Nhóm nút */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }

        /* Nút chung */
        button, .back-to-login {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            text-align: center;
            text-decoration: none; /* Cho link */
            display: inline-block; /* Cho link */
        }

        /* Nút Đăng ký */
        button[type="submit"] {
            background-color: #28a745; /* Màu xanh lá cây */
            color: white;
            box-shadow: 0 2px 5px rgba(40, 167, 69, 0.2);
        }

        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        /* Nút Quay lại Đăng nhập */
        .back-to-login {
            background-color: #6c757d; /* Màu xám */
            color: white;
            box-shadow: 0 2px 5px rgba(108, 117, 125, 0.2);
        }

        .back-to-login:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }

        /* Thông báo lỗi/thành công */
        .message {
            margin-top: 20px;
            font-size: 1em;
            font-weight: bold;
        }
        .message a {
            color: #007bff;
            text-decoration: none;
        }
        .message a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký Tài Khoản</h2>
        <?php echo $message; // Hiển thị thông báo lỗi/thành công ?>
        <form action="dangky.php" method="POST">
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
                <a href="dangnhap.php" class="back-to-login">Quay lại Đăng nhập</a> 
            </div>
        </form>
    </div>
</body>
</html>

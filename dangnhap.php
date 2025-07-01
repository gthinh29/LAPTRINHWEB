<?php
session_start(); // Bắt đầu phiên làm việc để quản lý trạng thái đăng nhập

// Bao gồm file database.php để thiết lập kết nối đến CSDL
require_once 'database.php';

$message = ''; // Biến để lưu trữ thông báo lỗi hoặc thành công

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? ''; // Lấy tên người dùng từ form
    $password = $_POST['password'] ?? ''; // Lấy mật khẩu từ form
    $remember = isset($_POST['remember']); // Kiểm tra checkbox "nhớ mật khẩu"

    // Chuẩn bị câu truy vấn SQL để lấy thông tin người dùng từ bảng 'users'
    // Lưu ý: Trong ứng dụng thực tế, mật khẩu LƯU TRỮ trong CSDL phải được HASH
    // và bạn sẽ so sánh mật khẩu nhập vào (cũng đã hash) với mật khẩu đã hash trong CSDL.
    $sql = "SELECT id, username, password FROM posts WHERE username = ?";
    $params = array($username);

    // Thực thi truy vấn
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Xử lý lỗi truy vấn CSDL
        $message = '<p style="color: red; text-align: center;">Lỗi truy vấn CSDL: ' . print_r(sqlsrv_errors(), true) . '</p>';
    } else {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($user) {
            // Người dùng tồn tại, bây giờ kiểm tra mật khẩu
            // Ví dụ đơn giản này so sánh mật khẩu thô.
            // TRONG THỰC TẾ, HÃY DÙNG `password_verify()` SAU KHI LƯU MẬT KHẨU ĐƯỢC HASH.
            // Ví dụ: if (password_verify($password, $user['password'])) { ... }
            if (password_verify($password, $user['password'])) { // So sánh mật khẩu thô (KHÔNG KHUYẾN CÁO TRONG THỰC TẾ)
                // Đăng nhập thành công
                $_SESSION['loggedin'] = true; // Đặt biến session để đánh dấu người dùng đã đăng nhập
                $_SESSION['username'] = $username; // Lưu tên người dùng vào session

                if ($remember) {
                    // Đặt cookie nếu người dùng chọn "nhớ mật khẩu"
                    // Lưu ý: Lưu mật khẩu thô trong cookie là KHÔNG AN TOÀN!
                    // Trong ứng dụng thực tế, bạn nên sử dụng token hoặc cơ chế remember me an toàn hơn.
                    setcookie('username', $username, time() + (86400 * 30), "/"); // Cookie tồn tại 30 ngày
                    setcookie('password', $password, time() + (86400 * 30), "/");
                } else {
                    // Xóa cookie nếu không chọn "nhớ mật khẩu"
                    setcookie('username', "", time() - 3600, "/");
                    setcookie('password', "", time() - 3600, "/");
                }

                // Chuyển hướng người dùng sang index.php
                header('Location: index.php');
                exit; // Dừng việc thực thi script sau khi chuyển hướng
            } else {
                // Mật khẩu không đúng
                $message = '<p style="color: red; text-align: center;">Đăng nhập không thành công! (Sai mật khẩu)</p>';
            }
        } else {
            // Người dùng không tồn tại
            $message = '<p style="color: red; text-align: center;">Đăng nhập không thành công! (Người dùng không tồn tại)</p>';
        }
        sqlsrv_free_stmt($stmt); // Giải phóng tài nguyên statement
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống</title>
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

        /* Container cho form đăng nhập */
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 350px;
            max-width: 90%; /* Đảm bảo responsive trên các màn hình nhỏ */
            box-sizing: border-box; /* Bao gồm padding và border vào width */
        }

        /* Tiêu đề form */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8em;
            letter-spacing: 0.5px;
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

        /* Nhóm checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2); /* Phóng to checkbox một chút */
        }

        .checkbox-group label {
            margin-bottom: 0; /* Xóa margin-bottom mặc định */
            font-weight: normal;
        }

        /* Nhóm nút */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 15px; /* Khoảng cách giữa các nút */
        }

        /* Nút chung */
        button {
            flex: 1; /* Các nút sẽ chiếm không gian bằng nhau */
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* Nút Đăng nhập */
        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-2px); /* Hiệu ứng nhấn nút */
        }

        /* Nút Hủy */
        button[type="reset"] {
            background-color: #6c757d;
            color: white;
        }

        button[type="reset"]:hover {
            background-color: #5a6268;
            transform: translateY(-2px); /* Hiệu ứng nhấn nút */
        }

        /* Thông báo lỗi */
        .message {
            margin-top: 20px;
            font-size: 1em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng Nhập</h2>
        <?php echo $message; // Hiển thị thông báo lỗi/thành công ?>
        <form action="dangnhap.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required
                       value="<?php echo htmlspecialchars($_COOKIE['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required
                       value="<?php echo htmlspecialchars($_COOKIE['password'] ?? ''); ?>">
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                <label for="remember">Nhớ mật khẩu</label>
            </div>
            <div class="button-group">
                <button type="submit">Đăng nhập</button>
                <button type="button" class="register-button" onclick="window.location.href='dangky.php'">Đăng ký</button>
            </div>
        </form>
    </div>
</body>
</html>

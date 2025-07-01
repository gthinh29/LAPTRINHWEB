<?php
session_start();

require_once 'database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $sql = "SELECT id, username, password FROM posts WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];

        if ($remember) {
            setcookie('username', $user['username'], time() + (86400 * 30), "/"); // 30 ngày
        } else {
            if (isset($_COOKIE['username'])) {
                setcookie('username', '', time() - 3600, "/");
            }
        }

        header('Location: index.php');
        exit;
    } else {
        $message = '<p style="color: red; text-align: center;">Tên đăng nhập hoặc mật khẩu không đúng!</p>';
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-container { background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); width: 350px; max-width: 90%; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; font-size: 1.8em; letter-spacing: 0.5px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; font-size: 1.05em; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 1em; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); outline: none; }
        .checkbox-group { display: flex; align-items: center; margin-bottom: 25px; }
        .checkbox-group input[type="checkbox"] { margin-right: 10px; transform: scale(1.2); }
        .checkbox-group label { margin-bottom: 0; font-weight: normal; }
        .button-group { display: flex; justify-content: space-between; gap: 15px; }
        button { flex: 1; padding: 12px 20px; border: none; border-radius: 6px; font-size: 1.1em; cursor: pointer; transition: all 0.3s ease; }
        button[type="submit"] { background-color: #007bff; color: white; }
        button[type="submit"]:hover { background-color: #0056b3; transform: translateY(-2px); }
        .register-button { background-color: #6c757d; color: white; }
        .register-button:hover { background-color: #5a6268; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng Nhập</h2>
        <?php echo $message; ?>
        <form action="dangnhap.php" method="POST" novalidate>
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_COOKIE['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                <label for="remember">Nhớ tài khoản</label>
            </div>
            <div class="button-group">
                <button type="submit">Đăng nhập</button>
                <button type="button" class="register-button" onclick="window.location.href='dangky.php'">Đăng ký</button>
            </div>
        </form>
    </div>
</body>
</html>
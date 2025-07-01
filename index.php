<?php
session_start();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {

    header('Location: dangnhap.php');
    exit;
}

$username = $_SESSION['username'] ?? 'Khách';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chính</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9f7ef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
            text-align: center;
        }
        .welcome-container {
            background-color: #ffffff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            box-sizing: border-box;
        }
        h1 {
            color: #28a745;
            margin-bottom: 25px;
            font-size: 2.5em;
            letter-spacing: 1px;
        }
        p {
            color: #333;
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 30px;
        }


        .logout-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logout-button:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Chào <?php echo htmlspecialchars($username); ?>!</h1>
        <a href="dangnhap.php" class="logout-button">Thoát</a>
    </div>
</body>
</html>

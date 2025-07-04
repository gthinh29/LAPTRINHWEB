<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');  

// TIDB CLOUD
$servername = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$port       = 4000;            
$username   = "33pVCZBkR1oDiNg.root";
$password   = "QrJfSNIHknK0l5fa";
$dbname     = "blog_db";


// 1. Khởi tạo một đối tượng kết nối mới
$conn = mysqli_init();

// 2. Bật chế độ kết nối an toàn (SSL) và trỏ đến file chứng chỉ
// __DIR__ sẽ lấy đường dẫn của thư mục hiện tại (my-blog)
$cert_path = __DIR__ . '/certs/cacert.pem';
mysqli_ssl_set($conn, NULL, NULL, $cert_path, NULL, NULL);

// 3. Bây giờ mới thực hiện kết nối
// Dòng gây lỗi sẽ hoạt động với cấu hình SSL mới này
mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);


// Kiểm tra kết nối như bình thường
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Thiết lập bảng mã UTF-8
mysqli_set_charset($conn, "utf8mb4");

?>


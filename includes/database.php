<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// TIDB CLOUD
$servername = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$port = 4000;
$username = "33pVCZBkR1oDiNg.root";
$password = "QrJfSNIHknK0l5fa";
$dbname = "blog_db";



$conn = mysqli_init();

$cert_path = __DIR__ . '/certs/cacert.pem';
mysqli_ssl_set($conn, NULL, NULL, $cert_path, NULL, NULL);

mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>
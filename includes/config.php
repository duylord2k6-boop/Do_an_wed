<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ban_sach";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Đường dẫn gốc của website, dùng để load css/js/ảnh cho đúng ở mọi trang (kể cả trong admin/)
define('BASE_URL', '/banSach/');
?>

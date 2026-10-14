<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['id_taikhoan']) || $_SESSION['vai_tro'] != 1) {
    header("Location: " . BASE_URL . "dang_nhap.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang quản trị - Nhà Sách Online</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="index.php">⚙️ Trang quản trị</a>
  <div>
    <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>index.php" target="_blank">Xem website</a>
    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>dang_xuat.php">Đăng xuất</a>
  </div>
</nav>

<div class="container-fluid">
<div class="row">
  <div class="col-md-2 bg-light vh-100 py-3">
    <div class="list-group">
      <a href="index.php" class="list-group-item list-group-item-action">Tổng quan</a>
      <a href="sach.php" class="list-group-item list-group-item-action">Quản lý sách</a>
      <a href="theloai.php" class="list-group-item list-group-item-action">Thể loại</a>
      <a href="tacgia.php" class="list-group-item list-group-item-action">Tác giả</a>
      <a href="nhaxuatban.php" class="list-group-item list-group-item-action">Nhà xuất bản</a>
      <a href="donhang.php" class="list-group-item list-group-item-action">Đơn hàng</a>
    </div>
  </div>
  <div class="col-md-10 py-3">

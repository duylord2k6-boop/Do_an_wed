<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhà Sách Online</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="<?= BASE_URL ?>index.php">📚 Nhà Sách Online</a>
  <div class="d-flex ms-auto align-items-center">
    <form class="d-flex me-3" method="GET" action="<?= BASE_URL ?>index.php">
      <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm sách..."
             value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
      <button class="btn btn-outline-light" type="submit">Tìm</button>
    </form>

    <a class="btn btn-outline-light me-2" href="<?= BASE_URL ?>gio_hang.php">🛒 Giỏ hàng</a>

    <?php if (isset($_SESSION['id_taikhoan'])): ?>
        <span class="text-white me-2">Xin chào, <?= htmlspecialchars($_SESSION['ho_ten']) ?></span>
        <?php if ($_SESSION['vai_tro'] == 1): ?>
            <a class="btn btn-warning btn-sm me-2" href="<?= BASE_URL ?>admin/index.php">Quản trị</a>
        <?php endif; ?>
        <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>dang_xuat.php">Đăng xuất</a>
    <?php else: ?>
        <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>dang_nhap.php">Đăng nhập</a>
        <a class="btn btn-light btn-sm" href="<?= BASE_URL ?>dang_ky.php">Đăng ký</a>
    <?php endif; ?>
  </div>
</nav>

<div class="container my-4">

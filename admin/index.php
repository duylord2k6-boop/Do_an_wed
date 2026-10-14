<?php require_once 'header.php'; ?>

<h3>Tổng quan hệ thống</h3>

<?php
$so_sach = $conn->query("SELECT COUNT(*) c FROM sach")->fetch_assoc()['c'];
$so_donhang = $conn->query("SELECT COUNT(*) c FROM donhang")->fetch_assoc()['c'];
$so_taikhoan = $conn->query("SELECT COUNT(*) c FROM taikhoan WHERE vai_tro = 0")->fetch_assoc()['c'];
$doanh_thu = $conn->query("SELECT SUM(tong_tien) t FROM donhang")->fetch_assoc()['t'] ?? 0;
?>

<div class="row g-3 mt-2">
  <div class="col-md-3">
    <div class="card text-white bg-primary">
      <div class="card-body">
        <h6>Tổng số sách</h6>
        <h3><?= $so_sach ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-success">
      <div class="card-body">
        <h6>Tổng đơn hàng</h6>
        <h3><?= $so_donhang ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-warning">
      <div class="card-body">
        <h6>Khách hàng</h6>
        <h3><?= $so_taikhoan ?></h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-danger">
      <div class="card-body">
        <h6>Doanh thu</h6>
        <h5><?= number_format($doanh_thu) ?> đ</h5>
      </div>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>

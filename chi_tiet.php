<?php
require_once 'includes/config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT s.*, t.ten_tacgia, n.ten_nxb, tl.ten_theloai
                         FROM sach s
                         LEFT JOIN tacgia t ON s.id_tacgia = t.id
                         LEFT JOIN nhaxuatban n ON s.id_nxb = n.id
                         LEFT JOIN theloai tl ON s.id_theloai = tl.id
                         WHERE s.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$sach = $stmt->get_result()->fetch_assoc();

if (!$sach) {
    header("Location: index.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="row">
  <div class="col-md-4">
    <img src="uploads/<?= htmlspecialchars($sach['hinh_anh']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($sach['ten_sach']) ?>">
  </div>
  <div class="col-md-8">
    <h3><?= htmlspecialchars($sach['ten_sach']) ?></h3>
    <p><b>Tác giả:</b> <?= htmlspecialchars($sach['ten_tacgia'] ?? 'Đang cập nhật') ?></p>
    <p><b>Nhà xuất bản:</b> <?= htmlspecialchars($sach['ten_nxb'] ?? 'Đang cập nhật') ?></p>
    <p><b>Thể loại:</b> <?= htmlspecialchars($sach['ten_theloai'] ?? 'Đang cập nhật') ?></p>
    <p><b>Năm xuất bản:</b> <?= htmlspecialchars($sach['nam_xb']) ?></p>
    <p class="gia-sach fs-4"><?= number_format($sach['gia']) ?> đ</p>
    <p><b>Tình trạng:</b>
      <?= $sach['so_luong_ton'] > 0 ? '<span class="text-success">Còn hàng (' . $sach['so_luong_ton'] . ')</span>' : '<span class="text-danger">Hết hàng</span>' ?>
    </p>

    <?php if ($sach['so_luong_ton'] > 0): ?>
        <form method="POST" action="gio_hang.php" class="d-flex align-items-center gap-2">
            <input type="hidden" name="id_sach" value="<?= $sach['id'] ?>">
            <label>Số lượng:</label>
            <input type="number" name="so_luong" value="1" min="1" max="<?= $sach['so_luong_ton'] ?>" class="form-control" style="width:80px">
            <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
        </form>
    <?php else: ?>
        <button class="btn btn-secondary" disabled>Hết hàng</button>
    <?php endif; ?>

    <hr>
    <h5>Mô tả</h5>
    <p><?= nl2br(htmlspecialchars($sach['mo_ta'])) ?></p>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php require_once 'header.php'; ?>

<?php
// Cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_donhang'])) {
    $stmt = $conn->prepare("UPDATE donhang SET trang_thai=? WHERE id=?");
    $stmt->bind_param("si", $_POST['trang_thai'], $_POST['id_donhang']);
    $stmt->execute();
    header("Location: donhang.php");
    exit;
}

// Xem chi tiết 1 đơn
$chi_tiet = null;
$don = null;
if (isset($_GET['xem'])) {
    $stmt = $conn->prepare("SELECT d.*, t.ho_ten, t.email, t.sdt FROM donhang d
                             JOIN taikhoan t ON d.id_taikhoan = t.id WHERE d.id = ?");
    $stmt->bind_param("i", $_GET['xem']);
    $stmt->execute();
    $don = $stmt->get_result()->fetch_assoc();

    $stmt2 = $conn->prepare("SELECT ct.*, s.ten_sach FROM chitiet_donhang ct
                              JOIN sach s ON ct.id_sach = s.id WHERE ct.id_donhang = ?");
    $stmt2->bind_param("i", $_GET['xem']);
    $stmt2->execute();
    $chi_tiet = $stmt2->get_result();
}

$ds = $conn->query("SELECT d.*, t.ho_ten FROM donhang d
                     JOIN taikhoan t ON d.id_taikhoan = t.id
                     ORDER BY d.ngay_dat DESC");
?>

<h3>Danh sách đơn hàng</h3>
<table class="table table-bordered">
<tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr>
<?php while ($r = $ds->fetch_assoc()): ?>
<tr>
    <td>#<?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['ho_ten']) ?></td>
    <td><?= $r['ngay_dat'] ?></td>
    <td><?= number_format($r['tong_tien']) ?> đ</td>
    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['trang_thai']) ?></span></td>
    <td><a href="donhang.php?xem=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a></td>
</tr>
<?php endwhile; ?>
</table>

<?php if ($don): ?>
<hr>
<h4>Chi tiết đơn hàng #<?= $don['id'] ?></h4>
<p><b>Khách hàng:</b> <?= htmlspecialchars($don['ho_ten']) ?> (<?= htmlspecialchars($don['email']) ?>, <?= htmlspecialchars($don['sdt']) ?>)</p>
<p><b>Địa chỉ giao:</b> <?= htmlspecialchars($don['dia_chi_giao']) ?></p>

<table class="table table-bordered">
<tr><th>Sách</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>
<?php while ($ct = $chi_tiet->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($ct['ten_sach']) ?></td>
    <td><?= $ct['so_luong'] ?></td>
    <td><?= number_format($ct['don_gia']) ?> đ</td>
    <td><?= number_format($ct['don_gia'] * $ct['so_luong']) ?> đ</td>
</tr>
<?php endwhile; ?>
</table>

<form method="POST" class="d-flex gap-2 align-items-center">
    <input type="hidden" name="id_donhang" value="<?= $don['id'] ?>">
    <label>Cập nhật trạng thái:</label>
    <select name="trang_thai" class="form-select" style="width:220px">
        <?php foreach (['Chờ xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'] as $tt): ?>
            <option value="<?= $tt ?>" <?= $don['trang_thai'] == $tt ? 'selected' : '' ?>><?= $tt ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
</form>
<?php endif; ?>

<?php require_once 'footer.php'; ?>

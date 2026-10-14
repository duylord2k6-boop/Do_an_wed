<?php
require_once 'includes/config.php';

if (!isset($_SESSION['id_taikhoan'])) {
    header("Location: dang_nhap.php");
    exit;
}
$id_kh = $_SESSION['id_taikhoan'];

// Thêm sách vào giỏ (từ trang chi tiết gửi lên)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_sach'])) {
    $id_sach = (int) $_POST['id_sach'];
    $sl = max(1, (int) ($_POST['so_luong'] ?? 1));

    $check = $conn->prepare("SELECT id, so_luong FROM giohang WHERE id_taikhoan=? AND id_sach=?");
    $check->bind_param("ii", $id_kh, $id_sach);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $sl_moi = $row['so_luong'] + $sl;
        $stmt = $conn->prepare("UPDATE giohang SET so_luong=? WHERE id=?");
        $stmt->bind_param("ii", $sl_moi, $row['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO giohang (id_taikhoan, id_sach, so_luong) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_kh, $id_sach, $sl);
        $stmt->execute();
    }
    header("Location: gio_hang.php");
    exit;
}

// Cập nhật số lượng trực tiếp trong giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cap_nhat'])) {
    foreach ($_POST['so_luong'] as $id_gh => $sl) {
        $sl = max(1, (int) $sl);
        $stmt = $conn->prepare("UPDATE giohang SET so_luong=? WHERE id=? AND id_taikhoan=?");
        $stmt->bind_param("iii", $sl, $id_gh, $id_kh);
        $stmt->execute();
    }
    header("Location: gio_hang.php");
    exit;
}

// Xóa 1 sách khỏi giỏ
if (isset($_GET['xoa'])) {
    $stmt = $conn->prepare("DELETE FROM giohang WHERE id=? AND id_taikhoan=?");
    $stmt->bind_param("ii", $_GET['xoa'], $id_kh);
    $stmt->execute();
    header("Location: gio_hang.php");
    exit;
}

$sql = "SELECT g.id, g.so_luong, s.id AS id_sach, s.ten_sach, s.gia, s.hinh_anh, s.so_luong_ton
        FROM giohang g JOIN sach s ON g.id_sach = s.id
        WHERE g.id_taikhoan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_kh);
$stmt->execute();
$gio = $stmt->get_result();

$items = [];
$tong = 0;
while ($r = $gio->fetch_assoc()) {
    $r['thanh_tien'] = $r['gia'] * $r['so_luong'];
    $tong += $r['thanh_tien'];
    $items[] = $r;
}

require_once 'includes/header.php';
?>

<h3 class="mb-3">Giỏ hàng của bạn</h3>

<?php if (count($items) == 0): ?>
    <p class="text-muted">Giỏ hàng đang trống. <a href="index.php">Tiếp tục mua sắm</a></p>
<?php else: ?>
    <form method="POST">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Sách</th>
                    <th>Đơn giá</th>
                    <th style="width:120px">Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $r): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="uploads/<?= htmlspecialchars($r['hinh_anh']) ?>" width="50">
                            <?= htmlspecialchars($r['ten_sach']) ?>
                        </div>
                    </td>
                    <td><?= number_format($r['gia']) ?> đ</td>
                    <td>
                        <input type="number" name="so_luong[<?= $r['id'] ?>]" value="<?= $r['so_luong'] ?>"
                               min="1" max="<?= $r['so_luong_ton'] ?>" class="form-control form-control-sm">
                    </td>
                    <td><?= number_format($r['thanh_tien']) ?> đ</td>
                    <td><a href="gio_hang.php?xoa=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Xóa sách này khỏi giỏ hàng?')">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="cap_nhat" class="btn btn-outline-secondary">Cập nhật giỏ hàng</button>
    </form>

    <div class="text-end mt-3">
        <h5>Tổng cộng: <span class="gia-sach"><?= number_format($tong) ?> đ</span></h5>
        <a href="dat_hang.php" class="btn btn-success">Tiến hành đặt hàng</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

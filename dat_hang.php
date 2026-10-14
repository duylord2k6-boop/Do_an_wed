<?php
require_once 'includes/config.php';

if (!isset($_SESSION['id_taikhoan'])) {
    header("Location: dang_nhap.php");
    exit;
}
$id_kh = $_SESSION['id_taikhoan'];
$thanh_cong = false;
$ma_don = null;
$loi = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dia_chi = trim($_POST['dia_chi']);

    if ($dia_chi == "") {
        $loi = "Vui lòng nhập địa chỉ giao hàng!";
    } else {
        $sql = "SELECT g.id_sach, g.so_luong, s.gia, s.so_luong_ton, s.ten_sach
                FROM giohang g JOIN sach s ON g.id_sach = s.id
                WHERE g.id_taikhoan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_kh);
        $stmt->execute();
        $items = $stmt->get_result();

        $tong = 0;
        $ds = [];
        while ($r = $items->fetch_assoc()) {
            if ($r['so_luong'] > $r['so_luong_ton']) {
                $loi = "Sách \"" . $r['ten_sach'] . "\" không đủ số lượng tồn kho!";
                break;
            }
            $tong += $r['gia'] * $r['so_luong'];
            $ds[] = $r;
        }

        if ($loi == "" && count($ds) > 0) {
            $conn->begin_transaction();
            try {
                $stmt2 = $conn->prepare("INSERT INTO donhang (id_taikhoan, tong_tien, dia_chi_giao) VALUES (?, ?, ?)");
                $stmt2->bind_param("ids", $id_kh, $tong, $dia_chi);
                $stmt2->execute();
                $id_donhang = $conn->insert_id;

                foreach ($ds as $r) {
                    $stmt3 = $conn->prepare("INSERT INTO chitiet_donhang (id_donhang, id_sach, so_luong, don_gia) VALUES (?, ?, ?, ?)");
                    $stmt3->bind_param("iiid", $id_donhang, $r['id_sach'], $r['so_luong'], $r['gia']);
                    $stmt3->execute();

                    $stmt4 = $conn->prepare("UPDATE sach SET so_luong_ton = so_luong_ton - ? WHERE id = ?");
                    $stmt4->bind_param("ii", $r['so_luong'], $r['id_sach']);
                    $stmt4->execute();
                }

                $conn->query("DELETE FROM giohang WHERE id_taikhoan = $id_kh");
                $conn->commit();

                $thanh_cong = true;
                $ma_don = $id_donhang;
            } catch (Exception $e) {
                $conn->rollback();
                $loi = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        } elseif ($loi == "") {
            $loi = "Giỏ hàng của bạn đang trống!";
        }
    }
}

require_once 'includes/header.php';
?>

<h3 class="mb-3">Đặt hàng</h3>

<?php if ($thanh_cong): ?>
    <div class="alert alert-success">
        Đặt hàng thành công! Mã đơn hàng của bạn là <b>#<?= $ma_don ?></b>.
        <a href="index.php">Tiếp tục mua sắm</a>
    </div>
<?php else: ?>
    <?php if ($loi): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($loi) ?></div>
    <?php endif; ?>
    <form method="POST" class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Địa chỉ giao hàng</label>
            <textarea name="dia_chi" class="form-control" rows="3" required><?= $_SESSION['dia_chi_luu'] ?? '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
        <a href="gio_hang.php" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
    </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

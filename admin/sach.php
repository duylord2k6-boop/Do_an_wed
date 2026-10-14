<?php require_once 'header.php'; ?>

<?php
// ---- XÓA ----
if (isset($_GET['xoa'])) {
    $stmt = $conn->prepare("DELETE FROM sach WHERE id = ?");
    $stmt->bind_param("i", $_GET['xoa']);
    $stmt->execute();
    header("Location: sach.php");
    exit;
}

// ---- THÊM / SỬA ----
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_sach = trim($_POST['ten_sach']);
    $id_tacgia = $_POST['id_tacgia'] ?: null;
    $id_nxb = $_POST['id_nxb'] ?: null;
    $id_theloai = $_POST['id_theloai'] ?: null;
    $gia = (float) $_POST['gia'];
    $so_luong_ton = (int) $_POST['so_luong_ton'];
    $mo_ta = trim($_POST['mo_ta']);
    $nam_xb = $_POST['nam_xb'] ?: null;
    $id = $_POST['id'] ?? null;

    // Xử lý upload ảnh (nếu có chọn file mới)
    $ten_anh = null;
    if (!empty($_FILES['hinh_anh']['name'])) {
        $ten_anh = time() . "_" . basename($_FILES['hinh_anh']['name']);
        $duong_dan = __DIR__ . "/../uploads/" . $ten_anh;
        move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $duong_dan);
    }

    if ($id) {
        // Cập nhật sách
        if ($ten_anh) {
            $stmt = $conn->prepare("UPDATE sach SET ten_sach=?, id_tacgia=?, id_nxb=?, id_theloai=?, gia=?, so_luong_ton=?, mo_ta=?, nam_xb=?, hinh_anh=? WHERE id=?");
            $stmt->bind_param("siiidisssi", $ten_sach, $id_tacgia, $id_nxb, $id_theloai, $gia, $so_luong_ton, $mo_ta, $nam_xb, $ten_anh, $id);
        } else {
            $stmt = $conn->prepare("UPDATE sach SET ten_sach=?, id_tacgia=?, id_nxb=?, id_theloai=?, gia=?, so_luong_ton=?, mo_ta=?, nam_xb=? WHERE id=?");
            $stmt->bind_param("siiidisi", $ten_sach, $id_tacgia, $id_nxb, $id_theloai, $gia, $so_luong_ton, $mo_ta, $nam_xb, $id);
        }
        $stmt->execute();
    } else {
        // Thêm mới
        if (!$ten_anh) $ten_anh = 'no-image.png';
        $stmt = $conn->prepare("INSERT INTO sach (ten_sach, id_tacgia, id_nxb, id_theloai, gia, so_luong_ton, mo_ta, nam_xb, hinh_anh) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("siiidisss", $ten_sach, $id_tacgia, $id_nxb, $id_theloai, $gia, $so_luong_ton, $mo_ta, $nam_xb, $ten_anh);
        $stmt->execute();
    }
    header("Location: sach.php");
    exit;
}

// ---- Lấy dữ liệu để hiển thị form sửa (nếu có ?sua=id) ----
$sach_sua = null;
if (isset($_GET['sua'])) {
    $stmt = $conn->prepare("SELECT * FROM sach WHERE id = ?");
    $stmt->bind_param("i", $_GET['sua']);
    $stmt->execute();
    $sach_sua = $stmt->get_result()->fetch_assoc();
}

$tacgia_list = $conn->query("SELECT * FROM tacgia ORDER BY ten_tacgia");
$nxb_list = $conn->query("SELECT * FROM nhaxuatban ORDER BY ten_nxb");
$theloai_list = $conn->query("SELECT * FROM theloai ORDER BY ten_theloai");

$ds_sach = $conn->query("SELECT s.*, t.ten_tacgia FROM sach s LEFT JOIN tacgia t ON s.id_tacgia = t.id ORDER BY s.id DESC");
?>

<h3><?= $sach_sua ? 'Sửa sách' : 'Thêm sách mới' ?></h3>

<form method="POST" enctype="multipart/form-data" class="row g-3 mb-4 border p-3 rounded bg-light">
    <?php if ($sach_sua): ?>
        <input type="hidden" name="id" value="<?= $sach_sua['id'] ?>">
    <?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">Tên sách</label>
        <input type="text" name="ten_sach" class="form-control" required
               value="<?= htmlspecialchars($sach_sua['ten_sach'] ?? '') ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Giá (đ)</label>
        <input type="number" name="gia" class="form-control" required
               value="<?= $sach_sua['gia'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Số lượng tồn</label>
        <input type="number" name="so_luong_ton" class="form-control" required
               value="<?= $sach_sua['so_luong_ton'] ?? 0 ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Tác giả</label>
        <select name="id_tacgia" class="form-select">
            <option value="">-- Chọn tác giả --</option>
            <?php $tacgia_list->data_seek(0); while ($tg = $tacgia_list->fetch_assoc()): ?>
                <option value="<?= $tg['id'] ?>" <?= (($sach_sua['id_tacgia'] ?? null) == $tg['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tg['ten_tacgia']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Nhà xuất bản</label>
        <select name="id_nxb" class="form-select">
            <option value="">-- Chọn NXB --</option>
            <?php $nxb_list->data_seek(0); while ($n = $nxb_list->fetch_assoc()): ?>
                <option value="<?= $n['id'] ?>" <?= (($sach_sua['id_nxb'] ?? null) == $n['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($n['ten_nxb']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Thể loại</label>
        <select name="id_theloai" class="form-select">
            <option value="">-- Chọn thể loại --</option>
            <?php $theloai_list->data_seek(0); while ($tl = $theloai_list->fetch_assoc()): ?>
                <option value="<?= $tl['id'] ?>" <?= (($sach_sua['id_theloai'] ?? null) == $tl['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tl['ten_theloai']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Năm xuất bản</label>
        <input type="number" name="nam_xb" class="form-control" value="<?= $sach_sua['nam_xb'] ?? '' ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Ảnh bìa <?= $sach_sua ? '(để trống nếu không đổi ảnh)' : '' ?></label>
        <input type="file" name="hinh_anh" class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea name="mo_ta" class="form-control" rows="3"><?= htmlspecialchars($sach_sua['mo_ta'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><?= $sach_sua ? 'Cập nhật' : 'Thêm sách' ?></button>
        <?php if ($sach_sua): ?>
            <a href="sach.php" class="btn btn-outline-secondary">Hủy</a>
        <?php endif; ?>
    </div>
</form>

<h4>Danh sách sách</h4>
<table class="table table-bordered align-middle">
<tr>
    <th>Ảnh</th><th>Tên sách</th><th>Tác giả</th><th>Giá</th><th>Tồn kho</th><th></th>
</tr>
<?php while ($s = $ds_sach->fetch_assoc()): ?>
<tr>
    <td><img src="../uploads/<?= htmlspecialchars($s['hinh_anh']) ?>" width="50"></td>
    <td><?= htmlspecialchars($s['ten_sach']) ?></td>
    <td><?= htmlspecialchars($s['ten_tacgia'] ?? '') ?></td>
    <td><?= number_format($s['gia']) ?> đ</td>
    <td><?= $s['so_luong_ton'] ?></td>
    <td>
        <a href="sach.php?sua=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
        <a href="sach.php?xoa=<?= $s['id'] ?>" class="btn btn-sm btn-danger"
           onclick="return confirm('Xóa sách này?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php require_once 'footer.php'; ?>

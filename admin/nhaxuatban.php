<?php require_once 'header.php'; ?>

<?php
if (isset($_GET['xoa'])) {
    $stmt = $conn->prepare("DELETE FROM nhaxuatban WHERE id = ?");
    $stmt->bind_param("i", $_GET['xoa']);
    $stmt->execute();
    header("Location: nhaxuatban.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = trim($_POST['ten_nxb']);
    $dia_chi = trim($_POST['dia_chi']);
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("UPDATE nhaxuatban SET ten_nxb=?, dia_chi=? WHERE id=?");
        $stmt->bind_param("ssi", $ten, $dia_chi, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO nhaxuatban (ten_nxb, dia_chi) VALUES (?, ?)");
        $stmt->bind_param("ss", $ten, $dia_chi);
    }
    $stmt->execute();
    header("Location: nhaxuatban.php");
    exit;
}

$sua = null;
if (isset($_GET['sua'])) {
    $stmt = $conn->prepare("SELECT * FROM nhaxuatban WHERE id = ?");
    $stmt->bind_param("i", $_GET['sua']);
    $stmt->execute();
    $sua = $stmt->get_result()->fetch_assoc();
}

$ds = $conn->query("SELECT * FROM nhaxuatban ORDER BY id DESC");
?>

<h3><?= $sua ? 'Sửa nhà xuất bản' : 'Thêm nhà xuất bản' ?></h3>
<form method="POST" class="row g-3 mb-4 border p-3 rounded bg-light">
    <?php if ($sua): ?><input type="hidden" name="id" value="<?= $sua['id'] ?>"><?php endif; ?>
    <div class="col-md-6">
        <input type="text" name="ten_nxb" class="form-control" placeholder="Tên nhà xuất bản" required
               value="<?= htmlspecialchars($sua['ten_nxb'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <input type="text" name="dia_chi" class="form-control" placeholder="Địa chỉ"
               value="<?= htmlspecialchars($sua['dia_chi'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary"><?= $sua ? 'Cập nhật' : 'Thêm' ?></button>
        <?php if ($sua): ?><a href="nhaxuatban.php" class="btn btn-outline-secondary">Hủy</a><?php endif; ?>
    </div>
</form>

<table class="table table-bordered">
<tr><th>Tên NXB</th><th>Địa chỉ</th><th style="width:150px"></th></tr>
<?php while ($r = $ds->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($r['ten_nxb']) ?></td>
    <td><?= htmlspecialchars($r['dia_chi']) ?></td>
    <td>
        <a href="nhaxuatban.php?sua=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
        <a href="nhaxuatban.php?xoa=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php require_once 'footer.php'; ?>

<?php require_once 'header.php'; ?>

<?php
if (isset($_GET['xoa'])) {
    $stmt = $conn->prepare("DELETE FROM tacgia WHERE id = ?");
    $stmt->bind_param("i", $_GET['xoa']);
    $stmt->execute();
    header("Location: tacgia.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = trim($_POST['ten_tacgia']);
    $tieu_su = trim($_POST['tieu_su']);
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("UPDATE tacgia SET ten_tacgia=?, tieu_su=? WHERE id=?");
        $stmt->bind_param("ssi", $ten, $tieu_su, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO tacgia (ten_tacgia, tieu_su) VALUES (?, ?)");
        $stmt->bind_param("ss", $ten, $tieu_su);
    }
    $stmt->execute();
    header("Location: tacgia.php");
    exit;
}

$sua = null;
if (isset($_GET['sua'])) {
    $stmt = $conn->prepare("SELECT * FROM tacgia WHERE id = ?");
    $stmt->bind_param("i", $_GET['sua']);
    $stmt->execute();
    $sua = $stmt->get_result()->fetch_assoc();
}

$ds = $conn->query("SELECT * FROM tacgia ORDER BY id DESC");
?>

<h3><?= $sua ? 'Sửa tác giả' : 'Thêm tác giả' ?></h3>
<form method="POST" class="row g-3 mb-4 border p-3 rounded bg-light">
    <?php if ($sua): ?><input type="hidden" name="id" value="<?= $sua['id'] ?>"><?php endif; ?>
    <div class="col-md-6">
        <input type="text" name="ten_tacgia" class="form-control" placeholder="Tên tác giả" required
               value="<?= htmlspecialchars($sua['ten_tacgia'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <textarea name="tieu_su" class="form-control" placeholder="Tiểu sử (tùy chọn)"><?= htmlspecialchars($sua['tieu_su'] ?? '') ?></textarea>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary"><?= $sua ? 'Cập nhật' : 'Thêm' ?></button>
        <?php if ($sua): ?><a href="tacgia.php" class="btn btn-outline-secondary">Hủy</a><?php endif; ?>
    </div>
</form>

<table class="table table-bordered">
<tr><th>Tên tác giả</th><th>Tiểu sử</th><th style="width:150px"></th></tr>
<?php while ($r = $ds->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($r['ten_tacgia']) ?></td>
    <td><?= htmlspecialchars($r['tieu_su']) ?></td>
    <td>
        <a href="tacgia.php?sua=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
        <a href="tacgia.php?xoa=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php require_once 'footer.php'; ?>

<?php require_once 'header.php'; ?>

<?php
if (isset($_GET['xoa'])) {
    $stmt = $conn->prepare("DELETE FROM theloai WHERE id = ?");
    $stmt->bind_param("i", $_GET['xoa']);
    $stmt->execute();
    header("Location: theloai.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = trim($_POST['ten_theloai']);
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $conn->prepare("UPDATE theloai SET ten_theloai=? WHERE id=?");
        $stmt->bind_param("si", $ten, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO theloai (ten_theloai) VALUES (?)");
        $stmt->bind_param("s", $ten);
    }
    $stmt->execute();
    header("Location: theloai.php");
    exit;
}

$sua = null;
if (isset($_GET['sua'])) {
    $stmt = $conn->prepare("SELECT * FROM theloai WHERE id = ?");
    $stmt->bind_param("i", $_GET['sua']);
    $stmt->execute();
    $sua = $stmt->get_result()->fetch_assoc();
}

$ds = $conn->query("SELECT * FROM theloai ORDER BY id DESC");
?>

<h3><?= $sua ? 'Sửa thể loại' : 'Thêm thể loại' ?></h3>
<form method="POST" class="row g-3 mb-4 border p-3 rounded bg-light">
    <?php if ($sua): ?><input type="hidden" name="id" value="<?= $sua['id'] ?>"><?php endif; ?>
    <div class="col-md-6">
        <input type="text" name="ten_theloai" class="form-control" placeholder="Tên thể loại" required
               value="<?= htmlspecialchars($sua['ten_theloai'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary"><?= $sua ? 'Cập nhật' : 'Thêm' ?></button>
        <?php if ($sua): ?><a href="theloai.php" class="btn btn-outline-secondary">Hủy</a><?php endif; ?>
    </div>
</form>

<table class="table table-bordered">
<tr><th>Tên thể loại</th><th style="width:150px"></th></tr>
<?php while ($r = $ds->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($r['ten_theloai']) ?></td>
    <td>
        <a href="theloai.php?sua=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
        <a href="theloai.php?xoa=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<?php require_once 'footer.php'; ?>

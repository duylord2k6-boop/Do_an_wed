<?php
require_once 'includes/config.php';

// Lấy danh sách thể loại để hiển thị bộ lọc
$theloai_list = $conn->query("SELECT * FROM theloai ORDER BY ten_theloai");

// Xây câu truy vấn theo điều kiện tìm kiếm / lọc
$where = [];
$params = [];
$types = "";

if (!empty($_GET['keyword'])) {
    $where[] = "(s.ten_sach LIKE ? OR t.ten_tacgia LIKE ?)";
    $kw = "%" . $_GET['keyword'] . "%";
    $params[] = $kw;
    $params[] = $kw;
    $types .= "ss";
}

if (!empty($_GET['theloai'])) {
    $where[] = "s.id_theloai = ?";
    $params[] = $_GET['theloai'];
    $types .= "i";
}

$sql = "SELECT s.*, t.ten_tacgia FROM sach s
        LEFT JOIN tacgia t ON s.id_tacgia = t.id";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY s.id DESC";

$stmt = $conn->prepare($sql);
if ($types != "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

require_once 'includes/header.php';
?>

<div class="row">
  <!-- Bộ lọc thể loại -->
  <div class="col-md-3">
    <h5>Thể loại</h5>
    <div class="list-group mb-4">
      <a href="index.php" class="list-group-item list-group-item-action <?= empty($_GET['theloai']) ? 'active' : '' ?>">
        Tất cả
      </a>
      <?php while ($tl = $theloai_list->fetch_assoc()): ?>
        <a href="index.php?theloai=<?= $tl['id'] ?>"
           class="list-group-item list-group-item-action <?= (isset($_GET['theloai']) && $_GET['theloai'] == $tl['id']) ? 'active' : '' ?>">
          <?= htmlspecialchars($tl['ten_theloai']) ?>
        </a>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Danh sách sách -->
  <div class="col-md-9">
    <h3 class="mb-3">
      <?= !empty($_GET['keyword']) ? 'Kết quả tìm kiếm: "' . htmlspecialchars($_GET['keyword']) . '"' : 'Sách mới nhất' ?>
    </h3>

    <div class="row g-3">
      <?php if ($result->num_rows == 0): ?>
        <p class="text-muted">Không tìm thấy sách nào.</p>
      <?php endif; ?>

      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-6 col-md-4">
          <div class="sach-item">
            <a href="chi_tiet.php?id=<?= $row['id'] ?>">
              <img src="uploads/<?= htmlspecialchars($row['hinh_anh']) ?>" alt="<?= htmlspecialchars($row['ten_sach']) ?>">
            </a>
            <h6><?= htmlspecialchars($row['ten_sach']) ?></h6>
            <p class="text-muted mb-1"><?= htmlspecialchars($row['ten_tacgia'] ?? 'Đang cập nhật') ?></p>
            <p class="gia-sach"><?= number_format($row['gia']) ?> đ</p>
            <a href="chi_tiet.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

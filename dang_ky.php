<?php require_once 'includes/config.php';

$loi = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $email  = trim($_POST['email']);
    $mat_khau = $_POST['mat_khau'];
    $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);

    if ($ho_ten == "" || $email == "" || $mat_khau == "") {
        $loi = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        $check = $conn->prepare("SELECT id FROM taikhoan WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $loi = "Email này đã được đăng ký!";
        } else {
            $stmt = $conn->prepare("INSERT INTO taikhoan (ho_ten, email, mat_khau) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $ho_ten, $email, $mat_khau_hash);
            $stmt->execute();
            header("Location: dang_nhap.php?dang_ky=1");
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h3 class="mb-3">Đăng ký tài khoản</h3>
    <?php if ($loi): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($loi) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" name="ho_ten" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="mat_khau" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
    </form>
    <p class="mt-3">Đã có tài khoản? <a href="dang_nhap.php">Đăng nhập</a></p>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

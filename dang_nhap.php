<?php require_once 'includes/config.php';

$loi = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $mat_khau = $_POST['mat_khau'];

    $stmt = $conn->prepare("SELECT * FROM taikhoan WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($mat_khau, $user['mat_khau'])) {
            $_SESSION['id_taikhoan'] = $user['id'];
            $_SESSION['ho_ten'] = $user['ho_ten'];
            $_SESSION['vai_tro'] = $user['vai_tro'];
            header("Location: index.php");
            exit;
        } else {
            $loi = "Sai email hoặc mật khẩu!";
        }
    } else {
        $loi = "Sai email hoặc mật khẩu!";
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h3 class="mb-3">Đăng nhập</h3>

    <?php if (isset($_GET['dang_ky'])): ?>
        <div class="alert alert-success">Đăng ký thành công! Hãy đăng nhập.</div>
    <?php endif; ?>
    <?php if ($loi): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($loi) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="mat_khau" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>
    <p class="mt-3">Chưa có tài khoản? <a href="dang_ky.php">Đăng ký ngay</a></p>
    <p class="text-muted small">Tài khoản admin mẫu: admin@bansach.com / admin123</p>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

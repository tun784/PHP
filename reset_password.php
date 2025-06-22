<?php
session_start();
include("db.php");

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: forgot_password.php");
    exit();
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    if ($new_pass !== $confirm_pass) {
        $error = "Mật khẩu không khớp.";
    } else {
        $email = $_SESSION['reset_email'];
        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_pass, $email);
        if ($stmt->execute()) {
            $success = "Đặt lại mật khẩu thành công. <a href='login.php'>Đăng nhập ngay</a>";
            session_unset(); session_destroy();
        } else {
            $error = "Lỗi khi cập nhật mật khẩu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu - Quạt Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">
<div class="register-container">
    <div class="form-header">
        <h1><i class="fas fa-unlock-alt" style="color: #ff6600; margin-right: 10px;"></i>Đặt lại mật khẩu</h1>
        <p>Vui lòng nhập mật khẩu mới cho tài khoản của bạn</p>
    </div>

    <div class="form-body">
        <form method="POST" id="resetForm">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="new_pass" class="form-input" placeholder="Mật khẩu mới" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_pass" class="form-input" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="submit-btn">Cập nhật mật khẩu</button>
        </form>

        <?php if ($success): ?>
            <p class="success-message"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>

        <div class="form-links">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Quay lại đăng nhập</a>
        </div>
    </div>
</div>
</body>
</html>

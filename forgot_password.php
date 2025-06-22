<?php
session_start();
include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expires'] = time() + 300; // 5 phút

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '83f9bebaa7784f';
            $mail->Password = 'ee3199dd8c11fa';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@quatstore.test', 'Quạt Store');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Ma OTP khoi phuc mat khau';
            $mail->Body = "Ma OTP cua ban la: <b>$otp</b>. Ma nay se het han sau 5 phut.";

            $mail->send();
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $error = "Không thể gửi OTP: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email không tồn tại trong hệ thống.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Shop Bán Quạt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">

<div class="register-container">
    <div class="form-header">
        <h1><i class="fas fa-unlock-alt" style="color: #ff6600; margin-right: 10px;"></i>Quên Mật Khẩu</h1>
        <p>Nhập email của bạn để nhận mã OTP</p>
    </div>

    <div class="form-body">
        <form method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-input" placeholder="Nhập email đã đăng ký" required>
            </div>

            <button type="submit" class="submit-btn">
                <span>Gửi Mã OTP</span>
            </button>
        </form>

        <?php if ($error): ?>
            <p style="color: red; margin-top: 15px; text-align: center;"><i class="fas fa-exclamation-circle"></i> <?= $error ?></p>
        <?php elseif ($success): ?>
            <p style="color: green; margin-top: 15px; text-align: center;"><i class="fas fa-check-circle"></i> <?= $success ?></p>
        <?php endif; ?>

        <div class="form-links" style="margin-top: 20px;">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Quay lại đăng nhập</a>
            <span style="color: #e2e8f0;">|</span>
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
        </div>
    </div>
</div>

</body>
</html>

<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = 'customer';

    if (empty($user_name) || empty($password) || empty($full_name) || empty($phone_number) || empty($address) || empty($email)) {
        echo "<script>showNotification('Vui lòng nhập đầy đủ thông tin!', 'error');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO `user` (`user_name`, `password`, `full_name`, `phone_number`, `address`, `email`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $user_name, $password, $full_name, $phone_number, $address, $email, $role);

        if ($stmt->execute()) {
            echo "<script>showNotification('Đăng ký thành công!', 'success'); setTimeout(() => window.location.href = 'index.php', 2000);</script>";
        } else {
            echo "<script>showNotification('Lỗi đăng ký: " . $stmt->error . "', 'error');</script>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Shop Bán Quạt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">

<div class="register-container">
    <div class="form-header">
        <h1><i class="fas fa-fan" style="color: #ff6600; margin-right: 10px;"></i>Đăng Ký</h1>
        <p>Tạo tài khoản mới để bắt đầu mua sắm</p>
    </div>

    <div class="form-body">
        <form method="POST" id="registerForm">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="user_name" class="form-input" placeholder="Tên đăng nhập" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-input" placeholder="Mật khẩu" required id="password">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </button>
            </div>

            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="full_name" class="form-input" placeholder="Họ và tên đầy đủ" required>
            </div>

            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone_number" class="form-input" placeholder="Số điện thoại" required maxlength="10">
            </div>

            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="address" class="form-input" placeholder="Địa chỉ" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-input" placeholder="Địa chỉ email" required>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="btnText">Đăng Ký Ngay</span>
            </button>
        </form>

        <div class="divider"><span>hoặc</span></div>

        <a href="google-login.php" class="google-btn">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
            Đăng ký bằng Google
        </a>

        <div class="form-links">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đã có tài khoản?</a>
            <span style="color: #e2e8f0;">|</span>
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

function showNotification(message, type) {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    submitBtn.disabled = true;
    btnText.innerHTML = '<span class="loading"></span>Đang xử lý...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        btnText.innerHTML = 'Đăng Ký Ngay';
    }, 3000);
});

document.querySelector('input[name="phone_number"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substring(0, 10);
    e.target.value = value;
});

document.querySelectorAll('.form-input').forEach(input => {
    input.addEventListener('blur', function() {
        this.style.borderColor = this.value.trim() === '' ? '#f56565' : '#48bb78';
    });
    
    input.addEventListener('input', function() {
        if (this.style.borderColor === 'rgb(245, 101, 101)') {
            this.style.borderColor = '#e2e8f0';
        }
    });
});
</script>

</body>
</html>
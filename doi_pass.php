<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $user_id = $_SESSION['user_id'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>showNotification('Vui lòng nhập đầy đủ thông tin!', 'error');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>showNotification('Mật khẩu mới và xác nhận mật khẩu không khớp!', 'error');</script>";
    } else {
        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['password'] === $current_password) {
                $update_stmt = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $new_password, $user_id);
                
                if ($update_stmt->execute()) {
                    echo "<script>showNotification('Đổi mật khẩu thành công!', 'success'); setTimeout(() => window.location.href = 'index.php', 2000);</script>";
                } else {
                    echo "<script>showNotification('Lỗi khi đổi mật khẩu: " . $update_stmt->error . "', 'error');</script>";
                }
                $update_stmt->close();
            } else {
                echo "<script>showNotification('Mật khẩu hiện tại không đúng!', 'error');</script>";
            }
        } else {
            echo "<script>showNotification('Lỗi: Không tìm thấy người dùng!', 'error');</script>";
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
    <title>Đổi Mật Khẩu - Shop Bán Quạt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">
<div class="register-container">
    <div class="form-header">
        <h1><i class="fas fa-fan" style="color: #ff6600; margin-right: 10px;"></i>Đổi Mật Khẩu</h1>
        <p>Cập nhật mật khẩu mới để bảo mật tài khoản</p>
    </div>

    <div class="form-body">
        <form method="POST" id="changePasswordForm">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="current_password" class="form-input" placeholder="Mật khẩu hiện tại" required id="currentPassword">
                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword', 'currentPasswordIcon')">
                    <i class="fas fa-eye" id="currentPasswordIcon"></i>
                </button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="new_password" class="form-input" placeholder="Mật khẩu mới" required id="newPassword">
                <button type="button" class="password-toggle" onclick="togglePassword('newPassword', 'newPasswordIcon')">
                    <i class="fas fa-eye" id="newPasswordIcon"></i>
                </button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" class="form-input" placeholder="Xác nhận mật khẩu mới" required id="confirmPassword">
                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')">
                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                </button>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="btnText">Đổi Mật Khẩu</span>
            </button>
        </form>

        <div class="form-links">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span style="color: #e2e8f0;">|</span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const passwordIcon = document.getElementById(iconId);
    
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

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    submitBtn.disabled = true;
    btnText.innerHTML = '<span class="loading"></span>Đang xử lý...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        btnText.innerHTML = 'Đổi Mật Khẩu';
    }, 3000);
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
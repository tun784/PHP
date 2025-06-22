<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_name = ? AND password = ?");
    if (!$stmt) {
        die("Lỗi prepare: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $path = "";
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['role'] = $user['role'] ?? 'customer';
        $_SESSION['user_id'] = $user['user_id'];
        if ($_SESSION['role'] === 'admin') {
            $path = "admin/view/index.php";
        } else {
            $path = "index.php";
        }
        header("Location: $path");
        exit();
    } else {
        echo "<h3 style='color: red;'>Sai tài khoản hoặc mật khẩu</h3>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Shop Bán Quạt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">
<div class="register-container">
    <div class="form-header">
        <h1><i class="fas fa-fan" style="color: #ff6600; margin-right: 10px;"></i>Đăng Nhập</h1>
        <p>Đăng nhập để tiếp tục mua sắm</p>
    </div>

    <div class="form-body">
        <form method="POST" id="loginForm">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-input" placeholder="Tên đăng nhập" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-input" placeholder="Mật khẩu" required id="password">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </button>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="btnText">Đăng Nhập Ngay</span>
            </button>
        </form>

        <div class="divider"><span>hoặc</span></div>

        <a href="google-login.php" class="google-btn">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
            Đăng nhập bằng Google
        </a>

        <div class="form-links">
            <a href="register.php"><i class="fas fa-user-plus"></i> Đăng ký tài khoản</a>
            <span style="color: #e2e8f0;">|</span>
            <a href="forgot_password.php"><i class="fas fa-unlock-alt"></i> Quên mật khẩu?</a>
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

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    submitBtn.disabled = true;
    btnText.innerHTML = '<span class="loading"></span>Đang xử lý...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        btnText.innerHTML = 'Đăng Nhập Ngay';
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


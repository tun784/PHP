<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'] ?? '';
    if (time() > $_SESSION['otp_expires']) {
        $error = "Mã OTP đã hết hạn. Vui lòng gửi lại.";
        unset($_SESSION['otp']);
    } elseif ($entered_otp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Mã OTP không đúng.";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh OTP - Quạt Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .otp-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        .otp-container h2 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .otp-container input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .otp-container button {
            width: 100%;
            padding: 10px;
            background-color: #4299e1;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .otp-container button:hover {
            background-color: #3182ce;
        }
        .otp-container p {
            text-align: center;
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="otp-container">
    <h2><i class="fas fa-key"></i> Xác minh OTP</h2>
    <form method="POST">
        <input type="text" name="otp" placeholder="Nhập mã OTP" required>
        <button type="submit">Xác nhận</button>
        <p><?= htmlspecialchars($error) ?></p>
    </form>
</div>
</body>
</html>

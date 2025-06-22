<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Khách hàng';

if (!$user_id) {
    die("Bạn cần đăng nhập để thanh toán.");
}

// Truy vấn giỏ hàng
$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.product_name AS name, p.price, p.picture
    FROM cart c
    JOIN product p ON c.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}
$stmt->close();

// Xử lý voucher (lấy từ session hoặc GET)
$voucher_code = $_GET['voucher'] ?? ($_SESSION['voucher_code'] ?? '');
$discount_percent = 0;
$discount_amount = 0;
$total_after_discount = $total;

if ($voucher_code) {
    $stmt_voucher = $conn->prepare("SELECT discount_percent FROM voucher WHERE code = ? AND expiry_date >= CURDATE()");
    $stmt_voucher->bind_param("s", $voucher_code);
    $stmt_voucher->execute();
    $res_voucher = $stmt_voucher->get_result();
    if ($row_voucher = $res_voucher->fetch_assoc()) {
        $discount_percent = $row_voucher['discount_percent'];
        $discount_amount = $total * $discount_percent / 100;
        $total_after_discount = $total - $discount_amount;
        $_SESSION['voucher_code'] = $voucher_code; // lưu lại voucher
    } else {
        $voucher_error = "Voucher không hợp lệ hoặc đã hết hạn.";
        unset($_SESSION['voucher_code']);
    }
    $stmt_voucher->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            gap: 40px;
        }

        .left,
        .right {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ddd;
        }

        .left {
            flex: 2;
        }

        .right {
            flex: 1;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .payment-method {
            margin-bottom: 20px;
        }

        .payment-method input[type="radio"] {
            width: auto;
            margin: 0 8px 0 0;
            vertical-align: middle;
        }

        .payment-method label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        button {
            width: 100%;
            background: #f9b000;
            color: white;
            padding: 15px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #d99900;
        }

        .order-summary {
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <h2>Xin chào, <?= htmlspecialchars($username) ?></h2>
    <p>Đơn hàng của bạn có <?= count($cart_items) ?> sản phẩm</p>

    <div class="container">
        <form class="left" action="process_payment.php" method="POST">
            <h3>Thông tin vận chuyển</h3>

            <label>Tên khách hàng</label>
            <input type="text" name="fullname" required>

            <label>Số điện thoại</label>
            <input type="text" name="phone" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Địa chỉ</label>
            <input type="text" name="address" required>

            <label>Tỉnh / Thành phố</label>
            <select name="province" required>
                <option value="">Chọn tỉnh</option>
                <option value="TP.HCM">TP.HCM</option>
                <option value="Hà Nội">Hà Nội</option>
            </select>

            <label>Quận / Huyện</label>
            <select name="district" required>
                <option value="">Chọn quận/huyện</option>
                <option value="Quận 1">Quận 1</option>
                <option value="Quận 3">Quận 3</option>
            </select>

            <label>Phường / Xã</label>
            <select name="ward" required>
                <option value="">Chọn phường/xã</option>
                <option value="Phường A">Phường A</option>
            </select>

            <label>Ghi chú</label>
            <textarea name="note" rows="3"></textarea>

            <h3>Phương thức thanh toán</h3>
            <div class="payment-method">
                <label><input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng
                    (COD)</label><br>
                <label><input type="radio" name="payment_method" value="vnpay"> Thanh toán qua VNPay</label>
            </div>

            <button type="submit">XÁC NHẬN THANH TOÁN</button>
        </form>

        <div class="right">
            <h3>Đơn hàng của bạn</h3>
            <div class="order-summary">
                <?php foreach ($cart_items as $item): ?>
                    <div style="margin-bottom: 10px;">
                        <b><?= htmlspecialchars($item['name']) ?></b><br>
                        Giá: <?= number_format($item['price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?><br>
                        <small>Tổng: <?= number_format($item['subtotal'], 0, ',', '.') ?>đ</small>
                    </div>
                <?php endforeach; ?>
                <hr>
                <p>Tạm tính: <?= number_format($total, 0, ',', '.') ?>đ</p>

                <form method="GET" style="margin-bottom:10px;">
                    <input type="text" name="voucher" placeholder="Nhập mã voucher"
                        value="<?= htmlspecialchars($voucher_code) ?>">
                    <button type="submit">Áp dụng</button>
                </form>

                <?php if (!empty($voucher_error)): ?>
                    <p style="color:red;"><?= htmlspecialchars($voucher_error) ?></p>
                <?php elseif ($discount_percent > 0): ?>
                    <p>Giảm giá: <?= $discount_percent ?>% (-<?= number_format($discount_amount, 0, ',', '.') ?>đ)</p>
                <?php endif; ?>

                <p>Phí vận chuyển: <b>Miễn phí</b></p>
                <h3>Tổng hóa đơn: <span
                        style="color: red;"><?= number_format($total_after_discount, 0, ',', '.') ?>đ</span></h3>
            </div>
        </div>
    </div>

</body>

</html>
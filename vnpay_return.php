<?php
session_start();
require_once("./config.php");
include 'db.php';

$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
$inputData = array();

foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);

$hashData = '';
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

// Kiểm tra tính hợp lệ của chữ ký
if ($secureHash === $vnp_SecureHash) {
    if ($_GET['vnp_ResponseCode'] == '00') {
        // ✅ Giao dịch thành công
        $user_id = $_SESSION['user_id'] ?? 0;
        if ($user_id == 0) {
            echo "<h2>Không xác định được người dùng.</h2>";
            exit();
        }

        // Lấy thông tin từ giỏ hàng
        $cart_items = [];
        $total = 0;
        $stmt = $conn->prepare("SELECT cart.product_id, cart.quantity, product.price, product.product_name 
                                FROM cart 
                                JOIN product ON cart.product_id = product.product_id 
                                WHERE cart.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
            $total += $row['price'] * $row['quantity'];
        }

        if (empty($cart_items)) {
            echo "<h2>Không tìm thấy sản phẩm trong giỏ hàng.</h2>";
            exit();
        }

        // Chèn đơn hàng
        $stmt = $conn->prepare("INSERT INTO `order` (user_id, total_amount, status) VALUES (?, ?, 'Completed')");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Chèn chi tiết đơn hàng
        foreach ($cart_items as $item) {
            $stmt_detail = $conn->prepare("INSERT INTO orderdetail (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_detail->execute();
        }

        // Xoá giỏ hàng
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        echo "<h2 style='color:green'>🎉 Thanh toán thành công! Cảm ơn bạn đã mua hàng.</h2>";
        echo "<p>Mã giao dịch: " . $_GET['vnp_TxnRef'] . "</p>";
        echo "<p><a href='index.php'>🔙 Quay lại trang chủ</a></p>";
    } else {
        echo "<h2 style='color:red'>❌ Giao dịch không thành công. Vui lòng thử lại.</h2>";
        echo "<p><a href='cart.php'>🔙 Quay lại giỏ hàng</a></p>";
    }
} else {
    echo "<h2 style='color:red'>⚠️ Chữ ký không hợp lệ.</h2>";
}
?>

<?php
session_start();
include 'db.php';
require_once("./config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $note = $_POST['note'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'] ?? 0;

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
        echo "<h2>Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.</h2>";
        exit();
    }
    $voucher_code = $_GET['voucher'] ?? ($_SESSION['voucher_code'] ?? '');
    if ($payment_method === 'cod') {
        $stmt = $conn->prepare("INSERT INTO `order` (user_id, total_amount, status, code) VALUES (?, ?, 'Pending', ?)");
        $stmt->bind_param("ids", $user_id, $total, $voucher_code);
        $stmt->execute();
        $order_id = $conn->insert_id;


        foreach ($cart_items as $item) {
            $stmt_detail = $conn->prepare("INSERT INTO `orderdetail` (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_detail->execute();
        }

        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        echo '
        <h2>Đặt hàng thành công (COD). Cảm ơn bạn!</h2>
        <p>Bạn sẽ được chuyển về trang chủ sau <span id="countdown">5</span> giây...</p>
        <script>
            let seconds = 5;
            const countdownEl = document.getElementById("countdown");
            const interval = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = "index.php";
                }
            }, 1000);
        </script>
    ';
    
        exit();

    } else if ($payment_method === 'vnpay') {
        $vnp_TxnRef = rand(10000, 99999); // Mã giao dịch duy nhất
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $vnp_TxnRef;
        $vnp_Amount = $total * 100; // x100 theo quy định của VNPay
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        ];

        // Sắp xếp theo key
        ksort($inputData);
        $hashdata = '';
        $query = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;

        header('Location: ' . $vnp_Url);
        exit();
    }
}
?>

<?php
ob_start();

session_start();
include 'db.php';

$errorMsg = '';
$successMsg = '';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = $userId LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}

$vouchers = [];
$voucherQuery = mysqli_query($conn, "SELECT code, discount_percent FROM voucher");
if ($voucherQuery) {
    while ($row = mysqli_fetch_assoc($voucherQuery)) {
        $vouchers[$row['code']] = $row['discount_percent'];
    }
}

$orderDetails = [];
$orderQuery = mysqli_query($conn, "SELECT o.order_id FROM `order` o WHERE o.user_id = $userId");
$orderIds = [];

while ($row = mysqli_fetch_assoc($orderQuery)) {
    $orderIds[] = $row['order_id'];
}

if (!empty($orderIds)) {
    $idList = implode(',', $orderIds);
    $detailsQuery = mysqli_query($conn, "SELECT od.order_id, p.product_name, p.picture, od.quantity, od.unit_price
                                         FROM orderdetail od
                                         JOIN product p ON od.product_id = p.product_id
                                         WHERE od.order_id IN ($idList)");
    while ($row = mysqli_fetch_assoc($detailsQuery)) {
        $orderDetails[$row['order_id']][] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Email không hợp lệ.";
    } elseif (!preg_match('/^\d{10}$/', $phonenumber)) {
        $errorMsg = "Số điện thoại phải gồm 10 chữ số.";
    } else {
        $sql = "UPDATE user SET full_name = ?, phone_number = ?, address = ?, email = ?";
        $sql .= " WHERE user_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $fullname, $phonenumber, $address, $email, $userId);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['fullname'] = $fullname;
            $_SESSION['phonenumber'] = $phonenumber;
            $_SESSION['address'] = $address;
            $_SESSION['email'] = $email;
            $successMsg = "Cập nhật thông tin thành công!";

            $result = mysqli_query($conn, "SELECT * FROM user WHERE user_id = $userId LIMIT 1");
            $user = mysqli_fetch_assoc($result);
        } else {
            $errorMsg = "Lỗi khi cập nhật thông tin.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold mb-6 text-center">Tài khoản của bạn</h2>
        <?php if (!empty($successMsg)): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= $successMsg ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= $errorMsg ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <?php if (isset($successMsg)): ?>
                <p class="text-green-600 mb-4"><?= $successMsg ?></p>
            <?php elseif (isset($errorMsg)): ?>
                <p class="text-red-600 mb-4"><?= $errorMsg ?></p>
            <?php endif; ?>

            <ul class="flex space-x-4 border-b mb-6">
                <li><a href="#info" class="font-medium py-2 px-4 text-blue-600 border-b-2 border-blue-600"onclick="toggleTab('info')">Thông tin cá nhân</a></li>
                <li><a href="#orders" class="font-medium py-2 px-4 hover:text-blue-600" onclick="toggleTab('orders')">Đơn hàng đã đặt</a></li>
            </ul>

            <div id="info" class="tab">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Họ và tên:</label>
                        <input type="text" name="fullname" class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['full_name']) ?>">
                    </div>
                    <div>
                        <label class="block font-medium">Số điện thoại:</label>
                        <input type="text" name="phonenumber" class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['phone_number']) ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium">Địa chỉ:</label>
                        <input type="text" name="address" class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['address']) ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium">Email:</label>
                        <input type="email" name="email" class="w-full border px-3 py-2 rounded" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Cập nhật
                        </button>
                        <a href="doi_pass.php" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">
                            Đổi mật khẩu
                        </a>
                    </div>
                </form>
            </div>

            <div id="orders" class="tab hidden">
                <h3 class="text-xl font-semibold mb-4">Lịch sử đơn hàng</h3>
                <?php
                $userId = $_SESSION['user_id'];
                $orders = mysqli_query($conn, "SELECT * FROM `order` WHERE user_id = $userId ORDER BY order_date DESC");
                if (mysqli_num_rows($orders) > 0): ?>
                    <table class="w-full text-left table-auto border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2">Mã đơn</th>
                                <th class="px-4 py-2">Ngày đặt</th>
                                <th class="px-4 py-2">Tổng tiền</th>
                                <th class="px-4 py-2">Trạng thái</th>
                                <th class="px-4 py-2">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($orders)): 
                                // Tính toán giảm giá như trong code quản lý đơn hàng
                                $total_amount = $row['total_amount'];
                                if (!empty($row['code']) && isset($vouchers[$row['code']])) {
                                    $discount_percent = $vouchers[$row['code']];
                                    $total_amount = $total_amount * (1 - $discount_percent / 100);
                                }
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-2">#<?= $row['order_id'] ?></td>
                                <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
                                <td class="px-4 py-2">
                                    <?php if (!empty($row['code']) && isset($vouchers[$row['code']])): ?>
                                        <span class="line-through text-gray-500 text-sm"><?= number_format($row['total_amount'], 0, ',', '.') ?>₫</span><br>
                                        <span class="text-red-600 font-semibold"><?= number_format($total_amount, 0, ',', '.') ?>₫</span>
                                        <span class="text-xs text-green-600">(Giảm <?= $vouchers[$row['code']] ?>%)</span>
                                    <?php else: ?>
                                        <?= number_format($total_amount, 0, ',', '.') ?>₫
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2"><?= $row['status'] ?></td>
                                <td class="px-4 py-2">
                                    <button onclick="openOrderPopup(<?= $row['order_id'] ?>)" class="text-blue-600 underline">Xem chi tiết</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <div id="order-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white w-full max-w-3xl p-6 rounded shadow-lg overflow-y-auto max-h-[80vh] relative">
                            <button onclick="closeOrderPopup()" class="absolute top-2 right-2 text-gray-600 text-xl">&times;</button>
                            <h3 class="text-xl font-semibold mb-4">Chi tiết đơn hàng</h3>
                            <div id="order-popup-content"></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Bạn chưa có đơn hàng nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script>
    const orderData = <?= json_encode($orderDetails, JSON_UNESCAPED_UNICODE) ?>;

function openOrderPopup(orderId) {
    const items = orderData[orderId] || [];
    const container = document.getElementById('order-popup-content');
    if (items.length === 0) {
        container.innerHTML = "<p>Không có sản phẩm nào trong đơn hàng này.</p>";
    } else {
        container.innerHTML = `
            <table class="w-full text-left table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Sản phẩm</th>
                        <th class="px-4 py-2">Ảnh</th>
                        <th class="px-4 py-2">Số lượng</th>
                        <th class="px-4 py-2">Đơn giá</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map(item => `
                        <tr class="border-t">
                            <td class="px-4 py-2">${item.product_name}</td>
                            <td class="px-4 py-2"><img src="${item.picture}" alt="${item.product_name}" class="h-16"></td>
                            <td class="px-4 py-2">${item.quantity}</td>
                            <td class="px-4 py-2">${Number(item.unit_price).toLocaleString()}₫</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }
    document.getElementById('order-popup').classList.remove('hidden');
}

function closeOrderPopup() {
    document.getElementById('order-popup').classList.add('hidden');
}
</script>

<script>
    function toggleTab(tabId) {
        document.getElementById('info').classList.add('hidden');
        document.getElementById('orders').classList.add('hidden');
        document.getElementById(tabId).classList.remove('hidden');
    }
</script>
</body>
</html>
<?php
$content = ob_get_clean();
include 'layout.php';
?>

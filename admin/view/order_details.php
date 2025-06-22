<?php
ob_start();
include 'db.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];
    $status = null;

    if ($action === 'complete') {
        $status = 'Completed';
    } elseif ($action === 'cancel') {
        $status = 'Cancelled';
    } elseif ($action === 'undo') {
        $status = 'Pending';
    }

    if ($status && in_array($status, ['Pending', 'Completed', 'Cancelled'])) {
        $stmt = $conn->prepare("UPDATE `order` SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $stmt->close();
        header("Location: order_details.php?order_id=$order_id&message=Trạng thái đơn hàng đã được cập nhật");
        exit();
    }
}

// Get order_id from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order information
$sql_order = "SELECT o.order_id, o.order_date, o.total_amount, o.status, u.user_name 
              FROM `order` o 
              LEFT JOIN `user` u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
$stmt_order = $conn->prepare($sql_order);
if (!$stmt_order) {
    die("Lỗi prepare: " . $conn->error);
}
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order = $order_result->fetch_assoc();
$stmt_order->close();

if (!$order) {
    echo "<div class='mb-4 p-4 rounded-lg bg-red-100 text-red-800'>Đơn hàng không tồn tại.</div>";
    $content = ob_get_clean();
    require_once 'layout.php';
    exit();
}
?>

<!-- Display Success/Error Message -->
<?php if (isset($_GET['message'])): ?>
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Chi tiết Đơn hàng #<?php echo htmlspecialchars($order['order_id']); ?></h1>

<!-- Order Information -->
<div class="mb-6 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Thông tin Đơn hàng</h2>
    <p><strong>ID Đơn hàng:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
    <p><strong>Tên Người dùng:</strong> <?php echo htmlspecialchars($order['user_name'] ?? 'Không xác định'); ?></p>
    <p><strong>Ngày đặt hàng:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
    <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount'], 2); ?> VND</p>
    <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    <div class="mt-4 flex space-x-4">
        <?php if ($order['status'] == 'Pending'): ?>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                <input type="hidden" name="action" value="complete">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Xác nhận hoàn tất</button>
            </form>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                <input type="hidden" name="action" value="cancel">
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">Hủy đơn hàng</button>
            </form>
        <?php elseif ($order['status'] == 'Cancelled'): ?>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                <input type="hidden" name="action" value="undo">
                <button type="submit" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600" onclick="return confirm('Bạn có chắc muốn hoàn tác hủy đơn hàng này?')">Hoàn tác</button>
            </form>
        <?php endif; ?>
        <a href="order_invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">In hóa đơn</a>
    </div>
</div>

<!-- Order Details Table -->
<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-semibold mb-4 p-6">Chi tiết Sản phẩm</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $sql_details = "SELECT od.order_detail_id, od.quantity, od.unit_price, od.product_id, p.product_name 
                           FROM orderdetail od 
                           JOIN product p ON od.product_id = p.product_id 
                           WHERE od.order_id = ?";
            $stmt_details = $conn->prepare($sql_details);
            if (!$stmt_details) {
                die("Lỗi prepare: " . $conn->error);
            }
            $stmt_details->bind_param("i", $order_id);
            $stmt_details->execute();
            $result_details = $stmt_details->get_result();

            if ($result_details && $result_details->num_rows > 0) {
                while ($row = $result_details->fetch_assoc()) {
                    $total_price = $row['quantity'] * $row['unit_price'];
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['product_name']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . number_format($row['unit_price'], 2) . " VND</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . number_format($total_price, 2) . " VND</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>Không có chi tiết sản phẩm.</td></tr>";
            }

            $stmt_details->close();
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="orders.php" class="inline-block bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Quay lại</a>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
<?php
ob_start();
include 'db.php';

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
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: orders.php?message=Trạng thái đơn hàng đã được cập nhật");
            exit();
        } else {
            $stmt->close();
            header("Location: orders.php?message=Lỗi khi cập nhật trạng thái");
            exit();
        }
    }
}
?>

<?php if (isset($_GET['message'])): ?>
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Quản lý Đơn hàng</h1>

<div class="mb-6">
    <form method="GET" class="flex items-center">
        <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Tìm kiếm theo ID đơn hàng hoặc tên người dùng..." class="w-full max-w-md p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="ml-2 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Tìm</button>
    </form>
</div>

<!-- Order Table -->
<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Đơn hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Người dùng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            // Lấy danh sách voucher
            $vouchers = [];
            $stmt = $conn->prepare("SELECT code, discount_percent FROM voucher");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $vouchers[$row['code']] = $row['discount_percent'];
                }
                $stmt->close();
            } else {
                echo "<tr><td colspan='6' class='px-6 py-4 text-center text-red-500'>Lỗi khi lấy danh sách voucher.</td></tr>";
            }

            // Truy vấn danh sách đơn hàng
            $sql = "SELECT o.order_id, o.user_id, u.user_name, o.order_date, o.total_amount, o.status, o.code 
                    FROM `order` o 
                    LEFT JOIN `user` u ON o.user_id = u.user_id";
            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

            if (!empty($search_term)) {
                $sql .= " WHERE o.order_id = ? OR u.user_name LIKE ?";
                $search_param_id = $search_term;
                $search_param_name = "%" . $search_term . "%";
            }

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Lỗi prepare: " . $conn->error);
            }

            if (!empty($search_term)) {
                $stmt->bind_param("is", $search_param_id, $search_param_name);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo $row['code'];
                    $total_amount = $row['total_amount'];
                    if (!empty($row['code']) && isset($vouchers[$row['code']])) {
                        $discount_percent = $vouchers[$row['code']];
                        $total_amount = $total_amount * (1 - $discount_percent / 100);
                    }

                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['order_id']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['user_name'] ?? 'Không xác định') . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . number_format($total_amount, 2) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap flex items-center space-x-2'>";
                    echo "<a href='order_details.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='inline-block bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 text-sm'>Xem chi tiết</a>";
                    if ($row['status'] == 'Pending') {
                        echo "<form method='POST' class='inline-block'>
                                <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                <input type='hidden' name='action' value='complete'>
                                <button type='submit' class='inline-block bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600 text-sm'>Xác nhận hoàn tất</button>
                              </form>";
                        echo "<form method='POST' class='inline-block'>
                                <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                <input type='hidden' name='action' value='cancel'>
                                <button type='submit' class='inline-block bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-sm' onclick='return confirm(\"Bạn có chắc muốn hủy đơn hàng này?\")'>Hủy đơn hàng</button>
                              </form>";
                    } elseif ($row['status'] == 'Cancelled') {
                        echo "<form method='POST' class='inline-block'>
                                <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                                <input type='hidden' name='action' value='undo'>
                                <button type='submit' class='inline-block bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 text-sm' onclick='return confirm(\"Bạn có chắc muốn hoàn tác hủy đơn hàng này?\")'>Hoàn tác</button>
                              </form>";
                    }
                    echo "<a href='order_invoice.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='inline-block bg-gray-500 text-white py-1 px-3 rounded hover:bg-gray-600 text-sm'>In hóa đơn</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>Không có đơn hàng nào.</td></tr>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
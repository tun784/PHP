<?php
ob_start();

include 'db.php';

if (!$conn) {
    die("<p class='text-red-500 text-center'>Lỗi kết nối cơ sở dữ liệu: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 text-center">
        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Quản Lý Khách Hàng</h1>

<div class="mb-6 flex justify-center">
    <form method="GET" class="flex items-center w-full max-w-md">
        <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Tìm kiếm..." class="flex-grow p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="ml-2 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Tìm</button>
    </form>
</div>

<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Đăng Nhập</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Khách Hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Điện Thoại</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa Chỉ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành Động</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $sql = "SELECT * FROM user WHERE role = 'customer'";
            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

            if (!empty($search_term)) {
                $sql .= " AND (user_name LIKE ? OR full_name LIKE ?)";
                $search_param = "%" . $search_term . "%";
            }

            $stmt = $conn->prepare($sql);
            if (!empty($search_term)) {
                $stmt->bind_param("ss", $search_param, $search_param);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['user_id']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['user_name']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['phone_number']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['address']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>
                            <a href='customer_delete.php?user_id=" . htmlspecialchars($row['user_id']) . "' class='inline-block bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-sm' onclick='return confirm(\"Bạn có chắc muốn xóa khách hàng này?\")'>Xóa</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>Không tìm thấy khách hàng nào.</td></tr>";
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
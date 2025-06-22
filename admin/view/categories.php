<?php
ob_start();
?>

<?php if (isset($_GET['message'])): ?>
    <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Quản lý Loại sản phẩm</h1>

<div class="mb-6">
    <form method="GET" class="flex items-center">
        <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Tìm kiếm..." class="w-full max-w-md p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="ml-2 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Tìm</button>
    </form>
</div>

<div class="mt-6">
    <a href="category_add.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Thêm Loại sản phẩm</a>
</div>

<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Loại sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            include 'db.php';

            $sql = "SELECT * FROM category";
            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

            if (!empty($search_term)) {
                $sql .= " WHERE category_name LIKE ?";
                $search_param = "%" . $search_term . "%";
            }

            $result = $conn->prepare($sql);
            if (!empty($search_term)) {
                $result->bind_param("s", $search_param);
            }
            $result->execute();
            $result = $result->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['category_id']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['category_name']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>
                            <a href='category_edit.php?category_id=" . htmlspecialchars($row['category_id']) . "' class='inline-block bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600 text-sm'>Sửa</a>
                            <a href='category_delete.php?category_id=" . htmlspecialchars($row['category_id']) . "' class='inline-block bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-sm ml-2' onclick='return confirm(\"Bạn có chắc muốn xóa loại sản phẩm này?\")'>Xóa</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>Không có loại sản phẩm nào.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
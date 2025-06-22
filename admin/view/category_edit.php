<?php
ob_start();

include 'db.php';

if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    echo "<p class='text-red-500 text-center'>ID loại sản phẩm không hợp lệ.</p>";
    echo isset($_GET['category_id']) ? "true" : "false";
    echo is_numeric($_GET['category_id']) ? "ID" : "false";
    $content = ob_get_clean();
    require_once 'layout.php';
    exit();
}

$category_id = (int)$_GET['category_id'];

$stmt = $conn->prepare("SELECT * FROM category WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-red-500 text-center'>Loại sản phẩm không tồn tại.</p>";
    $stmt->close();
    $conn->close();
    $content = ob_get_clean();
    require_once 'layout.php';
    exit();
}

$category = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name'] ?? '');

    if (empty($category_name)) {
        echo "<p class='text-red-500 text-center'>Vui lòng điền đầy đủ thông tin.</p>";
    }
    else {
        $stmt = $conn->prepare("UPDATE category SET category_name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $category_name, $category_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: categories.php");
            exit();
        } else {
            echo "<p class='text-red-500 text-center'>Lỗi khi cập nhật loại sản phẩm: " . htmlspecialchars($conn->error) . "</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Chỉnh sửa Loại sản phẩm</h1>

<div class="flex justify-center px-2 sm:px-4">
    <div class="bg-white shadow-md rounded-lg p-8 w-full">
        <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('Bạn có chắc muốn lưu thông tin này?');">
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700 text-center mb-2">Tên Loại sản phẩm</label>
                <input type="text" name="category_name" id="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="categories.php" class="bg-gray-500 text-white py-2 px-6 rounded hover:bg-gray-600 text-lg">Hủy</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 text-lg">Lưu Thay đổi</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
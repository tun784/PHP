<?php
ob_start();

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
include BASE_PATH . 'Demo/db.php';

// Check if product ID is provided
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    echo "<p class='text-red-500 text-center'>ID sản phẩm không hợp lệ.</p>";
    $content = ob_get_clean();
    require_once BASE_PATH . 'Demo/layout.php';
    exit();
}

$product_id = (int)$_GET['product_id'];

// Fetch product details and categories
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-red-500 text-center'>Sản phẩm không tồn tại.</p>";
    $stmt->close();
    $conn->close();
    $content = ob_get_clean();
    require_once BASE_PATH . 'Demo/layout.php';
    exit();
}

$product = $result->fetch_assoc();

$categories = [];
$stmt_categories = $conn->prepare("SELECT category_id, category_name FROM category");
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();
if ($result_categories) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}
$stmt_categories->close();

// Handle form submission to update product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    // Fetch category name for the directory
    $category_name = '';
    if ($category_id > 0) {
        $stmt = $conn->prepare("SELECT category_name FROM category WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $category = $result->fetch_assoc();
            $category_name = $category['category_name'];
        }
        $stmt->close();
    }

    // Basic validation
    if (empty($product_name) || empty($price) || $category_id == 0) {
        echo "<p class='text-red-500 text-center'>Vui lòng điền đầy đủ thông tin.</p>";
    } elseif (!is_numeric($price) || $price <= 0) {
        echo "<p class='text-red-500 text-center'>Giá phải là số dương.</p>";
    } else {
        $image_path = $product['picture']; // Default to current image

        // Handle file upload if a new image is provided
        if (!empty($_FILES['picture']['name'])) {
            $target_dir = BASE_PATH . 'Demo/image/' . $category_name . '/';
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    echo "<p class='text-red-500 text-center'>Lỗi khi tạo thư mục hình ảnh.</p>";
                }
            }

            $image_name = basename($_FILES['picture']['name']);
            $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($image_extension, $allowed_extensions)) {
                echo "<p class='text-red-500 text-center'>Chỉ cho phép các định dạng hình ảnh: jpg, jpeg, png, gif.</p>";
            } else {
                $unique_image_name = uniqid() . "_" . $image_name; // Add unique prefix
                $target_file = $target_dir . $unique_image_name;
                $image_path = '/Demo/image/' . $category_name . '/' . $unique_image_name;

                if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
                    // Delete the old image if it exists and different
                    $old_file = BASE_PATH . ltrim($product['picture'], '/'); // Convert web path to filesystem path
                    if (file_exists($old_file) && $old_file !== $target_file) {
                        unlink($old_file);
                    }
                } else {
                    echo "<p class='text-red-500 text-center'>Lỗi khi tải lên hình ảnh: " . error_get_last()['message'] . ". Vui lòng thử lại.</p>";
                    $image_path = $product['picture']; // Revert to old image if upload fails
                }
            }
        }

        // Update product in the database
        $stmt = $conn->prepare("UPDATE product SET product_name = ?, price = ?, picture = ?, category_id = ? WHERE product_id = ?");
        $stmt->bind_param("sdsii", $product_name, $price, $image_path, $category_id, $product_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: products.php?message=" . urlencode("Cập nhật sản phẩm thành công"));
            exit();
        } else {
            echo "<p class='text-red-500 text-center'>Lỗi khi cập nhật sản phẩm: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Chỉnh sửa Sản phẩm</h1>

<!-- Edit Product Form -->
<div class="flex justify-center px-2 sm:px-4">
    <div class="bg-white shadow-md rounded-lg p-8 w-full">
        <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('Bạn có chắc muốn lưu thông tin này?');">
            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-700 text-center mb-2">Tên Sản phẩm</label>
                <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 text-center mb-2">Giá (VNĐ)</label>
                <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required step="0.01">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 text-center mb-2">Loại Quạt</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required>
                    <option value="">Chọn loại quạt</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>" <?php echo ($product['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 text-center mb-2">Hình ảnh Sản phẩm (Để trống nếu không thay đổi)</label>
                <div class="relative">
                    <input type="file" name="picture" id="picture" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <button type="button" onclick="document.getElementById('picture').click()" class="mt-1 block w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 text-lg">Tải hình ảnh lên</button>
                </div>
                <div id="imagePreview" class="mt-4 text-center">
                    <img id="preview" src="<?php echo htmlspecialchars($product['picture']); ?>" alt="Hình ảnh hiện tại" class="max-w-xs mx-auto" style="display: block;">
                </div>
                <p class="text-sm text-gray-500 mt-1 text-center">Hình ảnh hiện tại: <a href="<?php echo htmlspecialchars($product['picture']); ?>" target="_blank" class="text-blue-500">Xem</a></p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="products.php" class="bg-gray-500 text-white py-2 px-6 rounded hover:bg-gray-600 text-lg">Hủy</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 text-lg">Lưu Thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
        previewContainer.style.display = 'block';
    } else {
        preview.src = "<?php echo htmlspecialchars($product['picture']); ?>"; // Revert to current image
        preview.style.display = 'block';
        previewContainer.style.display = 'block';
    }
}
</script>

<?php
$content = ob_get_clean(); // Capture the content and clean the buffer
require_once 'layout.php'; // Include the layout file
?>
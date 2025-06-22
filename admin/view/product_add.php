<?php
ob_start(); // Start output buffering to capture the content

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
include BASE_PATH . 'Demo/db.php'; // Adjust based on project structure

// Fetch categories for the dropdown
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

// Handle form submission to add product
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
    if (empty($product_name) || empty($price) || $category_id == 0 || empty($_FILES['picture']['name'])) {
        echo "<p class='text-red-500 text-center'>Vui lòng điền đầy đủ thông tin, bao gồm hình ảnh.</p>";
    } elseif (!is_numeric($price) || $price <= 0) {
        echo "<p class='text-red-500 text-center'>Giá phải là số dương.</p>";
    } else {
        // Handle file upload
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
                // Insert product into database
                $stmt = $conn->prepare("INSERT INTO product (product_name, price, picture, category_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdsi", $product_name, $price, $image_path, $category_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    $conn->close();
                    header("Location: products.php?message=" . urlencode("Thêm sản phẩm thành công"));
                    exit();
                } else {
                    echo "<p class='text-red-500 text-center'>Lỗi khi thêm sản phẩm: " . $conn->error . "</p>";
                    unlink($target_file); // Clean up uploaded file on failure
                }
                $stmt->close();
            } else {
                echo "<p class='text-red-500 text-center'>Lỗi khi tải lên hình ảnh: " . error_get_last()['message'] . ". Vui lòng thử lại.</p>";
            }
        }
    }
}

$conn->close();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Thêm Sản phẩm</h1>

<!-- Add Product Form -->
<div class="flex justify-center px-2 sm:px-4">
    <div class="bg-white shadow-md rounded-lg p-8 w-full">
        <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('Bạn có chắc muốn thêm sản phẩm này?');">
            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-700 text-center mb-2">Tên Sản phẩm</label>
                <input type="text" name="product_name" id="product_name" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 text-center mb-2">Giá (VNĐ)</label>
                <input type="number" name="price" id="price" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required step="0.01">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 text-center mb-2">Loại Quạt</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full border border-gray-300 rounded-md p-4 focus:ring-blue-500 focus:border-blue-500 text-lg" required>
                    <option value="">Chọn loại quạt</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 text-center mb-2">Hình ảnh Sản phẩm</label>
                <div class="relative">
                    <input type="file" name="picture" id="picture" accept="image/*" class="hidden" required onchange="previewImage(event)">
                    <button type="button" onclick="document.getElementById('picture').click()" class="mt-1 block w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 text-lg">Tải hình ảnh lên</button>
                </div>
                <div id="imagePreview" class="mt-4 text-center">
                    <img id="preview" class="max-w-xs mx-auto" style="display: none;">
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="products.php" class="bg-gray-500 text-white py-2 px-6 rounded hover:bg-gray-600 text-lg">Hủy</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 text-lg">Thêm Sản phẩm</button>
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
        preview.style.display = 'none';
        previewContainer.style.display = 'none';
    }
}
</script>

<?php
$content = ob_get_clean(); // Capture the content and clean the buffer
require_once 'layout.php'; // Include the layout file
?>
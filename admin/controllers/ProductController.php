<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
require_once BASE_PATH . 'Demo/admin/models/ProductModel.php';

class ProductController {
    private $model;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->model = new ProductModel($conn);
    }

    public function edit($product_id) {
        if (!isset($product_id) || !is_numeric($product_id)) {
            echo "<p class='text-red-500 text-center'>ID sản phẩm không hợp lệ.</p>";
            $this->renderLayout();
            return;
        }

        $product_id = (int)$product_id;
        $product = $this->model->getProductById($product_id);
        if (!$product) {
            echo "<p class='text-red-500 text-center'>Sản phẩm không tồn tại (ID: $product_id).</p>";
            $this->renderLayout();
            return;
        }

        $categories = $this->model->getCategories();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $product_name = trim($_POST['product_name'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);

            $category_name = '';
            if ($category_id > 0) {
                foreach ($categories as $category) {
                    if ($category['category_id'] == $category_id) {
                        $category_name = $category['category_name'];
                        break;
                    }
                }
            }

            if (empty($product_name) || empty($price) || $category_id == 0) {
                echo "<p class='text-red-500 text-center'>Vui lòng điền đầy đủ thông tin.</p>";
            } elseif (!is_numeric($price) || $price <= 0) {
                echo "<p class='text-red-500 text-center'>Giá phải là số dương.</p>";
            } else {
                $image_path = $product['picture']; // Default to current image

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
                        $unique_image_name = uniqid() . "_" . $image_name;
                        $target_file = $target_dir . $unique_image_name;
                        $image_path = '/Demo/image/' . $category_name . '/' . $unique_image_name;

                        if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
                            $old_file = BASE_PATH . ltrim($product['picture'], '/');
                            if (file_exists($old_file) && $old_file !== $target_file) {
                                unlink($old_file);
                            }
                        } else {
                            echo "<p class='text-red-500 text-center'>Lỗi khi tải lên hình ảnh: " . error_get_last()['message'] . ". Vui lòng thử lại.</p>";
                            $image_path = $product['picture'];
                        }
                    }
                }

                if (empty($this->conn->error)) {
                    if ($this->model->updateProduct($product_id, $product_name, $price, $image_path, $category_id)) {
                        header("Location: products.php?message=" . urlencode("Cập nhật sản phẩm thành công"));
                        exit();
                    } else {
                        echo "<p class='text-red-500 text-center'>Lỗi khi cập nhật sản phẩm: " . $this->conn->error . "</p>";
                    }
                }
            }
        }

        $view_data = [
            'product' => $product,
            'categories' => $categories
        ];
        $this->renderView('admin/view/product_edit.php', $view_data);
    }

    private function renderView($view, $data) {
        ob_start();
        extract($data);
        require_once $view;
        $content = ob_get_clean();
        $this->renderLayout($content);
    }

    private function renderLayout($content = '') {
        require_once 'layout.php';
    }
}

// Initialize and run
include BASE_PATH . 'Demo/db.php';
$controller = new ProductController($conn);
if (isset($_GET['product_id'])) {
    $controller->edit($_GET['product_id']);
}
?>
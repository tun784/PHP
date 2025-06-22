<?php
class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getProductById($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        return $product ?: null;
    }

    public function getCategories() {
        $categories = [];
        $stmt = $this->conn->prepare("SELECT category_id, category_name FROM category");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        $stmt->close();
        return $categories;
    }

    public function updateProduct($product_id, $product_name, $price, $image_path, $category_id) {
        $stmt = $this->conn->prepare("UPDATE product SET product_name = ?, price = ?, picture = ?, category_id = ? WHERE product_id = ?");
        $stmt->bind_param("sdsii", $product_name, $price, $image_path, $category_id, $product_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>
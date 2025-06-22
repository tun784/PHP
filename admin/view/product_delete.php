<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: products.php?message=" . urlencode("ID sản phẩm không hợp lệ."));
    exit();
}

$product_id = (int)$_GET['product_id'];

$stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $message = "Xóa sản phẩm thành công!";
} else {
    $message = "Lỗi khi xóa sản phẩm: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: products.php?message=" . urlencode($message));
exit();
?>
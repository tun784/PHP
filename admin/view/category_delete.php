<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include 'db.php';

if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    header("Location: categories.php?message=" . urlencode("ID loại sản phẩm không hợp lệ."));
    exit();
}

$category_id = (int)$_GET['category_id'];

$stmt = $conn->prepare("DELETE FROM category WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    $message = "Xóa loại sản phẩm thành công!";
} else {
    $message = "Lỗi khi xóa loại sản phẩm: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: categories.php?message=" . urlencode($message));
exit();
?>
<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: customers.php?message=" . urlencode("ID khách hàng không hợp lệ."));
    exit();
}

$user_id = (int)$_GET['user_id'];

$stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $message = "Xóa khách hàng thành công!";
} else {
    $message = "Lỗi khi xóa khách hàng: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: customers.php?message=" . urlencode($message));
exit();
?>
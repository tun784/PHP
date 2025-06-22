<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý - JustFans Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="..\css\style.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <div class="sidebar text-white">
            <a href="index.php" class="flex items-center justify-center space-x-2 mb-8 px-4">
                <img src="..\logo.png" alt="FanShop Logo" class="h-10 w-auto">
                <span class="text-2xl font-extrabold text-red-500">JUSTFANS</span>
            </a>
            <ul class="space-y-4">
                <li><a href="categories.php" class="block py-2 px-4 hover:bg-gray-700">Quản lý Loại sản phẩm</a></li>
                <li><a href="products.php" class="block py-2 px-4 hover:bg-gray-700">Quản lý Sản phẩm</a></li>
                <li><a href="customers.php" class="block py-2 px-4 hover:bg-gray-700">Quản lý Khách hàng</a></li>
                <li><a href="orders.php" class="block py-2 px-4 hover:bg-gray-700">Quản lý Đơn hàng</a></li>
                <li><a href="revenue.php" class="block py-2 px-4 hover:bg-gray-700">Quản lý Doanh thu</a></li>
                <li><a href="\Demo\logout.php" class="block py-2 px-4 hover:bg-gray-700">Đăng xuất</a></li>
            </ul>
        </div>

        <div class="content">
            <?php echo $content; ?>
        </div>
    </div>
</body>
</html>
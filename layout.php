<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JustFans - Quạt Máy Chất Lượng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <header class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2">
                <img src="logo.png" alt="FanShop Logo" class="h-10 w-auto">
                <span class="text-3xl font-extrabold text-red-theme">JUSTFANS</span>
            </a>
            <div class="flex items-center space-x-6">
                <form action="index.php" method="GET" class="relative">
                    <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Tìm kiếm quạt..." class="search-box pl-10 pr-4 w-96 py-2 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-theme" required>
                    <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-red-theme font-medium">Trang chủ</a>
                    <a href="products.php" class="text-gray-700 hover:text-red-theme font-medium">Sản phẩm</a>
                    <a href="about.php" class="text-gray-700 hover:text-red-theme font-medium">Giới thiệu</a>
                    <a href="contact.php" class="text-gray-700 hover:text-red-theme font-medium">Liên hệ</a>
                </nav>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <a href="user_profile.php" class="text-gray-700 font-semibold hover:text-red-theme">
                            <i class="fas fa-user text-lg mr-1"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <a href="logout.php" class="text-red-theme hover:text-red-600 ml-3 text-sm font-medium">(Đăng xuất)</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-red-theme">
                            <i class="fas fa-user text-lg"></i>
                        </a>
                    <?php endif; ?>
                    </div>
                    <a href="cart.php" class="text-gray-700 hover:text-red-theme">
                        <i class="fas fa-shopping-cart text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php echo $content; ?>
    </main>

    <footer class="bg-gray-800 text-white py-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">JustFans</h3>
                    <p>LÀM MÁT CUỘC SỐNG CỦA MỌI NHÀ</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Liên Kết Nhanh</h3>
                    <ul class="space-y-2">
                        <li><a href="products.php" class="hover:text-gray-200">Sản phẩm</a></li>
                        <li><a href="about.php" class="hover:text-gray-200">Giới thiệu</a></li>
                        <li><a href="contact.php" class="hover:text-gray-200">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Liên Hệ</h3>
                    <p>Email: JustFans@gmail.vn</p>
                    <p>Hotline: 0123 456 789</p>
                    <p>Địa chỉ: 140, Lê Trọng Tấn, TP. HCM</p>
                </div>
            </div>
            <div class="mt-8 text-center">
                <p class="text-sm">Ngày cập nhật: <?php echo date('d/m/Y'); ?></p>
                <p class="text-sm mt-2">Bản quyền thuộc về FanShop © 2025. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
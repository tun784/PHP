<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? null;

if ($_SESSION['user_id'] === null) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($product_id && in_array($action, ['increase', 'decrease'])) {
        if ($action === 'increase') {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ? AND quantity > 1");
        }

        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: cart.php");
    exit;
}

if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cart.php");
    exit;
}

if (isset($_GET['clear'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cart.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.product_name, p.price, p.picture
    FROM cart c
    JOIN product p ON c.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}
$stmt->close();
$conn->close();

ob_start();
?>

<main class="flex-1 pt-28 px-4">
    <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">🛒 Giỏ Hàng Của Bạn</h1>

    <?php if (!empty($cart_items)): ?>
        <div class="mb-6 text-right">
            <a href="cart.php?clear=1" class="inline-block bg-red-theme text-white px-4 py-2 rounded-lg hover:bg-red-theme-dark transition duration-300">Xóa Tất Cả</a>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($cart_items as $item): ?>
                <div class="flex items-center justify-between bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="flex items-center">
                        <img src="<?= htmlspecialchars($item['picture']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-24 h-24 object-cover rounded-lg mr-4" onerror="this.src='placeholder.png';">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h2>
                            <p class="text-gray-600">Giá: <?= number_format($item['price'], 0, ',', '.') ?> VNĐ</p>
                            <form method="post" action="cart.php" class="flex items-center space-x-2">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" name="action" value="decrease" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">−</button>
                                <span class="px-2"><?= $item['quantity'] ?></span>
                                <button type="submit" name="action" value="increase" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
                            </form>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-red-500 font-bold mb-2">Thành tiền: <?= number_format($item['subtotal'], 0, ',', '.') ?> VNĐ</p>
                        <a href="cart.php?remove=<?= $item['product_id'] ?>" class="text-red-500 hover:text-red-700 text-xl">×</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Tổng cộng: <span class="text-red-500"><?= number_format($total, 0, ',', '.') ?> VNĐ</span></h3>
            <div class="flex justify-between">
                <a href="index.php" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300">Tiếp tục mua sắm</a>
                <a href="checkout.php" class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300">Thanh toán</a>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center p-6 bg-white rounded-lg shadow-md">
            <p class="text-gray-600 text-lg">Giỏ hàng của bạn đang trống!</p>
            <a href="index.php" class="mt-4 inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</main>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

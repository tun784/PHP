<?php
session_start();
include 'db.php';

if (!isset($_GET['name'])) {
    die("Lỗi: Không có sản phẩm được chỉ định.");
}

$product_name = urldecode($_GET['name']);
$sql = "SELECT * FROM product WHERE product_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_name);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
    include 'db.php';
    $prod_id = intval($_POST['product_id']);
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $checkStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM product_review WHERE product_id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $prod_id, $user_id);
    $checkStmt->execute();
    $checkRes = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    if ($checkRes['cnt'] == 0) {
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment']);
        $stmt_r = $conn->prepare("INSERT INTO product_review (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt_r->bind_param("iiis", $prod_id, $user_id, $rating, $comment);
        $stmt_r->execute();
        $stmt_r->close();
        $redirectParam = "review_added=1";
    } else {
        $redirectParam = "already_reviewed=1";
    }
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?name=" . urlencode($product_name) . "&" . $redirectParam);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = session_id();
    }

    $session_id = $_SESSION['session_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE product_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($_SESSION['user_id'] === null) {
        header("Location: login.php");
        exit();
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $row['cart_id']);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $stmt->close();

    // Chuyển về trang giỏ hàng hoặc reload
    header("Location: " . $_SERVER['PHP_SELF'] . "?name=" . urlencode($product_name) . "&added=1");
    exit();
}


if ($result->num_rows === 0) {
    die("Lỗi: Sản phẩm không tồn tại.");
}

$product = $result->fetch_assoc();
$stmt->close();
$conn->close();

ob_start();
?>
<div id="toast-success"
    class="hidden fixed bottom-5 right-5 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transition-opacity duration-300">
    ✅ Sản phẩm đã được thêm vào giỏ hàng!
</div>

<main class="flex-1 pt-28 px-4">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-xl">
        <!-- Nút quay lại -->
        <div class="mb-6">
            <a href="index.php"
                class="inline-block text-white bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg shadow transition duration-200">
                ← Quay về Trang chủ
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <img src="<?php echo htmlspecialchars($product['picture']); ?>"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                class="w-full h-auto rounded-lg shadow-lg border border-gray-200">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800 mb-4">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h1>
                <p class="text-3xl text-red-500 font-bold mb-6">
                    <?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?>
                </p>

                <div class="bg-gray-100 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Mô tả sản phẩm:</h2>
                    <p class="text-gray-600">Mô tả sản phẩm đang được cập nhật. Vui lòng quay lại sau để biết thêm chi
                        tiết!</p>
                </div>
                <div class="mb-2">
                    <?php
                    // Fetch average rating and comments count
                    include 'db.php';
                    $revStmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM product_review WHERE product_id = ?");
                    $revStmt->bind_param("i", $product['product_id']);
                    $revStmt->execute();
                    $revRes = $revStmt->get_result()->fetch_assoc();
                    $avg = number_format($revRes['avg_rating'], 1);
                    $cnt = $revRes['cnt'];
                    ?>
                    <div class="flex items-center gap-2">
                        <span class="font-medium">Đánh giá trung bình:</span>
                        <span
                            class="text-yellow-500"><?php echo str_repeat('★', floor($avg)) . str_repeat('☆', 5 - floor($avg)); ?></span>
                        <span class="text-gray-800 ml-2"><?php echo $avg; ?>/5</span>
                        <span>(<?php echo $cnt; ?> nhận xét)</span>
                    </div>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                    <label for="quantity" class="block text-gray-700 font-medium">Số lượng:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1"
                        class="w-24 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-theme">

                    <button type="submit"
                        class="inline-block red-theme text-black text-lg font-semibold px-6 py-3 rounded-xl shadow hover:shadow-md transition duration-300">
                        🛒 Thêm vào giỏ hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
<div id="review-section" class="container mx-auto px-4 py-8">
    <?php
    include 'db.php';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $can_review = false;
    if ($user_id) {
        $chk = $conn->prepare("SELECT COUNT(*) as cnt FROM orderdetail od JOIN `order` o ON od.order_id = o.order_id WHERE o.user_id = ? AND od.product_id = ?");
        $chk->bind_param("ii", $user_id, $product['product_id']);
        $chk->execute();
        $cntRes = $chk->get_result()->fetch_assoc();
        $can_review = $cntRes['cnt'] > 0;
    }
    // Check if user already reviewed
    $has_reviewed = false;
    if ($user_id) {
        $chkRev = $conn->prepare("SELECT COUNT(*) as cnt FROM product_review WHERE product_id = ? AND user_id = ?");
        $chkRev->bind_param("ii", $product['product_id'], $user_id);
        $chkRev->execute();
        $has_review_res = $chkRev->get_result()->fetch_assoc();
        $has_reviewed = $has_review_res['cnt'] > 0;
        $chkRev->close();
    }
    // Fetch reviews
    $revStmt = $conn->prepare("SELECT pr.rating, pr.comment, pr.created_at, COALESCE(u.user_name, 'Ăn ba tô cơm') as user_name FROM product_review pr LEFT JOIN `user` u ON pr.user_id = u.user_id WHERE pr.product_id = ? ORDER BY pr.created_at DESC");
    $revStmt->bind_param("i", $product['product_id']);
    $revStmt->execute();
    $reviews = $revStmt->get_result();
    ?>
    <h2 class="text-2xl font-semibold mb-4">Nhận xét (<?php echo $reviews->num_rows; ?>)</h2>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($r = $reviews->fetch_assoc()): ?>
            <div class="border-b py-4">
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="text-yellow-500"><?php echo str_repeat("★", $r['rating']) . str_repeat("☆", 5 - $r['rating']); ?></span>
                    <span class="font-medium"><?php echo htmlspecialchars($r['user_name']); ?></span>
                    <span class="text-sm text-gray-600"><?php echo date("d/m/Y H:i", strtotime($r['created_at'])); ?></span>
                </div>
                <p class="text-gray-700"><?php echo htmlspecialchars($r['comment']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-600">Chưa có nhận xét nào.</p>
    <?php endif; ?>
    <?php
    // Ensure at least 2 reviews placeholders
    $countExist = $reviews->num_rows;
    for ($i = $countExist; $i < 2; $i++) {
        echo '<div class="border-b py-4">';
        echo '<div class="flex items-center gap-2 mb-1">';
        echo '<span class="text-yellow-300">' . str_repeat('☆', 5) . '</span>';
        echo '<span class="text-gray-500 ml-2">Chưa có nhận xét</span>';
        echo '</div>';
        echo '<p class="text-gray-400 italic">Hãy là người đầu tiên để lại bình luận!</p>';
        echo '</div>';
    }
    ?>
    <?php if ($user_id && $can_review && !$has_reviewed): ?>
        <div class="mt-6">
            <h3 class="text-xl font-semibold mb-2">Viết nhận xét của bạn</h3>
            <?php if (isset($_GET['review_added'])): ?>
                <p class="text-green-600 mb-2">Cảm ơn đánh giá!</p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <label>Đánh giá:
                    <select name="rating" class="border px-2 py-1">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo str_repeat("★", $i) . str_repeat("☆", 5 - $i); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </label>
                <label>Bình luận:
                    <textarea name="comment" rows="3" class="w-full border px-2 py-1"></textarea>
                </label>
                <button type="submit" name="review_submit" class="bg-blue-500 text-white px-4 py-2 rounded">Gửi nhận
                    xét</button>
            </form>
        </div>
    <?php elseif ($user_id && $has_reviewed): ?>
        <p class="text-gray-600 mt-4">Bạn đã đánh giá sản phẩm này rồi.</p>
    <?php elseif (!$user_id): ?>
        <p class="text-red-600 mt-4">Bạn cần <a href="login.php" class="underline text-blue-600">đăng nhập</a> để đánh giá.
        </p>
    <?php elseif (!$can_review): ?>
        <p class="text-gray-600 mt-4">Bạn chỉ có thể đánh giá sau khi mua sản phẩm này.</p>
    <?php endif; ?>
</div>
<script>
    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    window.addEventListener("DOMContentLoaded", () => {
        const added = getQueryParam("added");
        if (added === "1") {
            const toast = document.getElementById("toast-success");
            toast.classList.remove("hidden");

            // Ẩn toast sau 3 giây
            setTimeout(() => {
                toast.classList.add("opacity-0");
                setTimeout(() => toast.remove(), 300); // remove khỏi DOM
            }, 3000);
        }
    });
</script>


<?php
$content = ob_get_clean();
include 'layout.php';
?>
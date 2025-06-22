<?php
ob_start();
?>

<section class="banner">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-4">JUSTFANS - LÀM MÁT CUỘC SỐNG CỦA MỌI NHÀ</h1>
        <p class="text-lg md:text-2xl text-black mb-8 bg-blue-200 font-bold py-4 px-8 margin-center text-center">JUSTFANS - LÀM MÁT CUỘC SỐNG CỦA MỌI NHÀ</p>
        <a href="products.php" class="bg-red-theme text-white px-8 py-3 rounded-full font-semibold text-lg hover:bg-red-theme-dark">Khám Phá Ngay</a>
    </div>
</section>

<section class="container mx-auto px-4 py-5">
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">SẢN PHẨM NỔI BẬT CỦA CỬA HÀNG</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        include 'db.php';
        $products_per_page = 8;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $products_per_page;

        $products = [];
        $sql = "SELECT * FROM product";
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

        if (!empty($search_term)) {
            $sql .= " WHERE product_name LIKE ?";
            $search_param = "%" . $search_term . "%";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if (!empty($search_term)) {
            $stmt->bind_param("sii", $search_param, $products_per_page, $offset);
        } else {
            $stmt->bind_param("ii", $products_per_page, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            echo '<p class="text-center text-gray-500 col-span-full">Không có sản phẩm nào để hiển thị.</p>';
        }

        foreach ($products as $product) {
            echo '
            <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-[420px]">
                <img src="' . (empty($product['picture']) ? 'placeholder.png' : htmlspecialchars($product['picture'])) . '" alt="' . htmlspecialchars($product['product_name']) . '" class="w-full h-56 object-cover" onerror="this.src=\'placeholder.png\';">
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="text-lg font-semibold text-gray-800">' . htmlspecialchars($product['product_name']) . '</h3>
                    <p class="text-blue-theme font-bold mt-6">' . number_format($product['price'], 0, ',', '.') . ' VNĐ</p>
                    <div class="mt-auto pt-3">
                        <a href="product.php?name=' . urlencode($product['product_name']) . '" class="block w-full bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600 transition duration-300 font-medium shadow-md">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>';
        }

        $stmt->close();
        ?>
    </div>

    <!-- Pagination -->
    <?php
    $sql_count = "SELECT COUNT(*) as total FROM product";
    if (!empty($search_term)) {
        $sql_count .= " WHERE product_name LIKE ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("s", $search_param);
    } else {
        $result_count = $conn->query($sql_count);
    }
    if (!empty($search_term)) {
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
    }
    $total_products = $result_count->fetch_assoc()['total'];
    $total_pages = ceil($total_products / $products_per_page);

    if ($total_pages > 1) {
        echo '<div class="pagination mt-10 flex justify-center items-center space-x-2 bg-white text-black p-4 rounded-lg">';
        if ($current_page > 1) {
            echo '<a href="?page=1' . (!empty($search_term) ? '&search=' . urlencode($search_term) : '') . '" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">«</a>';
        }

        $window_size = 3;
        $start_page = max(1, $current_page - 1);
        $end_page = min($total_pages, $start_page + $window_size - 1);

        $start_page = max(1, $end_page - $window_size + 1);

        for ($i = $start_page; $i <= $end_page; $i++) {
            echo '<a href="?page=' . $i . (!empty($search_term) ? '&search=' . urlencode($search_term) : '') . '" class="px-4 py-2 ' . ($i == $current_page ? 'bg-red-theme' : 'bg-gray-700') . ' text-black rounded hover:bg-gray-600">' . $i . '</a>';
        }

        if ($current_page < $total_pages) {
            echo '<a href="?page=' . $total_pages . (!empty($search_term) ? '&search=' . urlencode($search_term) : '') . '" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">»</a>';
        }
        echo '</div>';
    }
    if (!empty($search_term)) $stmt_count->close();
    $conn->close();
    ?>
</section>

<div id="chat-bubble" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; cursor: pointer;">
    <img src="image/doro.jpg" alt="Chat" style="width: 60px; height: 60px; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
</div>

<div id="chat-window" style="display: none; position: fixed; bottom: 90px; right: 20px; width: 300px; background: #fff; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.2); z-index: 1000;">
    <div style="background: #000000; color: white; padding: 10px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        Open Channel JustFans
        <span id="chat-close" style="float: right; cursor: pointer;">&times;</span>
    </div>
    <div id="chat-messages" style="padding: 10px; max-height: 200px; overflow-y: auto;">
        <p style="font-weight: bold;">We are online<br><small>and ready to help!</small></p>
        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
            <div style="text-align: center;">
                <img src="image/businessman-in-suit-head-vector-icon.jpg" alt="Huy" style="width: 40px; height: 40px; border-radius: 50%;">
                <div style="font-size: 12px;">Huy</div>
            </div>
            <div style="text-align: center;">
                <img src="image/businessman-in-suit-head-vector-icon.jpg" alt="Doang" style="width: 40px; height: 40px; border-radius: 50%;">
                <div style="font-size: 12px;">Doanh</div>
            </div>
            <div style="text-align: center;">
                <img src="image/businessman-in-suit-head-vector-icon.jpg" alt="Khánh" style="width: 40px; height: 40px; border-radius: 50%;">
                <div style="font-size: 12px;">Khánh</div>
            </div>
        </div>
    </div>
    <div style="padding: 10px; border-top: 1px solid #eee;">
        <textarea id="chat-input" placeholder="Enter message..." style="width: 100%; height: 60px; border: 1px solid #ccc; border-radius: 4px; padding: 5px; resize: none;"></textarea>
        <button id="chat-send" style="margin-top: 5px; width: 100%; background: #000000; color: white; border: none; padding: 8px; border-radius: 4px;">Send</button>
    </div>
</div>

<script>
document.getElementById("chat-bubble").addEventListener("click", function() {
    var chatWindow = document.getElementById("chat-window");
    chatWindow.style.display = (chatWindow.style.display === "none") ? "block" : "none";
});

document.getElementById("chat-close").addEventListener("click", function() {
    document.getElementById("chat-window").style.display = "none";
});

document.getElementById("chat-send").addEventListener("click", function() {
    var input = document.getElementById("chat-input");
    var message = input.value.trim();
    if (message !== "") {
        var chatMessages = document.getElementById("chat-messages");

        // Thêm tin nhắn người dùng
        var userMsg = document.createElement("div");
        userMsg.style.margin = "5px 0";
        userMsg.style.textAlign = "right";
        userMsg.innerHTML = `<span style="background: #000; color: #fff; padding: 5px 10px; border-radius: 12px; display: inline-block;">${message}</span>`;
        chatMessages.appendChild(userMsg);

        chatMessages.scrollTop = chatMessages.scrollHeight;

        input.value = "";

        // Giả lập phản hồi hệ thống
        setTimeout(function() {
            var botMsg = document.createElement("div");
            botMsg.style.margin = "5px 0";
            botMsg.style.textAlign = "left";
            botMsg.innerHTML = `<span style="background: #eee; padding: 5px 10px; border-radius: 12px; display: inline-block;">Thanks for your message!</span>`;
            chatMessages.appendChild(botMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 500);
    }
});
</script>



<?php
$content = ob_get_clean();
include 'layout.php';
?>

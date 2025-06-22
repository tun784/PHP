<?php
ob_start();
?>

<section class="container mx-auto px-4 py-8" style="margin-top: 80px";>
    <h2 class="text-3xl font-bold mb-6 text-center">Bộ lọc sản phẩm</h2>
    <form action="products.php" method="GET" class="flex flex-wrap gap-4 justify-center">
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Loại quạt:</label>
            <select name="category_id" class="border border-gray-400 rounded px-3 py-2">
                <option value="">Tất cả</option>
                <?php
                include 'db.php';
                $category_names = [
                    'quat_tran' => 'Quạt trần',
                    'quat_dung' => 'Quạt đứng',
                    'quat_treo' => 'Quạt treo',
                    'quat_lung' => 'Quạt lửng',
                    'quat_ban' => 'Quạt bàn',
                    'quat_hop' => 'Quạt hộp',
                    'quat_cong_nghiep' => 'Quạt công nghiệp',
                    'quat_thong_gio' => 'Quạt thông gió',
                    'quat_hoi_nuoc' => 'Quạt hơi nước'
                ];
                $category_sql = "SELECT * FROM category";
                $category_result = $conn->query(query: $category_sql);
                if ($category_result->num_rows > 0) {
                    while ($cat = $category_result->fetch_assoc()) {
                        $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '';
                        $display_name = isset($category_names[$cat['category_name']]) ? $category_names[$cat['category_name']] : $cat['category_name'];
                        echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . $display_name . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Sắp xếp theo giá:</label>
            <select name="price_sort" class="border border-gray-400 rounded px-3 py-2">
                <option value="">Mặc định</option>
                <option value="asc" <?php if(isset($_GET['price_sort']) && $_GET['price_sort'] == 'asc') echo 'selected'; ?>>Giá thấp đến cao</option>
                <option value="desc" <?php if(isset($_GET['price_sort']) && $_GET['price_sort'] == 'desc') echo 'selected'; ?>>Giá cao đến thấp</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Lọc</button>
    </form>
</section>

<!-- DANH SÁCH SẢN PHẨM -->
<section class="container mx-auto px-4 pb-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        $products_per_page = 8;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $products_per_page;

        $sql = "SELECT * FROM product WHERE 1=1";

        if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
            $sql .= " AND category_id = " . intval($_GET['category_id']);
        }

        if (isset($_GET['price_sort']) && ($_GET['price_sort'] == 'asc' || $_GET['price_sort'] == 'desc')) {
            $sql .= " ORDER BY price " . ($_GET['price_sort'] == 'asc' ? 'ASC' : 'DESC');
        }

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if (!empty($_GET['category_id']) && $_GET['category_id'] !== '') {
            $stmt->bind_param("ii", $products_per_page, $offset);
        } else {
            $stmt->bind_param("ii", $products_per_page, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                echo '
                <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-[420px]">
                    <img src="' . (empty($product['picture']) ? 'placeholder.png' : htmlspecialchars($product['picture'])) . '" alt="' . htmlspecialchars($product['product_name']) . '" class="w-full h-56 object-cover" onerror="this.src=\'placeholder.png\';">
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-lg font-semibold text-gray-800">' . htmlspecialchars($product['product_name']) . '</h3>
                        <p class="text-blue-500 font-bold mt-6">' . number_format($product['price'], 0, ',', '.') . ' VNĐ</p>
                        <div class="mt-auto pt-3">
                            <a href="product.php?name=' . urlencode($product['product_name']) . '" class="block w-full bg-blue-500 text-white text-center py-2 rounded-lg hover:bg-blue-600 transition duration-300 font-medium shadow-md">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-gray-600 col-span-full text-center">Không tìm thấy sản phẩm nào phù hợp.</p>';
        }
        $stmt->close();
        ?>
    </div>

    <!-- Pagination -->
    <?php
    $sql_count = "SELECT COUNT(*) as total FROM product WHERE 1=1";
    if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
        $sql_count .= " AND category_id = " . intval($_GET['category_id']);
    }
    $result_count = $conn->query($sql_count);
    $total_products = $result_count->fetch_assoc()['total'];
    $total_pages = ceil($total_products / $products_per_page);

    if ($total_pages > 1) {
        echo '<div class="pagination mt-10 flex justify-center items-center space-x-2 bg-white text-black p-4 rounded-lg">';
        if ($current_page > 1) {
            echo '<a href="?page=1&category_id=' . (isset($_GET['category_id']) ? $_GET['category_id'] : '') . '&price_sort=' . (isset($_GET['price_sort']) ? $_GET['price_sort'] : '') . '" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">«</a>';
        }

        $window_size = 3;
        $start_page = max(1, $current_page - 1);
        $end_page = min($total_pages, $start_page + $window_size - 1);
        $start_page = max(1, $end_page - $window_size + 1);

        for ($i = $start_page; $i <= $end_page; $i++) {
            echo '<a href="?page=' . $i . '&category_id=' . (isset($_GET['category_id']) ? $_GET['category_id'] : '') . '&price_sort=' . (isset($_GET['price_sort']) ? $_GET['price_sort'] : '') . '" class="px-4 py-2 ' . ($i == $current_page ? 'bg-red-theme' : 'bg-gray-700') . ' text-black rounded hover:bg-gray-600">' . $i . '</a>';
        }

        if ($current_page < $total_pages) {
            echo '<a href="?page=' . $total_pages . '&category_id=' . (isset($_GET['category_id']) ? $_GET['category_id'] : '') . '&price_sort=' . (isset($_GET['price_sort']) ? $_GET['price_sort'] : '') . '" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600">»</a>';
        }
        echo '</div>';
    }
    $conn->close();
    ?>
</section>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
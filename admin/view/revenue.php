<?php
ob_start();
include 'db.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize filter values
$period = isset($_GET['period']) ? $_GET['period'] : 'day';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d', strtotime('2025-06-11 19:14:00 +07:00'));
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m', strtotime('2025-06-11 19:14:00 +07:00'));
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y', strtotime('2025-06-11 19:14:00 +07:00'));

// Initialize query parameters for table
$where_clause = "WHERE o.status = 'Completed'";
$params = [];
$types = '';

if ($period === 'day' && !empty($date)) {
    $where_clause .= " AND DATE(o.order_date) = ?";
    $params[] = $date;
    $types .= 's';
} elseif ($period === 'month' && !empty($month)) {
    $where_clause .= " AND DATE_FORMAT(o.order_date, '%Y-%m') = ?";
    $params[] = $month;
    $types .= 's';
} elseif ($period === 'year' && !empty($year)) {
    $where_clause .= " AND YEAR(o.order_date) = ?";
    $params[] = $year;
    $types .= 'i';
}

// Fetch summary data for table
$summary_sql = "SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue 
                FROM `order` o 
                $where_clause";
$stmt_summary = $conn->prepare($summary_sql);
if (!$stmt_summary) {
    die("Lỗi prepare (summary): " . $conn->error . " | Query: " . $summary_sql);
}
if (!empty($params)) {
    $stmt_summary->bind_param($types, ...$params);
}
$stmt_summary->execute();
$summary_result = $stmt_summary->get_result();
$summary = $summary_result->fetch_assoc();
$stmt_summary->close();

$total_revenue = $summary['total_revenue'] ?? 0;
$order_count = $summary['order_count'] ?? 0;
$avg_order_value = $order_count > 0 ? $total_revenue / $order_count : 0;

// Fetch order details for table
$orders_sql = "SELECT o.order_id, o.user_id, u.user_name, o.order_date, o.total_amount, o.status 
               FROM `order` o 
               LEFT JOIN `user` u ON o.user_id = u.user_id 
               $where_clause 
               ORDER BY o.order_date DESC";
$stmt_orders = $conn->prepare($orders_sql);
if (!$stmt_orders) {
    die("Lỗi prepare (orders): " . $conn->error . " | Query: " . $orders_sql);
}
if (!empty($params)) {
    $stmt_orders->bind_param($types, ...$params);
}
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();

// Fetch chart data based on filter period
$current_date = new DateTime('2025-06-11 19:14:00 +07:00');
$week_start = (clone $current_date)->modify('last Sunday')->format('Y-m-d');
$week_end = (clone $current_date)->modify('next Saturday')->format('Y-m-d');
$month_start = (clone $current_date)->modify('first day of this month')->format('Y-m-d');
$month_end = (clone $current_date)->modify('last day of this month')->format('Y-m-d');
$year_start = (clone $current_date)->modify('first day of January')->format('Y-m-d');
$year_end = (clone $current_date)->modify('last day of December')->format('Y-m-d');

// Weekly data
$weekly_data = array_fill(0, 7, 0);
$weekly_labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$sql = "SELECT DATE_FORMAT(order_date, '%w') as day_num, SUM(total_amount) as revenue 
        FROM `order` 
        WHERE status = 'Completed' AND order_date BETWEEN ? AND ? 
        GROUP BY day_num";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi prepare (weekly): " . $conn->error . " | Query: " . $sql);
}
$stmt->bind_param("ss", $week_start, $week_end);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $weekly_data[(int)$row['day_num']] = (float)($row['revenue'] ?? 0);
}
$stmt->close();

// Monthly data
$days_in_month = $current_date->format('t');
$monthly_data = array_fill(0, $days_in_month, 0);
$monthly_labels = range(1, $days_in_month);
$sql = "SELECT DAY(order_date) as day_num, SUM(total_amount) as revenue 
        FROM `order` 
        WHERE status = 'Completed' AND order_date BETWEEN ? AND ? 
        GROUP BY day_num";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi prepare (monthly): " . $conn->error . " | Query: " . $sql);
}
$stmt->bind_param("ss", $month_start, $month_end);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $monthly_data[(int)$row['day_num'] - 1] = (float)($row['revenue'] ?? 0);
}
$stmt->close();

// Yearly data
$yearly_data = array_fill(0, 12, 0);
$yearly_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$sql = "SELECT MONTH(order_date) as month_num, SUM(total_amount) as revenue 
        FROM `order` 
        WHERE status = 'Completed' AND order_date BETWEEN ? AND ? 
        GROUP BY month_num";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi prepare (yearly): " . $conn->error . " | Query: " . $sql);
}
$stmt->bind_param("ss", $year_start, $year_end);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $yearly_data[(int)$row['month_num'] - 1] = (float)($row['revenue'] ?? 0);
}
$stmt->close();
?>

<h1 class="text-3xl font-bold mb-6 text-gray-800">Thống kê Doanh thu</h1>

<!-- Chart Selection and Display -->
<div class="mb-6 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Biểu đồ Doanh thu</h2>
    <div class="mb-4">
        <label for="chartSelect" class="block text-sm font-medium text-gray-700">Chọn biểu đồ:</label>
        <select id="chartSelect" class="mt-1 p-2 border border-gray-300 rounded-md">
            <option value="weekly">Doanh thu theo tuần</option>
            <option value="monthly">Doanh thu theo tháng</option>
            <option value="yearly">Doanh thu theo năm</option>
        </select>
    </div>
    <div id="chartContainer">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- Filter Form -->
<div class="mb-6 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Lọc theo thời gian</h2>
    <form method="GET" class="flex items-center gap-x-4">
        <div>
            <label for="period" class="block text-sm font-medium text-gray-700">Thời gian</label>
            <select name="period" id="period" onchange="toggleInputs()" class="mt-1 p-2 rounded-md border border-gray-200">
                <option value="day" <?php echo $period === 'day' ? 'selected' : ''; ?>>Ngày</option>
                <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>Tháng</option>
                <option value="year" <?php echo $period === 'year' ? 'selected' : ''; ?>>Năm</option>
            </select>
        </div>
        <div id="date-input" class="<?php echo $period !== 'day' ? 'hidden' : ''; ?>">
            <label for="date" class="block text-sm font-medium text-gray-700">Chọn ngày</label>
            <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($date); ?>" class="p-2 rounded-md border border-gray-200">
        </div>
        <div id="month-input" class="<?php echo $period !== 'month' ? 'hidden' : ''; ?>">
            <label for="month" class="block text-sm font-medium text-gray-700">Chọn tháng</label>
            <input type="month" name="month" id="month" value="<?php echo htmlspecialchars($month); ?>" class="p-2 rounded-md border border-gray-200">
        </div>
        <div id="year-input" class="<?php echo $period !== 'year' ? 'hidden' : ''; ?>">
            <label for="year" class="block text-sm font-medium text-gray-700">Chọn năm</label>
            <input type="number" name="year" id="year" value="<?php echo htmlspecialchars($year); ?>" min="2000" max="<?php echo date('Y'); ?>" class="p-2 rounded-md border border-gray-200">
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Lọc</button>
        </div>
    </form>
</div>

<!-- Summary Section -->
<div class="mb-6 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Tổng quan Doanh thu</h2>
    <p><strong>Tổng doanh thu:</strong> <?php echo number_format($total_revenue, 2); ?> VND</p>
    <p><strong>Số đơn hàng:</strong> <?php echo $order_count; ?></p>
    <p><strong>Giá trị trung bình đơn hàng:</strong> <?php echo number_format($avg_order_value, 2); ?> VND</p>
</div>

<!-- Orders Table -->
<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-semibold mb-4 p-6">Danh sách Đơn hàng</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Đơn hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Người dùng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            if ($orders_result && $orders_result->num_rows > 0) {
                while ($row = $orders_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['order_id']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['user_name'] ?? 'Không xác định') . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>";
                    echo "<a href='order_details.php?order_id=" . htmlspecialchars($row['order_id']) . "' class='inline-block bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 text-sm'>Xem chi tiết</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>Không có đơn hàng nào trong khoảng thời gian này.</td></tr>";
            }

            $stmt_orders->close();
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleInputs() {
    const period = document.getElementById('period').value;
    document.getElementById('date-input').classList.add('hidden');
    document.getElementById('month-input').classList.add('hidden');
    document.getElementById('year-input').classList.add('hidden');
    if (period === 'day') {
        document.getElementById('date-input').classList.remove('hidden');
    } else if (period === 'month') {
        document.getElementById('month-input').classList.remove('hidden');
    } else if (period === 'year') {
        document.getElementById('year-input').classList.remove('hidden');
    }
}

let revenueChart;
function updateChart() {
    const chartSelect = document.getElementById('chartSelect').value;
    let labels, data, backgroundColor, borderColor, xTitle;

    if (revenueChart) {
        revenueChart.destroy();
    }

    switch (chartSelect) {
        case 'weekly':
            labels = <?php echo json_encode(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']); ?>;
            data = <?php echo json_encode($weekly_data); ?>;
            backgroundColor = '#3B82F6';
            borderColor = '#2563EB';
            xTitle = 'Ngày trong tuần';
            break;
        case 'monthly':
            labels = <?php echo json_encode($monthly_labels); ?>;
            data = <?php echo json_encode($monthly_data); ?>;
            backgroundColor = '#10B981';
            borderColor = '#059669';
            xTitle = 'Ngày trong tháng';
            break;
        case 'yearly':
            labels = <?php echo json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']); ?>;
            data = <?php echo json_encode($yearly_data); ?>;
            backgroundColor = '#F59E0B';
            borderColor = '#D97706';
            xTitle = 'Tháng trong năm';
            break;
    }

    const ctx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: data,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VND)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: xTitle
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateChart();
    document.getElementById('chartSelect').addEventListener('change', updateChart);
});
</script>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
<?php
ob_start();
session_start(); // Start the session to access user_id
?>

<main class="flex-1 pt-28 px-4">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-xl">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-6">Liên Hệ Với Chúng Tôi</h2>
        <p class="text-gray-600 mb-4">Chúng tôi luôn sẵn sàng hỗ trợ bạn. Vui lòng để phản hồi dưới đây hoặc liên hệ trực tiếp qua các kênh sau:</p>
        
        <!-- Display success or error messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'feedback_submitted'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                Cảm ơn bạn! Phản hồi của bạn đã được gửi thành công.
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php
                if ($_GET['error'] === 'empty_message') {
                    echo 'Vui lòng nhập nội dung phản hồi.';
                } elseif ($_GET['error'] === 'database_error') {
                    echo 'Có lỗi xảy ra khi gửi phản hồi. Vui lòng thử lại sau.';
                } elseif ($_GET['error'] === 'please_login') {
                    echo 'Vui lòng đăng nhập để gửi phản hồi.';
                } elseif ($_GET['error'] === 'invalid_request') {
                    echo 'Yêu cầu không hợp lệ.';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Thông Tin Liên Hệ</h3>
                <p class="text-gray-600"><strong>Email:</strong> JustFans@gmail.com</p>
                <p class="text-gray-600"><strong>Hotline:</strong> 0123 456 789</p>
                <p class="text-gray-600"><strong>Địa chỉ:</strong> 140, Lê Trọng Tấn, TP. HCM</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Gửi Tin Nhắn</h3>
                <form action="contact_process.php" method="POST" class="space-y-4">
                    <div>
                        <textarea id="message" name="message" rows="4" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300">Gửi Tin Nhắn</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
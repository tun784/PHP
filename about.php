<?php
ob_start();
?>

<main class="flex-1 pt-20 px-4 bg-gray-50">
    <!-- Hero Section with Thumbnail -->
    <div class="relative w-full h-64 md:h-96 bg-cover bg-center rounded-xl shadow-lg mb-8" style="background-image: url('thumbnails.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg">Về JustFans</h1>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-xl transition-all duration-300 hover:shadow-2xl">
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-6">Giới Thiệu Về Chúng Tôi</h2>
        <div class="space-y-6 text-gray-600 leading-relaxed">
            <p>
                <span class="font-semibold text-blue-600">JustFans</span> là thương hiệu hàng đầu trong lĩnh vực cung cấp các sản phẩm quạt máy chất lượng cao, mang lại sự mát mẻ và thoải mái cho mọi gia đình Việt Nam. Với sứ mệnh <em>"Làm mát cuộc sống của mọi nhà"</em>, chúng tôi cam kết mang đến những sản phẩm bền bỉ, thiết kế hiện đại và thân thiện với môi trường.
            </p>
            <p>
                Được thành lập vào năm <span class="font-semibold">2020</span>, JustFans đã không ngừng đổi mới và phát triển để đáp ứng nhu cầu ngày càng cao của khách hàng. Đội ngũ của chúng tôi bao gồm các chuyên gia giàu kinh nghiệm, luôn sẵn sàng hỗ trợ và tư vấn để bạn có trải nghiệm mua sắm tốt nhất.
            </p>
            <p>
                Chúng tôi tự hào là sự lựa chọn tin cậy của hàng ngàn gia đình trên khắp cả nước. Hãy cùng JustFans tạo nên không gian sống mát mẻ và dễ chịu!
            </p>
        </div>
        <!-- Call to Action -->
        <div class="mt-8">
            <a href="contact.php" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-300">Liên Hệ Với Chúng Tôi</a>
        </div>
    </div>
</main>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
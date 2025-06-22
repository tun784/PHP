<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="vi">
    <h1 class="flex justify-center px-2 sm:px-4"><strong>Đây là trang quản lý của JUSTFANS Admin</strong></h1>
</html>
<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
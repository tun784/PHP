<?php
$conn = new mysqli("localhost", "root", "", "web_ban_quat");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

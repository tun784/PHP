<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $_SESSION['message'] = 'Username hoặc password không được để trống';
    } elseif ($username === "duy" && $password === "123") {
        $_SESSION['message'] = 'Đăng nhập thành công';
    } else {
        $_SESSION['message'] = 'Username hoặc password không đúng';
    }

    header('Location: trangchu.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>
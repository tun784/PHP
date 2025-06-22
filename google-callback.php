<?php
require_once 'vendor/autoload.php';
include("db.php");

session_start();

$client = new Google_Client();
$client->setClientId('xxx.apps.googleusercontent.com');
$client->setClientSecret('GOCSPX-xxx-xxx-xxx-xxx');
$client->setRedirectUri('http://localhost/Demo/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name = $google_account_info->name;

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = 'customer';
            $_SESSION['user_id'] = $result->fetch_assoc()['user_id'];
        } else {
            $password = 'gglogin_' . uniqid();
            $role = 'customer';

            $insert_stmt = $conn->prepare("INSERT INTO user (user_name, password, role, email, full_name) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $name, $password, $role, $email, $name);
            $insert_stmt->execute();

            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $conn->insert_id;
        }

        header('Location: index.php');
        exit();
    } else {
        echo "Lỗi đăng nhập bằng Google: " . $token['error_description'];
    }
} else {
    echo "Không có mã xác thực từ Google";
}
exit;
?>

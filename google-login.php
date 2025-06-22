<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('xxx.apps.googleusercontent.com');
$client->setClientSecret('GOCSPX-xxx-xxx-xxx-xxx');
$client->setRedirectUri('http://localhost/Demo/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$loginUrl = $client->createAuthUrl();
header("Location: $loginUrl");
exit;
?>

<?php
    require "connection.php";

function redirect() {
    header("Location: login.php");
    exit();
}


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if(empty($password) || empty($email)) { header("Location: login.php"); exit(); }

$selectingUser= $pdo->prepare("SELECT user_id,password FROM users WHERE email=?");
$selectingUser->execute([$email]);
$user=$selectingUser->fetch(PDO::FETCH_ASSOC);


if(!$user || !password_verify($password, $user['password']))
{
    header("Location: login.php");
    exit();
}

$selectingIP= $pdo->prepare("SELECT ip_address FROM user_sessions WHERE user_id=?");
$selectingIP->execute([$user['user_id']]);
$IP=$selectingIP->fetch(PDO::FETCH_ASSOC);

$new_ip = $_SERVER['REMOTE_ADDR'];

if($IP!==$new_ip) {
    
    
    header("Location: otp.php");
    exit();
}


$sessionLifetime = 60 * 60 * 24;

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => '/',
    'secure'   => false,   
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

session_regenerate_id(true);

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['login_time'] = time();


header("Location: home.php");
exit();


?>
<?php
require 'connection.php';
session_start();

$user_id=$_SESSION['otp_user'];

$selectingOTP= $pdo->prepare("SELECT otp, otp_expiration FROM otps WHERE user_id=?");
$selectingOTP->execute([$user_id]);
$user_otp= $selectingOTP->fetch(PDO::FETCH_ASSOC);

$expiresAt= new datetime($user_otp['otp_expiration']);

if($expiresAt< new datetime())
{
    header("Location: login.php");
    exit();
}



$otp= "";
for($i=1 ; $i<=6; $i++)
{
    $otp .= $_POST["$i"];
}


if(password_verify($otp, $user_otp['otp'])) 
{ 
    session_unset();
    session_destroy();

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

    $_SESSION['user_id'] = $user_id;
    $_SESSION['login_time'] = time();

    header("Location: home.php");
    exit;
}

else { header("Location: login.php"); exit(); }


?>
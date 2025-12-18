<?php
    require "connection.php";
    require "mailler.php";

function generateOTP()
{
    $generator = "1357902468";
    $result="";
    for($i=0; $i<6; $i++)
    {
        $result .= substr($generator, (rand()%(strlen($generator))),1);
    }

    return $result;
}


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if(empty($password) || empty($email)) { header("Location: login.php"); exit(); }

$selectingUser= $pdo->prepare("SELECT user_id, first_name, last_name, password FROM users WHERE email=?");
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

if($IP!=$new_ip) {
    
    $otp=generateOTP();
    $hashedotp= password_hash($otp, PASSWORD_DEFAULT);
    $expiresAt= date('Y-m-d H:i:s', time()+600);

    $insertingOTP= $pdo->prepare("INSERT INTO otps (user_id,otp,otp_expiration)
    VALUES(?,?,?) ON DUPLICATE KEY UPDATE
    otp = VALUES(otp), otp_expiration = VALUES(otp_expiration) " );
    $insertingOTP->execute([$user['user_id'], $hashedotp, $expiresAt]);

    $name= $user['last_name'].$user['first_name'];
    generateEmail($email, $name, $otp);
    session_start();
    $_SESSION['otp_user']=$user['user_id'];
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
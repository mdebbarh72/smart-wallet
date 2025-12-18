<?php
    require "connection.php";

function redirect() {
    header("Location: signup.php");
    exit();
}

$fname = trim($_POST['fname'] ?? '');
$lname = trim($_POST['lname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$terms = $_POST['terms'] ?? '';


if (empty($fname)) {
    redirect();
}
if (empty($lname)) {
    redirect();
}
if (empty($email)) {
    redirect();
}
if (empty($password)) {
    redirect();
}
if (empty($confirmPassword)) {
    redirect();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect();
}

if ($password !== $confirmPassword) {
    redirect("");
}

if ($terms !== 'on') {
    redirect();
}

if (strlen($password) < 8) {
    redirect();
}
if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
    redirect();
}
if (!preg_match('/[\d]/', $password) && !preg_match('/[^A-Za-z0-9]/', $password)) {
    redirect();
}
if (preg_match('/(.)\1{2,}/', $password)) {
    redirect();
}


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$user_ip = $_SERVER['REMOTE_ADDR'];
try
    {
        $sql= $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $sql->execute([$fname, $lname, $email, $hashedPassword]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

$user_id = $pdo->lastInsertId();

try
    {
        $sql= $pdo->prepare("INSERT INTO user_sessions (user_id, ip_address) VALUES (?, ?)");
        $sql->execute([$user_id, $user_ip]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
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

$_SESSION['user_id'] = $user_id;
$_SESSION['login_time'] = time();


header("Location: home.php");
exit();
?>

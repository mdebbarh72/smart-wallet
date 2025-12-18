<?php


$host= 'localhost';
$dbname= 'smart wallet';
$username= 'root';
$password= '';
$charset= 'utf8mb4';


$dsn= "mysql:host=$host;dbname=$dbname;charset=$charset";


try 
{
    $pdo= new PDO($dsn, $username, $password);
    $pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (PDOException $e)
{
    die("Connection failed:".$e->getMessage());
}

?>
<?php

//credentials and connection details
$host= 'localhost';
$dbname= 'smart wallet';
$username= 'root';
$password= '';
$charset= 'utf8mb4';

//dsn(specifies the driver, host and data base name)
$dsn= "mysql:host=$host;dbname=$dbname;charset=$charset";

//establishing the connection
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
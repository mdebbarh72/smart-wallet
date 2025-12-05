<?php
require 'connection.php';

if(isset($_POST['action']) && $_POST['action']=="add")
{
    $amount= $_POST['amount'];
    $description= $_POST['description'];
    $date= $_POST['date'];

    try
    {
        $sql= $pdo->prepare("INSERT INTO incomes (amount, description, income_date) VALUES (?, ?, ?)");
        $sql->execute([$amount, $description, $date]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: incomes.php');
    exit;
}

else if(isset($_POST['action']) && $_POST['action']=="edit")
{
    $amount= $_POST['new-amount'];
    $description= $_POST['new-description'];
    $date= $_POST['new-date'];
    $id= $_POST['id'];

    try
    {
        $sql= $pdo->prepare("UPDATE incomes SET amount=? , description=?, created_at=? WHERE id=?");
        $sql->execute([$amount, $description, $date, $id]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: incomes.php');
    exit;
}

else if(isset($_POST['action']) && $_POST['action']=="delete")
{
    $id= $_POST['delete-id'];

    try
    {
        $sql= $pdo->prepare("DELETE FROM incomes WHERE id =?");
        $sql->execute([$id]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: incomes.php');
    exit;
}
?>
<?php
require 'connection.php';

if(isset($_POST['action']) && $_POST['action']=="add")
{
    $amount= $_POST['amount'];
    $description= $_POST['description'];
    $date= $_POST['date'];

    try
    {
        $sql= $pdo->prepare("INSERT INTO expenses (amount, description, expense_date) VALUES (?, ?, ?)");
        $sql->execute([$amount, $description, $date]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: expenses.php');
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
        $sql= $pdo->prepare("UPDATE expenses SET amount=? , description=?, expense_date=? WHERE id=?");
        $sql->execute([$amount, $description, $date, $id]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: expenses.php');
    exit;
}

else if(isset($_POST['action']) && $_POST['action']=="delete")
{
    $id= $_POST['delete-id'];

    try
    {
        $sql= $pdo->prepare("DELETE FROM expenses WHERE id =?");
        $sql->execute([$id]);
    }
    catch (PDOException $e)
    {
        die("Error during insertion: ".$e->getMessage());
    }

    header('location: expenses.php');
    exit;
}
?>
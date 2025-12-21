<?php
require "connection.php";
$sessionLifetime = 60 * 60 * 24;

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => '/',
    'domain'   => '',
    'secure'   => false,   
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }


// if (time() - $_SESSION['login_time'] > $sessionLifetime) {
//     session_unset();
//     session_destroy();
//     header("Location: login.php?expired=1");
//     exit;
// }



if(isset($_POST['operation'] ) && $_POST['operation']=="add")
{
    $card_balance=$_POST['card_balance'];
    $card_name=$_POST['card_name'];
    $bank_name=$_POST['bank_name'];
    $card_number=$_POST['card_number'];
    $card_type = ucfirst(strtolower($_POST['card_type'])); 
    
    if(empty($card_name) || empty($bank_name) || empty($card_type)) {
    die("Required fields are missing!");
}

    $insertingcard= $pdo->prepare("INSERT INTO cards (user_id, card_total, card_name, bank_name, card_number, card_type )
                                    VALUES (?,?,?,?,?,?) ");
    $insertingcard->execute([$_SESSION['user_id'], $card_balance, $card_name, $bank_name, $card_number, $card_type]);
    
    $card_id= $pdo->lastInsertId();

    
    if(isset($_POST['spending_limit']) && !empty($_POST['spending_limit']))
    {
        $spending_limit = floatval($_POST['spending_limit']);
        
        
        $checkCategory = $pdo->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $checkCategory->execute(['total']);
        $category_id = $checkCategory->fetchColumn();
        
        if (!$category_id) {
            
            $insertCategory = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $insertCategory->execute(['total']);
            $category_id = $pdo->lastInsertId();
        }

        $insertingLimit = $pdo->prepare("INSERT INTO cards_limits(category_id, card_id, amount_limit, consumed_amount) VALUES(?, ?, ?, 0)");
        $insertingLimit->execute([$category_id, $card_id, $spending_limit]);
    }

    
    if(isset($_POST['card_limit']) && !empty($_POST['card_limit']) && 
    isset($_POST['limit_category']) && !empty($_POST['limit_category']))
    {
        $card_limit = floatval($_POST['card_limit']);
        $limit_category = $_POST['limit_category'];
        
        
        $checkCategory = $pdo->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $checkCategory->execute([$limit_category]);
        $category_id = $checkCategory->fetchColumn();
        
        if (!$category_id) {
            
            $insertCategory = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $insertCategory->execute([$limit_category]);
            $category_id = $pdo->lastInsertId();
        }

        $insertingLimit = $pdo->prepare("INSERT INTO cards_limits(category_id, card_id, amount_limit, consumed_amount) VALUES(?, ?, ?, 0)");
        $insertingLimit->execute([$category_id, $card_id, $card_limit]);
    }
    

    $selectingCardCount= $pdo->prepare('SELECT COUNT(*) 
                                        FROM cards
                                        WHERE user_id= ?');
    $selectingCardCount->execute([$_SESSION['user_id']]);
    $cardsCount= $selectingCardCount->fetchColumn();

    if($cardsCount == 1 || isset($_POST['primary']))
    {
        
        $deletePrimary = $pdo->prepare("DELETE FROM main_cards WHERE user_id = ?");
        $deletePrimary->execute([$_SESSION['user_id']]);
        
        $insertingPrimaryCard = $pdo->prepare("INSERT INTO main_cards (user_id, card_id) VALUES (?, ?)");
        $insertingPrimaryCard->execute([$_SESSION['user_id'], $card_id]);
    }


    header("Location: cards.php"); exit();
}


if(isset($_POST['operation'] ) && $_POST['operation']=="set_primary")
{
    $card_id= $_POST['card_id'];
    $user_id= $_SESSION['user_id'];

    $deleting_primary= $pdo->prepare("DELETE FROM main_cards WHERE user_id=?");
    $deleting_primary->execute([$_SESSION['user_id']]);

    $inserting_primary= $pdo->prepare('INSERT INTO main_cards(user_id,card_id) VALUES(?,?) ');
    $inserting_primary-> execute([$user_id, $card_id]);
    
    header("Location: cards.php"); exit();
}








?>


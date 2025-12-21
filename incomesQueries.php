
<?php
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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (time() - $_SESSION['login_time'] > $sessionLifetime) {
    header("Location: login.php");
    exit();
}

require 'connection.php';

$user_id = $_SESSION['user_id'];


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        
        $amount = $_POST['amount'];
        $description = trim($_POST['description']);
        $card_id = $_POST['card_id'];
        $income_date = $_POST['income_date'];
        
        if (empty($income_date)) {
            $income_date = date('Y-m-d');
        }
        
        try {
            
            $insertingIncome = $pdo->prepare("INSERT INTO incomes (amount, description, income_date, card_id) VALUES (?, ?, ?, ?)");
            $insertingIncome->execute([$amount, $description, $income_date, $card_id]);
            
            
            $updatingCard = $pdo->prepare("UPDATE cards SET card_total = card_total + ? WHERE card_id = ?");
            $updatingCard->execute([$amount, $card_id]);
            
            header("Location: incomes.php");
            exit();
        } catch (PDOException $e) {
            die("Error adding income: " . $e->getMessage());
        }
    } 
    elseif ($action == 'edit') {
       
        $income_id = $_POST['id'];
        $amount = $_POST['amount'];
        $description = trim($_POST['description']);
        $card_id = $_POST['card_id'];
        $income_date = $_POST['income_date'];
        
        try {
            
            $updatingIncome = $pdo->prepare("
                UPDATE incomes 
                SET amount = ?, description = ?, income_date = ?, card_id = ? 
                WHERE id = ?
            ");
            $updatingIncome->execute([$amount, $description, $income_date, $card_id, $income_id]);
            
            
            if ($old_income['old_card_id'] != $card_id) {
                
                $updatingOldCard = $pdo->prepare("UPDATE cards SET card_total = card_total - ? WHERE card_id = ?");
                $updatingOldCard->execute([$old_income['old_amount'], $old_income['old_card_id']]);
                
                
                $updatingNewCard = $pdo->prepare("UPDATE cards SET card_total = card_total + ? WHERE card_id = ?");
                $updatingNewCard->execute([$amount, $card_id]);
            } else {
                
                $amount_diff = $amount - $old_income['old_amount'];
                $updatingCard = $pdo->prepare("UPDATE cards SET card_total = card_total + ? WHERE card_id = ?");
                $updatingCard->execute([$amount_diff, $card_id]);
            }
            
            header("Location: incomes.php");
            exit();
        } catch (PDOException $e) {
            die("Error updating income: " . $e->getMessage());
        }
    }
    elseif ($action == 'delete') {
        
        $income_id = $_POST['id'];
        
        try {
            
            $deletingIncome = $pdo->prepare("DELETE FROM incomes WHERE id = ?");
            $deletingIncome->execute([$income_id]);
            
            
            $updatingCard = $pdo->prepare("UPDATE cards SET card_total = card_total - ? WHERE card_id = ?");
            $updatingCard->execute([$income_data['amount'], $income_data['card_id']]);
            
            header("Location: incomes.php");
            exit();
        } catch (PDOException $e) {
            die("Error deleting income: " . $e->getMessage());
        }
    }
}


header("Location: incomes.php");
exit();
?>

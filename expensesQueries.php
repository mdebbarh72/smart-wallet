<?php
require 'connection.php';


session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


function reccurentTransactions($pdo, $user_id) {
    $last_processed = $_SESSION['last_recurring_process'] ?? '';
    $current_month = date('Y-m');
    
    if ($last_processed !== $current_month) {
        
        $recurring_sql = $pdo->prepare("
            SELECT rt.* 
            FROM recurrent_transactions rt 
            LEFT JOIN cards c ON rt.card_id = c.card_id 
            WHERE c.user_id = ?
        ");
        $recurring_sql->execute([$user_id]);
        $recurring_transactions = $recurring_sql->fetchAll(PDO::FETCH_ASSOC);
        
        $current_date = date('Y-m-d');
        
        foreach ($recurring_transactions as $recurring) {
            
            $card_sql = $pdo->prepare("SELECT card_total FROM cards WHERE card_id = ?");
            $card_sql->execute([$recurring['card_id']]);
            $card = $card_sql->fetch(PDO::FETCH_ASSOC);
            
            if ($card && $card['card_total'] >= $recurring['transaction_amount']) {
                
                if ($recurring['category_id']) {
                    $limit_sql = $pdo->prepare("
                        SELECT amount_limit, consumed_amount 
                        FROM cards_limits 
                        WHERE card_id = ? AND category_id = ?
                    ");
                    $limit_sql->execute([$recurring['card_id'], $recurring['category_id']]);
                    $limit = $limit_sql->fetch(PDO::FETCH_ASSOC);
                    
                    if ($limit) {
                        $new_consumed = $limit['consumed_amount'] + $recurring['transaction_amount'];
                        if ($new_consumed > $limit['amount_limit']) {
                            
                            continue;
                        }
                        
                        
                        $update_limit_sql = $pdo->prepare("
                            UPDATE cards_limits 
                            SET consumed_amount = consumed_amount + ? 
                            WHERE card_id = ? AND category_id = ?
                        ");
                        $update_limit_sql->execute([$recurring['transaction_amount'], $recurring['card_id'], $recurring['category_id']]);
                    }
                }
                
                
                $update_card_sql = $pdo->prepare("UPDATE cards SET card_total = card_total - ? WHERE card_id = ?");
                $update_card_sql->execute([$recurring['transaction_amount'], $recurring['card_id']]);
                
                
                $expense_sql = $pdo->prepare("
                    INSERT INTO expenses (amount, description, expense_date, card_id, category_id) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $expense_sql->execute([
                    $recurring['transaction_amount'],
                    $recurring['transaction_description'],
                    $current_date,
                    $recurring['card_id'],
                    $recurring['category_id']
                ]);
            }
        }
        
        
        $_SESSION['last_recurring_process'] = $current_month;
    }
}


if (date('j') <= 5) { 
    reccurentTransactions($pdo, $user_id);
}

if(isset($_POST['action']) && $_POST['action']=="add")
{
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'] ?? date('Y-m-d');
    $category_id = $_POST['category_id'] ?? null;
    $card_id = $_POST['card_id'] ?? null;
    $is_recurring = isset($_POST['is_recurring']) ? (int)$_POST['is_recurring'] : 0;
    $make_recurring = isset($_POST['make_recurring']) ? true : false;

    try
    {
        
        if ($card_id) {
            $card_sql = $pdo->prepare("SELECT card_total FROM cards WHERE card_id = ? AND user_id = ?");
            $card_sql->execute([$card_id, $user_id]);
            $card = $card_sql->fetch(PDO::FETCH_ASSOC);
            
            if (!$card) {
                header('location: expenses.php?message=Selected card not found&error=1');
                exit;
            }
            
            
            if ($card['card_total'] < $amount) {
                header('location: expenses.php?message=Insufficient card balance&error=1');
                exit;
            }
            
            
            if ($category_id) {
                $limit_sql = $pdo->prepare("
                    SELECT amount_limit, consumed_amount 
                    FROM cards_limits 
                    WHERE card_id = ? AND category_id = ?
                ");
                $limit_sql->execute([$card_id, $category_id]);
                $limit = $limit_sql->fetch(PDO::FETCH_ASSOC);
                
                if ($limit) {
                    $new_consumed = $limit['consumed_amount'] + $amount;
                    if ($new_consumed > $limit['amount_limit']) {
                        $exceeded_by = $new_consumed - $limit['amount_limit'];
                        header("location: expenses.php?limit_exceeded=Category limit exceeded by " . $exceeded_by . " DH");
                        exit;
                    }
                }
            }
            
            
            if (!$make_recurring) {
                
                $update_card_sql = $pdo->prepare("UPDATE cards SET card_total = card_total - ? WHERE card_id = ?");
                $update_card_sql->execute([$amount, $card_id]);
                
                
                if ($category_id && isset($limit)) {
                    $update_limit_sql = $pdo->prepare("
                        UPDATE cards_limits 
                        SET consumed_amount = consumed_amount + ? 
                        WHERE card_id = ? AND category_id = ?
                    ");
                    $update_limit_sql->execute([$amount, $card_id, $category_id]);
                }
            }
        }
        
        if ($make_recurring) {
            
            $recurring_sql = $pdo->prepare("
                INSERT INTO recurrent_transactions (card_id, transaction_description, category_id, transaction_amount) 
                VALUES (?, ?, ?, ?)
            ");
            $recurring_sql->execute([$card_id, $description, $category_id, $amount]);
            
            $message = 'Recurring expense added successfully! It will be automatically processed at the beginning of each month.';
        } else {
            
            $sql = $pdo->prepare("
                INSERT INTO expenses (amount, description, expense_date, card_id, category_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $sql->execute([$amount, $description, $date, $card_id, $category_id]);
            
            $message = 'Expense added successfully';
        }
        
        header('location: expenses.php?message=' . urlencode($message));
        exit;
    }
    catch (PDOException $e)
    {
        header('location: expenses.php?message=Error: ' . urlencode($e->getMessage()) . '&error=1');
        exit;
    }
}

else if(isset($_POST['action']) && $_POST['action']=="edit")
{
    $amount = $_POST['new-amount'];
    $description = $_POST['new-description'];
    $date = $_POST['new-date'];
    $category_id = $_POST['new-category_id'] ?? null;
    $card_id = $_POST['new-card_id'] ?? null;
    $id = $_POST['id'];

    try
    {
        
        $original_sql = $pdo->prepare("SELECT amount, card_id, category_id FROM expenses WHERE id = ?");
        $original_sql->execute([$id]);
        $original = $original_sql->fetch(PDO::FETCH_ASSOC);
        
        if (!$original) {
            header('location: expenses.php?message=Expense not found&error=1');
            exit;
        }
        
        $original_amount = $original['amount'];
        $original_card_id = $original['card_id'];
        $original_category_id = $original['category_id'];
        
        
        if ($original_card_id != $card_id || $original_amount != $amount) {
            
            if ($original_card_id) {
                $return_sql = $pdo->prepare("UPDATE cards SET card_total = card_total + ? WHERE card_id = ?");
                $return_sql->execute([$original_amount, $original_card_id]);
                
                
                if ($original_category_id) {
                    $return_limit_sql = $pdo->prepare("
                        UPDATE cards_limits 
                        SET consumed_amount = consumed_amount - ? 
                        WHERE card_id = ? AND category_id = ?
                    ");
                    $return_limit_sql->execute([$original_amount, $original_card_id, $original_category_id]);
                }
            }
            
            
            if ($card_id) {
                $card_sql = $pdo->prepare("SELECT card_total FROM cards WHERE card_id = ? AND user_id = ?");
                $card_sql->execute([$card_id, $user_id]);
                $card = $card_sql->fetch(PDO::FETCH_ASSOC);
                
                if (!$card) {
                    header('location: expenses.php?message=Selected card not found&error=1');
                    exit;
                }
                
                
                $available_balance = $card['card_total'] + ($original_card_id == $card_id ? $original_amount : 0);
                if ($available_balance < $amount) {
                    header('location: expenses.php?message=Insufficient card balance&error=1');
                    exit;
                }
                
                
                if ($category_id) {
                    $limit_sql = $pdo->prepare("
                        SELECT amount_limit, consumed_amount 
                        FROM cards_limits 
                        WHERE card_id = ? AND category_id = ?
                    ");
                    $limit_sql->execute([$card_id, $category_id]);
                    $limit = $limit_sql->fetch(PDO::FETCH_ASSOC);
                    
                    if ($limit) {
                        
                        $current_consumed = $limit['consumed_amount'];
                        if ($original_card_id == $card_id && $original_category_id == $category_id) {
                            $current_consumed -= $original_amount;
                        }
                        
                        $new_consumed = $current_consumed + $amount;
                        if ($new_consumed > $limit['amount_limit']) {
                            $exceeded_by = $new_consumed - $limit['amount_limit'];
                            header("location: expenses.php?limit_exceeded=Category limit exceeded by " . $exceeded_by . " DH");
                            exit;
                        }
                    }
                }
                
                
                $deduct_sql = $pdo->prepare("UPDATE cards SET card_total = card_total - ? WHERE card_id = ?");
                $deduct_sql->execute([$amount, $card_id]);
                
                
                if ($category_id && isset($limit)) {
                    $update_limit_sql = $pdo->prepare("
                        UPDATE cards_limits 
                        SET consumed_amount = consumed_amount + ? 
                        WHERE card_id = ? AND category_id = ?
                    ");
                    
                    $adjustment = $amount;
                    if ($original_card_id == $card_id && $original_category_id == $category_id) {
                        $adjustment = $amount - $original_amount;
                    }
                    $update_limit_sql->execute([$adjustment, $card_id, $category_id]);
                }
            }
        }
        
        
        $sql = $pdo->prepare("
            UPDATE expenses 
            SET amount = ?, description = ?, expense_date = ?, card_id = ?, category_id = ? 
            WHERE id = ?
        ");
        $sql->execute([$amount, $description, $date, $card_id, $category_id, $id]);

        header('location: expenses.php?message=Expense updated successfully');
        exit;
    }
    catch (PDOException $e)
    {
        header('location: expenses.php?message=Error: ' . urlencode($e->getMessage()) . '&error=1');
        exit;
    }
}

else if(isset($_POST['action']) && $_POST['action']=="edit_recurring")
{
    $amount = $_POST['new-amount'];
    $description = $_POST['new-description'];
    $category_id = $_POST['new-category_id'] ?? null;
    $card_id = $_POST['new-card_id'] ?? null;
    $recurring_id = $_POST['recurring_id'];

    try
    {
        
        $sql = $pdo->prepare("
            UPDATE recurrent_transactions 
            SET transaction_amount = ?, transaction_description = ?, card_id = ?, category_id = ? 
            WHERE transaction_id = ?
        ");
        $sql->execute([$amount, $description, $card_id, $category_id, $recurring_id]);

        header('location: expenses.php?message=Recurring expense updated successfully');
        exit;
    }
    catch (PDOException $e)
    {
        header('location: expenses.php?message=Error: ' . urlencode($e->getMessage()) . '&error=1');
        exit;
    }
}

else if(isset($_POST['action']) && $_POST['action']=="delete")
{
    $id = $_POST['delete-id'];

    try
    {
        
        $expense_sql = $pdo->prepare("SELECT amount, card_id, category_id FROM expenses WHERE id = ?");
        $expense_sql->execute([$id]);
        $expense = $expense_sql->fetch(PDO::FETCH_ASSOC);
        
        if ($expense && $expense['card_id']) {
            
            $return_sql = $pdo->prepare("UPDATE cards SET card_total = card_total + ? WHERE card_id = ?");
            $return_sql->execute([$expense['amount'], $expense['card_id']]);
            
            
            if ($expense['category_id']) {
                $update_limit_sql = $pdo->prepare("
                    UPDATE cards_limits 
                    SET consumed_amount = consumed_amount - ? 
                    WHERE card_id = ? AND category_id = ?
                ");
                $update_limit_sql->execute([$expense['amount'], $expense['card_id'], $expense['category_id']]);
            }
        }
        
        
        $sql = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
        $sql->execute([$id]);

        header('location: expenses.php?message=Expense deleted successfully');
        exit;
    }
    catch (PDOException $e)
    {
        header('location: expenses.php?message=Error: ' . urlencode($e->getMessage()) . '&error=1');
        exit;
    }
}

else if(isset($_POST['action']) && $_POST['action']=="delete_recurring")
{
    $recurring_id = $_POST['delete-recurring-id'];

    try
    {
        
        $sql = $pdo->prepare("DELETE FROM recurrent_transactions WHERE transaction_id = ?");
        $sql->execute([$recurring_id]);

        header('location: expenses.php?message=Recurring expense deleted successfully');
        exit;
    }
    catch (PDOException $e)
    {
        header('location: expenses.php?message=Error: ' . urlencode($e->getMessage()) . '&error=1');
        exit;
    }
}
?>
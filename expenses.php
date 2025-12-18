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
    exit;
}


if (time() - $_SESSION['login_time'] > $sessionLifetime) {
    session_unset();
    session_destroy();
    header("Location: login.php?expired=1");
    exit;
}



?>
<?php

require 'connection.php';

$sql= $pdo->prepare("SELECT * FROM expenses");
$sql->execute();

$expenses= $sql->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Finance Tracker</h1>
            <div class="space-x-4">
                <a href="index.php"  class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
                <a href="incomes.php"  class="hover:bg-blue-700 px-3 py-2 rounded">Incomes</a>
                <a href="#" class="hover:bg-blue-700 px-3 py-2 rounded">Expenses</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div id="expenses-page" class="page-content">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Expenses</h2>
                    <button onclick="openAddExpenseModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">+ Add Expense</button>
                </div>
                <div id="expenses-list" class="space-y-3">
                    <?php
                        $count = 0; 
                        foreach ($expenses as $expense) {
                            echo '<div class="item item-' . $count . ' flex justify-evenly">  
                                    <div class="item-amount">' . htmlspecialchars($expense['amount']) . '</div>
                                    <div class="item-description">' . htmlspecialchars($expense['description']) . '</div>
                                    <div class="item-created_at">' . htmlspecialchars($expense['expense_date']) . '</div>
                                    <div class="item-modified_at">' . htmlspecialchars($expense['updated_at']) . '</div>
                                    <div class="item-action-btn">
                                    <button class="expense-editBtn rounded-lg shadow-lg bg-orange-600 px-4 py-2" 
                                        data-id="'.$expense['id'].'" 
                                        data-amount="'.$expense['amount'].'"
                                        data-description="'.$expense['description'].'"
                                        data-date="'.$expense['expense_date'].'"
                                        >modify
                                    </button>
                                    <button class="expense-delete-item rounded-lg shadow-lg bg-red-600 px-4 py-2"
                                            data-id="'.$expense['id'].'" 
                                            data-amount="'.$expense['amount'].'"
                                            data-description="'.$expense['description'].'"
                                            data-date="'.$expense['expense_date'].'"
                                            >delete
                                    </button>
                                </div>
                            </div>';
                            $count++; 
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div id="add-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <h3 class="text-xl font-bold mb-4">Add New Expense</h3>
            <div class="space-y-3 mb-4">
                <input type="hidden" name="action" value="add">
                <input type="number" step="0.01" name="amount" id="expense-amount" placeholder="Amount" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" step="0.01">
                <input type="text" name="description" id="expense-description" placeholder="Expense Description" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600">
                <input type="date" name="date" id="expense-date" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeAddExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="add" value="Add" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            </div>
        </form>
    </div>

    <!-- Delete Expense Modal -->
    <div id="delete-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <h3 class="text-xl font-bold mb-4">Confim the deletion</h3>
            <div id="delete-expense-list" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                <input type="hidden" name="action" value="delete">
                <input type="number" step="0.01" name="amount" id="delete-expense-amount" placeholder="Amount" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" step="0.01">
                <input type="text" name="description" id="delete-expense-description" placeholder="description" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="date" name="date" id="delete-expense-date" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="delete" value="DELETE" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">
            </div>
        </form>
    </div>

    <!-- Edit Expense Modal -->
    <div id="edit-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <h3 class="text-xl font-bold mb-4">Edit Expense</h3>
            <div class="space-y-3 mb-4">
                <input type="hidden" name="action" value="edit">
                <input type="number" name="new-amount" step="0.01" id="edit-expense-amount" placeholder="Amount" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" >
                <input type="text" name="new-description" id="edit-expense-description" placeholder="Expense Description" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600">
                <input type="date" name="new-date" id="edit-expense-date" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600">

            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeEditExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit"name="save" value="save" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            </div>
        </form>
    </div>

<script src="main.js"></script>
</body>
</html>
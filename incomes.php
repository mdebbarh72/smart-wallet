<?php

require 'connection.php';

$sql= $pdo->prepare("SELECT * FROM incomes");
$sql->execute();

$incomes= $sql->fetchAll(PDO::FETCH_ASSOC);

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

    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Smart Wallet</h1>
            <div class="space-x-4">
                <a href="index.php"  class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
                <a href="#"  class="hover:bg-blue-700 px-3 py-2 rounded">Incomes</a>
                <a href="expenses.php" class="hover:bg-blue-700 px-3 py-2 rounded">Expenses</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div id="incomes-page" class="page-content">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Incomes</h2>
                    <button onclick="openAddIncomeModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Income</button>
                </div>
                <div id="incomes-list" class="space-y-3">
                    <?php
                        $count = 0; 
                        foreach ($incomes as $income) {
                            echo '<div class="item item-' . $count . ' flex justify-evenly">  
                                    <div class="item-amount">' . htmlspecialchars($income['amount']) . '</div>
                                    <div class="item-description">' . htmlspecialchars($income['description']) . '</div>
                                    <div class="item-created_at">' . htmlspecialchars($income['income_date']) . '</div>
                                    <div class="item-modified_at">' . htmlspecialchars($income['updated_at']) . '</div>
                                    <div class="item-action-btn">
                                    <button class="editBtn rounded-lg shadow-lg bg-orange-600 px-4 py-2" 
                                        data-id="'.$income['id'].'" 
                                        data-amount="'.$income['amount'].'"
                                        data-description="'.$income['description'].'"
                                        data-date="'.$income['income_date'].'"
                                        >modify
                                    </button>
                                    <button class="delete-item rounded-lg shadow-lg bg-red-600 px-4 py-2"
                                            data-id="'.$income['id'].'" 
                                            data-amount="'.$income['amount'].'"
                                            data-description="'.$income['description'].'"
                                            data-date="'.$income['income_date'].'"
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

    <!-- Add Income Modal -->
    <div id="add-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Add New Income</h3>
            <form class="space-y-3 mb-4" action="incomesQueries.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="number" step="0.01" name="amount" id="income-amount" placeholder="Amount"  class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" step="0.01">
                <input type="text" name="description" id="income-name" placeholder="description" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="date" name="date" id="income-date" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeAddIncomeModal()"  class="cancelBtn px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="save" value="Add" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            </div>
            </form>
        </div>
    </div>

    <!-- Delete Income Modal -->
    <div id="delete-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="incomesQueries.php" method="POST">
            <input type="hidden" name="action" value="delete">
            <h3 class="text-xl font-bold mb-4">Confim the deletion</h3>
            <div id="delete-income-list" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                <!-- <input type="hidden" name="id" id="delete-input-id"> -->
                <input type="number" step="0.01" name="amount" id="delete-income-amount" placeholder="Amount" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" step="0.01">
                <input type="text" name="description" id="delete-income-description" placeholder="description" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="date" name="date" id="delete-income-date" disabled class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteIncomeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="delete" value="DELETE" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">
            </div>
        </form>
    </div>

    <!-- Edit Income Modal -->
    <div id="edit-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Edit Income</h3>
            <form class="space-y-3 mb-4" action="incomesQueries.php" method="POST" >
                <input type="hidden" name="action" value="edit">
                <input type="number" name="new-amount" step="0.01" id="edit-income-amount" placeholder="Amount" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" step="0.01">
                <input type="text" name="new-description" id="edit-income-description" placeholder="description" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="date" name="new-date" id="edit-income-date" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeEditIncomeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="save" value="save" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            </div>
            </form>
        </div>
    </div>

<script src="main.js"></script>
</body>
</html>
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

if (isset($_SESSION['login_time'])) {
    if (time() - $_SESSION['login_time'] > $sessionLifetime) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
} else {
    
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

?>
<?php

require 'connection.php';


$user_id = $_SESSION['user_id'];


$sql = $pdo->prepare("
    SELECT e.*, c.card_name, cat.category_name 
    FROM expenses e 
    LEFT JOIN cards c ON e.card_id = c.card_id 
    LEFT JOIN categories cat ON e.category_id = cat.category_id 
    WHERE c.user_id = ?
    ORDER BY e.expense_date DESC
");
$sql->execute([$user_id]);
$expenses = $sql->fetchAll(PDO::FETCH_ASSOC);


$recurring_sql = $pdo->prepare("
    SELECT rt.*, c.card_name, cat.category_name 
    FROM recurrent_transactions rt 
    LEFT JOIN cards c ON rt.card_id = c.card_id 
    LEFT JOIN categories cat ON rt.category_id = cat.category_id 
    WHERE c.user_id = ?
    ORDER BY rt.transaction_description
");
$recurring_sql->execute([$user_id]);
$recurring_transactions = $recurring_sql->fetchAll(PDO::FETCH_ASSOC);


$cards_sql = $pdo->prepare("SELECT * FROM cards WHERE user_id = ?");
$cards_sql->execute([$user_id]);
$cards = $cards_sql->fetchAll(PDO::FETCH_ASSOC);


$categories_sql = $pdo->prepare("SELECT * FROM categories");
$categories_sql->execute();
$categories = $categories_sql->fetchAll(PDO::FETCH_ASSOC);


$total_expenses = count($expenses);
$recurring_count = count($recurring_transactions);

$current_month = date('Y-m');
$monthly_expenses = 0;
$this_month_count = 0;

foreach ($expenses as $expense) {
    if (date('Y-m', strtotime($expense['expense_date'])) === $current_month) {
        $monthly_expenses += $expense['amount'];
        $this_month_count++;
    }
}

$avg_expense = $this_month_count > 0 ? $monthly_expenses / $this_month_count : 0;


$message = '';
$message_type = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    if (isset($_GET['error'])) {
        $message_type = 'error';
    } else {
        $message_type = 'success';
    }
}


$limit_message = '';
if (isset($_GET['limit_exceeded'])) {
    $limit_message = htmlspecialchars($_GET['limit_exceeded']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .expense-gradient { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .limit-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
        .limit-danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        
        .recurring-badge {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }
        
        .category-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center">
                    <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></h1>
                </a>
            </div>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Home</a>
                <a href="incomes.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Incomes</a>
                <a href="expenses.php" class="bg-blue-600 text-white font-medium px-4 py-2 rounded transition-colors">Expenses</a>
                <a href="logout.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Success/Error Message -->
    <?php if ($message): ?>
    <div class="max-w-7xl mx-auto px-4 pt-6">
        <div class="<?php echo $message_type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700'; ?> border px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas <?php echo $message_type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?> mr-2"></i>
                <span><?php echo $message; ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Limit Exceeded Message -->
    <?php if ($limit_message): ?>
    <div id="limit-warning" class="max-w-7xl mx-auto px-4 pt-6">
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="warning-message"><?php echo $limit_message; ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Expense Management</h1>
                <p class="text-gray-600 mt-2">Track and control your spending</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-500">Monthly Expenses</p>
                    <p class="text-2xl font-bold text-red-600" id="monthly-expenses"><?php echo number_format($monthly_expenses, 2); ?> DH</p>
                </div>
                <button onclick="openAddExpenseModal()" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add Expense
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Total Expenses</p>
                        <p class="text-3xl font-bold text-gray-900" id="total-expenses"><?php echo $total_expenses; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">All time</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <p class="text-3xl font-bold text-gray-900" id="this-month-count"><?php echo $this_month_count; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Expense transactions</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Recurring</p>
                        <p class="text-3xl font-bold text-gray-900" id="recurring-count"><?php echo $recurring_count; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-redo-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Monthly recurring</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Avg. per Expense</p>
                        <p class="text-3xl font-bold text-gray-900" id="avg-expense"><?php echo number_format($avg_expense, 2); ?> DH</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Monthly average</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button id="tab-all" onclick="switchTab('all')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-600">
                        All Expenses
                    </button>
                    <button id="tab-recurring" onclick="switchTab('recurring')" 
                            class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Recurring Expenses
                    </button>
                </nav>
            </div>
        </div>

        <!-- All Expenses Section -->
        <div id="all-expenses-section">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Recent Expenses</h2>
                    <div class="flex items-center space-x-2">
                        <select id="filter-category" onchange="filterExpenses()" 
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filter-card" onchange="filterExpenses()" 
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">All Cards</option>
                            <?php foreach ($cards as $card): ?>
                            <option value="<?php echo htmlspecialchars($card['card_id']); ?>">
                                <?php echo htmlspecialchars($card['card_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div id="expenses-list" class="divide-y divide-gray-200">
                    <?php if (empty($expenses)): ?>
                    <div id="no-expenses" class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Expenses Yet</h3>
                        <p class="text-gray-600 mb-6">Add your first expense to start tracking</p>
                        <button onclick="openAddExpenseModal()" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3 rounded-lg inline-flex items-center transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Your First Expense
                        </button>
                    </div>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): 
                            $expense_date = new DateTime($expense['expense_date']);
                            $formatted_date = $expense_date->format('M d, Y');
                            
                            $updated_at = new DateTime($expense['updated_at']);
                            $formatted_updated = $updated_at->format('M d, Y H:i');
                        ?>
                        <div class="p-6 hover:bg-gray-50 transition-colors fade-in" 
                             data-expense-id="<?php echo htmlspecialchars($expense['id']); ?>" 
                             data-category="<?php echo htmlspecialchars($expense['category_id'] ?? ''); ?>" 
                             data-card="<?php echo htmlspecialchars($expense['card_id'] ?? ''); ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <h3 class="font-bold text-gray-900 text-lg mr-3"><?php echo htmlspecialchars($expense['description']); ?></h3>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                                  style="background-color: #8B5CF620; color: #8B5CF6;">
                                                <?php echo htmlspecialchars($expense['category_name'] ?? 'Uncategorized'); ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?php echo $formatted_date; ?>
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                <?php echo htmlspecialchars($expense['card_name'] ?? 'No Card'); ?>
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-clock mr-1"></i>
                                                Updated: <?php echo $formatted_updated; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-red-600">
                                            -<?php echo number_format($expense['amount'], 2); ?> DH
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <button onclick="editExpense(<?php echo $expense['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-800 font-medium flex items-center"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteExpense(<?php echo $expense['id']; ?>)" 
                                                class="text-red-600 hover:text-red-800 font-medium flex items-center"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recurring Expenses Section -->
        <div id="recurring-expenses-section" class="hidden">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Recurring Monthly Expenses</h2>
                    <p class="text-gray-600 text-sm mt-1">These expenses are automatically added at the beginning of each month</p>
                </div>
                
                <div id="recurring-expenses-list" class="divide-y divide-gray-200">
                    <?php if (empty($recurring_transactions)): ?>
                    <div id="no-recurring-expenses" class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-redo-alt text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Recurring Expenses</h3>
                        <p class="text-gray-600 mb-6">Set up recurring expenses to have them automatically added each month</p>
                        <button onclick="openAddExpenseModal(true)" 
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-8 py-3 rounded-lg inline-flex items-center transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Recurring Expense
                        </button>
                    </div>
                    <?php else: ?>
                        <?php foreach ($recurring_transactions as $recurring): ?>
                        <div class="p-6 hover:bg-gray-50 transition-colors" data-recurring-id="<?php echo htmlspecialchars($recurring['transaction_id']); ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-redo-alt text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg mb-1"><?php echo htmlspecialchars($recurring['transaction_description']); ?></h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                <?php echo number_format($recurring['transaction_amount'], 2); ?> DH
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                <?php echo htmlspecialchars($recurring['card_name'] ?? 'No Card'); ?>
                                            </span>
                                            <span class="px-2 py-1 rounded-full text-xs" 
                                                  style="background-color: #8B5CF620; color: #8B5CF6;">
                                                <?php echo htmlspecialchars($recurring['category_name'] ?? 'Uncategorized'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button onclick="editRecurringExpense(<?php echo $recurring['transaction_id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-800 font-medium flex items-center"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRecurringExpense(<?php echo $recurring['transaction_id']; ?>)" 
                                            class="text-red-600 hover:text-red-800 font-medium flex items-center"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div id="add-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Add New Expense</h3>
                <button type="button" onclick="closeAddExpenseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="add-expense-form" action="expensesQueries.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="is_recurring" id="expense-is-recurring" value="0">
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description *</label>
                    <input type="text" name="description" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                           placeholder="e.g., Grocery Shopping, Restaurant Dinner">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Amount (DH) *</label>
                    <input type="number" step="0.01" name="amount" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Category *</label>
                    <select name="category_id" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Payment Card *</label>
                    <div id="cards-container">
                        <?php foreach ($cards as $card): 
                            $card_balance = $card['card_total'];
                            $eligible = true;
                            $warning = '';
                            
                            if ($card_balance <= 0) {
                                $eligible = false;
                                $warning = ' - Insufficient balance';
                            }
                        ?>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg mb-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="card_id" value="<?php echo htmlspecialchars($card['card_id']); ?>" 
                                   class="h-4 w-4 text-red-600" required>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($card['card_name']); ?></div>
                                <div class="text-sm text-gray-500">Balance: <?php echo number_format($card['card_total'], 2); ?> DH</div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" name="date" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="expense-recurring" name="make_recurring" class="h-4 w-4 text-red-600 rounded" 
                           onchange="toggleRecurringOption()">
                    <label for="expense-recurring" class="ml-2 text-gray-700">Make this expense recurring (monthly)</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddExpenseModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Add Expense
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div id="edit-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Expense</h3>
                <button type="button" onclick="closeEditExpenseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 mb-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit-expense-id">
                <input type="number" step="0.01" name="new-amount" id="edit-expense-amount" placeholder="Amount" 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                <input type="text" name="new-description" id="edit-expense-description" placeholder="Expense Description" 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                
                <select name="new-category_id" id="edit-expense-category" 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="new-card_id" id="edit-expense-card" 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                    <option value="">Select Card</option>
                    <?php foreach ($cards as $card): ?>
                    <option value="<?php echo htmlspecialchars($card['card_id']); ?>">
                        <?php echo htmlspecialchars($card['card_name'] . ' (' . $card['bank_name'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="new-date" id="edit-expense-date" 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeEditExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="save" value="Save" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            </div>
        </form>
    </div>

    <!-- Edit Recurring Expense Modal -->
    <div id="edit-recurring-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Recurring Expense</h3>
                <button type="button" onclick="closeEditRecurringExpenseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-3 mb-4">
                <input type="hidden" name="action" value="edit_recurring">
                <input type="hidden" name="recurring_id" id="edit-recurring-id">
                <input type="number" step="0.01" name="new-amount" id="edit-recurring-amount" placeholder="Amount" 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                <input type="text" name="new-description" id="edit-recurring-description" placeholder="Expense Description" 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                
                <select name="new-category_id" id="edit-recurring-category" 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="new-card_id" id="edit-recurring-card" 
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                    <option value="">Select Card</option>
                    <?php foreach ($cards as $card): ?>
                    <option value="<?php echo htmlspecialchars($card['card_id']); ?>">
                        <?php echo htmlspecialchars($card['card_name'] . ' (' . $card['bank_name'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeEditRecurringExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="save" value="Save" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            </div>
        </form>
    </div>

    <!-- Delete Expense Modal -->
    <div id="delete-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Confirm Deletion</h3>
                <button type="button" onclick="closeDeleteExpenseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-2 mb-4">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="delete-id" id="delete-expense-id">
                <input type="number" step="0.01" id="delete-expense-amount" placeholder="Amount" disabled 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="text" id="delete-expense-description" placeholder="Description" disabled 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="date" id="delete-expense-date" disabled 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="delete" value="DELETE" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">
            </div>
        </form>
    </div>

    <!-- Delete Recurring Expense Modal -->
    <div id="delete-recurring-expense-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <form class="bg-white rounded-lg p-6 max-w-md w-full mx-4" action="expensesQueries.php" method="POST">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Delete Recurring Expense</h3>
                <button type="button" onclick="closeDeleteRecurringExpenseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-2 mb-4">
                <input type="hidden" name="action" value="delete_recurring">
                <input type="hidden" name="delete-recurring-id" id="delete-recurring-id">
                <input type="number" step="0.01" id="delete-recurring-amount" placeholder="Amount" disabled 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
                <input type="text" id="delete-recurring-description" placeholder="Description" disabled 
                       class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeDeleteRecurringExpenseModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <input type="submit" name="delete" value="DELETE" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">
            </div>
        </form>
    </div>

<script>
    let currentTab = 'all';
    
    // Modal functions
    function openAddExpenseModal(isRecurring = false) {
        const modal = document.getElementById('add-expense-modal');
        const title = document.getElementById('modal-title');
        const isRecurringInput = document.getElementById('expense-is-recurring');
        
        if (isRecurring) {
            title.textContent = 'Add Recurring Expense';
            isRecurringInput.value = '1';
            document.getElementById('expense-recurring').checked = true;
        } else {
            title.textContent = 'Add New Expense';
            isRecurringInput.value = '0';
            document.getElementById('expense-recurring').checked = false;
        }
        
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('add-expense-form').querySelector('input[name="date"]').value = today;
        
        modal.classList.remove('hidden');
    }
    
    function closeAddExpenseModal() {
        document.getElementById('add-expense-modal').classList.add('hidden');
        document.getElementById('add-expense-form').reset();
        document.getElementById('expense-is-recurring').value = '0';
    }
    
    function toggleRecurringOption() {
        const isRecurringInput = document.getElementById('expense-is-recurring');
        const dateInput = document.getElementById('add-expense-form').querySelector('input[name="date"]');
        const checkbox = document.getElementById('expense-recurring');
        
        if (checkbox.checked) {
            isRecurringInput.value = '1';
            
            dateInput.value = '';
            dateInput.required = false;
        } else {
            isRecurringInput.value = '0';
            
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
            dateInput.required = true;
        }
    }
    
    function openEditExpenseModal(expenseId) {
        
        
        document.getElementById('edit-expense-id').value = expenseId;
        
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('edit-expense-date').value = today;
        
        document.getElementById('edit-expense-modal').classList.remove('hidden');
    }
    
    function closeEditExpenseModal() {
        document.getElementById('edit-expense-modal').classList.add('hidden');
    }
    
    function editRecurringExpense(recurringId) {
        document.getElementById('edit-recurring-id').value = recurringId;
        document.getElementById('edit-recurring-expense-modal').classList.remove('hidden');
    }
    
    function closeEditRecurringExpenseModal() {
        document.getElementById('edit-recurring-expense-modal').classList.add('hidden');
    }
    
    function deleteExpense(expenseId) {
        document.getElementById('delete-expense-id').value = expenseId;
        document.getElementById('delete-expense-modal').classList.remove('hidden');
    }
    
    function closeDeleteExpenseModal() {
        document.getElementById('delete-expense-modal').classList.add('hidden');
    }
    
    function deleteRecurringExpense(recurringId) {
        document.getElementById('delete-recurring-id').value = recurringId;
        document.getElementById('delete-recurring-expense-modal').classList.remove('hidden');
    }
    
    function closeDeleteRecurringExpenseModal() {
        document.getElementById('delete-recurring-expense-modal').classList.add('hidden');
    }
    
    // Switch between tabs
    function switchTab(tab) {
        currentTab = tab;
        
        // Update tab styles
        document.getElementById('tab-all').classList.remove('border-red-500', 'text-red-600');
        document.getElementById('tab-all').classList.add('border-transparent', 'text-gray-500');
        document.getElementById('tab-recurring').classList.remove('border-red-500', 'text-red-600');
        document.getElementById('tab-recurring').classList.add('border-transparent', 'text-gray-500');
        
        document.getElementById('tab-' + tab).classList.add('border-red-500', 'text-red-600');
        document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
        
        // Show/hide sections
        if (tab === 'all') {
            document.getElementById('all-expenses-section').classList.remove('hidden');
            document.getElementById('recurring-expenses-section').classList.add('hidden');
        } else {
            document.getElementById('all-expenses-section').classList.add('hidden');
            document.getElementById('recurring-expenses-section').classList.remove('hidden');
        }
    }
    
    // Filter expenses
    function filterExpenses() {
        const categoryFilter = document.getElementById('filter-category').value;
        const cardFilter = document.getElementById('filter-card').value;
        
        const expenseElements = document.querySelectorAll('#expenses-list > div[data-expense-id]');
        
        expenseElements.forEach(element => {
            const category = element.dataset.category;
            const card = element.dataset.card;
            
            let show = true;
            
            if (categoryFilter && category !== categoryFilter) {
                show = false;
            }
            
            if (cardFilter && card !== cardFilter) {
                show = false;
            }
            
            if (show) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        });
    }
    
    // Auto-hide messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100');
        messages.forEach(message => {
            setTimeout(() => {
                message.style.display = 'none';
            }, 5000);
        });
        
        // Initialize tab
        switchTab('all');
    });
</script>
</body>
</html>
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
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

require 'connection.php';

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incomes - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .income-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        
        .page-link {
            cursor: pointer;
        }
        
        .page-link.active {
            background-color: #10b981;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="home.php" class="flex items-center">
                    <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></h1>
                </a>
            </div>
            <div class="space-x-4">
                <a href="home.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Home</a>
                <a href="incomes.php" class="bg-blue-600 text-white font-medium px-4 py-2 rounded transition-colors">Incomes</a>
                <a href="expenses.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Expenses</a>
                <a href="cards.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Cards</a>
                <a href="transfers.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Transfers</a>
                <a href="logout.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Income Management</h1>
                <p class="text-gray-600 mt-2">Track and manage your income sources</p>
            </div>
            <div class="flex items-center space-x-4">
                <?php
                
                $monthly_income = 0;
                $this_month = date('Y-m');
                $gettingMonthlyIncome = $pdo->prepare("
                    SELECT SUM(incomes.amount) as total 
                    FROM incomes 
                    LEFT JOIN cards ON incomes.card_id = cards.card_id 
                    WHERE cards.user_id = ? AND DATE_FORMAT(incomes.income_date, '%Y-%m') = ?
                ");
                $gettingMonthlyIncome->execute([$user_id, $this_month]);
                $monthly_data = $gettingMonthlyIncome->fetch(PDO::FETCH_ASSOC);
                if ($monthly_data && $monthly_data['total']) {
                    $monthly_income = $monthly_data['total'];
                }
                ?>
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-500">Monthly Income</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo number_format($monthly_income, 2); ?> DH</p>
                </div>
                <button onclick="openAddIncomeModal()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add Income
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php
            
            $gettingTotalIncomes = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM incomes 
                LEFT JOIN cards ON incomes.card_id = cards.card_id 
                WHERE cards.user_id = ?
            ");
            $gettingTotalIncomes->execute([$user_id]);
            $total_data = $gettingTotalIncomes->fetch(PDO::FETCH_ASSOC);
            $total_incomes = $total_data['total'];
            
            
            $gettingThisMonthCount = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM incomes 
                LEFT JOIN cards ON incomes.card_id = cards.card_id 
                WHERE cards.user_id = ? AND DATE_FORMAT(incomes.income_date, '%Y-%m') = ?
            ");
            $gettingThisMonthCount->execute([$user_id, $this_month]);
            $month_count_data = $gettingThisMonthCount->fetch(PDO::FETCH_ASSOC);
            $this_month_count = $month_count_data['count'];
            
            
            $avg_income = $this_month_count > 0 ? $monthly_income / $this_month_count : 0;
            ?>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Total Incomes</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $total_incomes; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">All time</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $this_month_count; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Income transactions</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Avg. per Income</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo number_format($avg_income, 2); ?> DH</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Monthly average</div>
            </div>
        </div>

        <!-- Incomes List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">All Incomes</h2>
            </div>
            
            <div id="incomes-list" class="divide-y divide-gray-200">
                <?php
                
                $gettingIncomes = $pdo->prepare("
                    SELECT incomes.*, cards.card_name, cards.bank_name 
                    FROM incomes 
                    LEFT JOIN cards ON incomes.card_id = cards.card_id 
                    WHERE cards.user_id = ? 
                    ORDER BY incomes.income_date DESC, incomes.id DESC
                ");
                $gettingIncomes->execute([$user_id]);
                $incomes = $gettingIncomes->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($incomes) > 0) {
                    foreach ($incomes as $income) {
                        $date = new DateTime($income['income_date']);
                        $formattedDate = $date->format('M d, Y');
                        ?>
                        <div class="p-6 hover:bg-gray-50 transition-colors fade-in" 
                             data-id="<?php echo $income['id']; ?>"
                             data-amount="<?php echo $income['amount']; ?>"
                             data-description="<?php echo htmlspecialchars($income['description']); ?>"
                             data-card-id="<?php echo $income['card_id']; ?>"
                             data-date="<?php echo $income['income_date']; ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg mb-1"><?php echo htmlspecialchars($income['description']); ?></h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?php echo $formattedDate; ?>
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-credit-card mr-1"></i>
                                                <?php echo htmlspecialchars($income['card_name']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600">
                                            +<?php echo number_format($income['amount'], 2); ?> DH
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            One-time
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <button onclick="editIncome(this)" 
                                                class="text-blue-600 hover:text-blue-800 font-medium flex items-center"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteIncome(this)" 
                                                class="text-red-600 hover:text-red-800 font-medium flex items-center"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-money-bill-wave text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Incomes Yet</h3>
                        <p class="text-gray-600 mb-6">Add your first income to start tracking</p>
                        <button onclick="openAddIncomeModal()" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-3 rounded-lg inline-flex items-center transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Your First Income
                        </button>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Add Income Modal -->
    <div id="add-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Add New Income</h3>
                <button onclick="closeAddIncomeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="incomesQueries.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description *</label>
                    <input type="text" name="description" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                           placeholder="e.g., Monthly Salary, Freelance Project">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Amount (DH) *</label>
                    <input type="number" step="0.01" name="amount" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Destination Card *</label>
                    <select name="card_id" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        <option value="">Select Card</option>
                        <?php
                        
                        $gettingCards = $pdo->prepare("SELECT * FROM cards WHERE user_id = ?");
                        $gettingCards->execute([$user_id]);
                        $cards = $gettingCards->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($cards as $card) {
                            echo '<option value="' . $card['card_id'] . '">' . htmlspecialchars($card['card_name']) . ' (' . htmlspecialchars($card['bank_name']) . ')</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" name="income_date" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddIncomeModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Add Income
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Income Modal -->
    <div id="edit-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Edit Income</h3>
                <button onclick="closeEditIncomeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="incomesQueries.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit-income-id">
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description *</label>
                    <input type="text" name="description" id="edit-income-description" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                           placeholder="e.g., Monthly Salary, Freelance Project">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Amount (DH) *</label>
                    <input type="number" step="0.01" name="amount" id="edit-income-amount" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Destination Card *</label>
                    <select name="card_id" id="edit-income-card" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        <option value="">Select Card</option>
                        <?php
                        
                        $gettingCards = $pdo->prepare("SELECT * FROM cards WHERE user_id = ?");
                        $gettingCards->execute([$user_id]);
                        $cards = $gettingCards->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($cards as $card) {
                            echo '<option value="' . $card['card_id'] . '">' . htmlspecialchars($card['card_name']) . ' (' . htmlspecialchars($card['bank_name']) . ')</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" name="income_date" id="edit-income-date" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditIncomeModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Update Income
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-income-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Income</h3>
                <p class="text-gray-600">Are you sure you want to delete this income?</p>
                <p class="text-sm text-gray-500 mt-2" id="delete-income-details"></p>
            </div>
            
            <div class="space-y-4">
                <form id="delete-income-form" action="incomesQueries.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-income-id">
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteIncomeModal()" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                            Delete Income
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddIncomeModal() {
            document.getElementById('add-income-modal').classList.remove('hidden');
        }
        
        function closeAddIncomeModal() {
            document.getElementById('add-income-modal').classList.add('hidden');
        }
        
        function openEditIncomeModal() {
            document.getElementById('edit-income-modal').classList.remove('hidden');
        }
        
        function closeEditIncomeModal() {
            document.getElementById('edit-income-modal').classList.add('hidden');
        }
        
        function openDeleteIncomeModal() {
            document.getElementById('delete-income-modal').classList.remove('hidden');
        }
        
        function closeDeleteIncomeModal() {
            document.getElementById('delete-income-modal').classList.add('hidden');
        }
        
        // Edit income 
        function editIncome(button) {
            const incomeDiv = button.closest('div[data-id]');
            
            const id = incomeDiv.dataset.id;
            const description = incomeDiv.dataset.description;
            const amount = incomeDiv.dataset.amount;
            const cardId = incomeDiv.dataset.cardId;
            const date = incomeDiv.dataset.date;
            
            // Fill the edit form
            document.getElementById('edit-income-id').value = id;
            document.getElementById('edit-income-description').value = description;
            document.getElementById('edit-income-amount').value = amount;
            document.getElementById('edit-income-card').value = cardId;
            document.getElementById('edit-income-date').value = date;
            
            openEditIncomeModal();
        }
        
        // Delete income 
        function deleteIncome(button) {
            const incomeDiv = button.closest('div[data-id]');
            
            const id = incomeDiv.dataset.id;
            const description = incomeDiv.dataset.description;
            const amount = incomeDiv.dataset.amount;
            
            
            document.getElementById('delete-income-id').value = id;
            document.getElementById('delete-income-details').textContent = 
                `${description} - ${parseFloat(amount).toFixed(2)} DH`;
            
            openDeleteIncomeModal();
        }
    </script>
</body>
</html>

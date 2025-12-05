<?php  

    require 'connection.php';

    $expenses_selection= $pdo->prepare("SELECT * FROM expenses");
    $expenses_selection->execute();
    $expenses= $expenses_selection->fetchAll(PDO::FETCH_ASSOC);

    $incomes_selection= $pdo->prepare("SELECT * FROM incomes");
    $incomes_selection->execute();
    $incomes= $incomes_selection->fetchAll(PDO::FETCH_ASSOC);

    $totalIncomes_selection= $pdo->prepare("SELECT SUM(amount) AS total_incomes FROM incomes");
    $totalIncomes_selection->execute();
    $totalIncomes= $totalIncomes_selection->fetchColumn();
    
    $totalExpenses_selection= $pdo->prepare("SELECT SUM(amount) AS total_expenses FROM expenses");
    $totalExpenses_selection->execute();
    $totalExpenses= $totalExpenses_selection->fetchColumn();

    $incomes_sorted_selection= $pdo->prepare("SELECT *, 'income' as type FROM incomes WHERE YEAR(income_date)=YEAR(CURDATE()) AND MONTH(income_date)=MONTH(CURDATE())");
    $incomes_sorted_selection->execute();
    $sorted_incomes= $incomes_sorted_selection->fetchAll(PDO::FETCH_ASSOC);

    $expenses_sorted_selection= $pdo->prepare("SELECT *, 'expense' as type FROM expenses WHERE YEAR(expense_date)=YEAR(CURDATE()) AND MONTH(expense_date)=MONTH(CURDATE())");
    $expenses_sorted_selection->execute();
    $sorted_expenses= $expenses_sorted_selection->fetchAll(PDO::FETCH_ASSOC);

    $exchanges = array_merge($sorted_incomes, $sorted_expenses);
    
    usort($exchanges, function($a, $b) {
        $dateA = $a['type'] === 'income' ? $a['income_date'] : $a['expense_date'];
        $dateB = $b['type'] === 'income' ? $b['income_date'] : $b['expense_date'];
        return strtotime($dateB) - strtotime($dateA);
    });

    $monthly_data = [];
    for($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));

        $income_query = $pdo->prepare("SELECT SUM(amount) as total FROM incomes WHERE DATE_FORMAT(income_date, '%Y-%m') = ?");
        $income_query->execute([$month]);
        $income_total = $income_query->fetchColumn() ?: 0;
        
        
        $expense_query = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?");
        $expense_query->execute([$month]);
        $expense_total = $expense_query->fetchColumn() ?: 0;
        
        $monthly_data[] = [
            'month' => $month_name,
            'income' => $income_total,
            'expense' => $expense_total
        ];
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Wallet - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Smart Wallet</h1>
            <div class="space-x-4">
                <a href="#" class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
                <a href="incomes.php" class="hover:bg-blue-700 px-3 py-2 rounded">Incomes</a>
                <a href="expenses.php" class="hover:bg-blue-700 px-3 py-2 rounded">Expenses</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Income</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="totalIncome">
                            $<?php echo number_format($totalIncomes, 2) ?>
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Expenses</p>
                        <p class="text-3xl font-bold text-red-600 mt-2" id="totalExpenses">
                            $<?php echo number_format($totalExpenses, 2) ?>
                        </p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Account Balance</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="balance">
                            $<?php 
                                $balance= $totalIncomes-$totalExpenses;
                                echo number_format($balance, 2);
                            ?>
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Income vs Expenses (Last 6 Months)</h2>
                <div class="relative h-64 flex items-center justify-center">
                    <?php if(empty($monthly_data)): ?>
                        <p class="text-gray-500">No data available</p>
                    <?php else: ?>
                        <canvas id="incomeExpenseChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Current Month Transactions</h2>
                <div class="overflow-y-auto max-h-80" id="transactionsList">
                    <?php if(empty($exchanges)): ?>
                        <p class="text-gray-500 text-center py-8">No transactions this month</p>
                    <?php else: ?>
                        <?php foreach($exchanges as $exchange): ?>
                            <?php 
                                $isIncome = $exchange['type'] === 'income';
                                $date = $isIncome ? $exchange['income_date'] : $exchange['expense_date'];
                                $formattedDate = date('M d, Y', strtotime($date));
                            ?>
                            <div class="flex items-center justify-between py-3 border-b border-gray-200 hover:bg-gray-50 px-2 rounded">
                                <div class="flex items-center space-x-3">
                                    <div class="<?php echo $isIncome ? 'bg-green-100' : 'bg-red-100'; ?> rounded-full p-2">
                                        <svg class="w-4 h-4 <?php echo $isIncome ? 'text-green-600' : 'text-red-600'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $isIncome ? 'M12 4v16m8-8H4' : 'M20 12H4'; ?>"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($exchange['description']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo $isIncome ? 'Income' : 'Expense'; ?> • <?php echo $formattedDate; ?></p>
                                    </div>
                                </div>
                                <span class="font-bold <?php echo $isIncome ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $isIncome ? '+' : '-'; ?>$<?php echo number_format($exchange['amount'], 2); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if(!empty($monthly_data)): ?>
        
        const monthlyData = <?php echo json_encode($monthly_data); ?>;
        
        const chartData = {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Income',
                    data: monthlyData.map(item => item.income),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Expenses',
                    data: monthlyData.map(item => item.expense),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        };

        
        const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toFixed(2);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>

    <script src="main.js"></script>
</body>
</html>
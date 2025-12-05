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
                <a href="#"  class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
                <a href="incomes.php"  class="hover:bg-blue-700 px-3 py-2 rounded">Incomes</a>
                <a href="expenses.php" class="hover:bg-blue-700 px-3 py-2 rounded">Expenses</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div id="home-page" class="page-content">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-3xl font-bold mb-4 text-gray-800">Welcome to Finance Tracker</h2>
                <p class="text-gray-600 mb-6">Manage your incomes and expenses efficiently. Track your financial activities and maintain a clear record of all transactions.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-green-50 p-6 rounded-lg border-2 border-green-200">
                        <h3 class="text-xl font-semibold mb-2 text-green-700">Incomes</h3>
                        <p class="text-gray-600 mb-4">Track all your income sources and see a summary of your earnings.</p>
                        <a href="incomes.php"><button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">View Incomes</button></a>
                    </div>
                    <div class="bg-red-50 p-6 rounded-lg border-2 border-red-200">
                        <h3 class="text-xl font-semibold mb-2 text-red-700">Expenses</h3>
                        <p class="text-gray-600 mb-4">Monitor your spending and categorize your expenses.</p>
                        <a href="expenses.php"><button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">View Expenses</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="main.js"></script>
</body>
</html>
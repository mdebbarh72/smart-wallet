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
        <!-- Signup Page -->
        <div id="signup-page" class="page-content hidden">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Sign Up</h2>
                <div class="space-y-4">
                    <input type="text" placeholder="Full Name" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <input type="email" placeholder="Email" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <input type="password" placeholder="Password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <input type="password" placeholder="Confirm Password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Sign Up</button>
                    <p class="text-center text-gray-600">Already have an account? <a href="#" onclick="loadPage('login')" class="text-blue-600 hover:underline">Login</a></p>
                </div>
            </div>
        </div>
    </div>
<script src="main.js"></script>
</body>
</html>
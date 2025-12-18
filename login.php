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

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .login-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                <h1 class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></h1>
            </div>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">landing</a>
                <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded transition-colors">Login</a>
                <a href="signup.php" class="bg-gray-900 hover:bg-black text-white font-medium px-4 py-2 rounded transition-colors">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Column - Branding -->
            <div class="text-center lg:text-left">
                <div class="login-gradient rounded-2xl p-8 md:p-12 shadow-2xl">
                    <div class="mb-8">
                        <i class="fas fa-wallet text-white text-5xl mb-4"></i>
                        <h2 class="text-4xl font-bold text-white mb-4">Welcome Back to Smart Wallet</h2>
                        <p class="text-blue-100 text-lg">Track your finances, achieve your goals, and make smarter money decisions.</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Income Tracking</h4>
                                <p class="text-sm text-blue-100">Monitor all your income sources</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Expense Management</h4>
                                <p class="text-sm text-blue-100">Control and categorize spending</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Visual Analytics</h4>
                                <p class="text-sm text-blue-100">Beautiful charts and insights</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 card-hover">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Login to Your Account</h2>
                    <p class="text-gray-600">Enter your credentials to access your dashboard</p>
                </div>
                
                <form class="space-y-6" action="loginvalidation.php" method="POST">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="email">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" placeholder="you@example.com" name="email"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="password">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" placeholder="Enter your password" name="password"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-gray-700">Remember me</label>
                        </div>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Forgot password?</a>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login to Dashboard
                    </button>
                    
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" 
                                class="flex items-center justify-center py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                        </button>
                        <button type="button" 
                                class="flex items-center justify-center py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fab fa-apple text-gray-800 mr-2"></i> Apple
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-600">Don't have an account? 
                        <a href="signup.php" class="text-blue-600 hover:text-blue-800 font-bold ml-1">Sign up now</a>
                    </p>
                    <p class="text-gray-500 text-sm mt-4">By logging in, you agree to our <a href="#" class="text-blue-600">Terms</a> and <a href="#" class="text-blue-600">Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.fa-eye').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
    </script>
</body>
</html>

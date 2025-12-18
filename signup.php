
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .signup-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .progress-bar {
            transition: width 0.3s ease;
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
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Landing</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Login</a>
                <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded transition-colors">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Column - Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 card-hover">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h2>
                    <p class="text-gray-600">Start your journey to financial freedom today</p>
                    
                    <!-- Progress Bar -->
                    <div class="mt-6 mb-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="password-strength" class="bg-red-500 h-2 rounded-full progress-bar" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Weak</span>
                            <span>Strong</span>
                        </div>
                    </div>
                </div>
                
                <form class="space-y-6" id="signup-form" action="signupvalidation.php" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="first-name">First Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="first-name" placeholder="John" name="fname"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="last-name">Last Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="last-name" placeholder="Doe" name="lname"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none" required>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="signup-email">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="signup-email" placeholder="you@example.com"  name="email"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="signup-password">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="signup-password" placeholder="Create a strong password"  name="password"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        <div id="password-requirements" class="mt-2 space-y-1">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                <span class="text-gray-600">At least 8 characters</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                <span class="text-gray-600">Contains uppercase & lowercase</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-circle text-gray-300 mr-2 text-xs"></i>
                                <span class="text-gray-600">Includes a number or special character</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="confirm-password">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="confirm-password" placeholder="Confirm your password" name="confirm_password"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors outline-none" required>
                        </div>
                        <div id="password-match" class="mt-2 text-sm hidden">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            <span class="text-green-600">Passwords match!</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                        <label for="terms" class="ml-2 text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="newsletter" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="newsletter" class="ml-2 text-gray-700">
                            Send me financial tips and updates
                        </label>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </button>
                    
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or sign up with</span>
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
                    <p class="text-gray-600">Already have an account? 
                        <a href="login.php" class="text-blue-600 hover:text-blue-800 font-bold ml-1">Login here</a>
                    </p>
                </div>
            </div>
            
            <!-- Right Column - Benefits -->
            <div>
                <div class="signup-gradient rounded-2xl p-8 md:p-12 shadow-2xl text-white">
                    <div class="mb-8">
                        <i class="fas fa-rocket text-white text-5xl mb-4"></i>
                        <h2 class="text-4xl font-bold mb-4">Start Your Financial Journey</h2>
                        <p class="text-indigo-100 text-lg">Join thousands who have taken control of their finances with Smart Wallet.</p>
                    </div>
                    
                    <div class="space-y-8">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-chart-bar text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl mb-2">Instant Insights</h4>
                                <p class="text-indigo-100">Get real-time insights into your spending patterns and financial health.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl mb-2">Bank-Level Security</h4>
                                <p class="text-indigo-100">Your financial data is protected with enterprise-grade encryption.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-mobile-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl mb-2">Access Anywhere</h4>
                                <p class="text-indigo-100">Manage your finances from any device, anywhere in the world.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-12 pt-8 border-t border-white/20">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Free 30-Day Trial</h4>
                                <p class="text-sm text-indigo-100">No credit card required to start</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.fa-eye').addEventListener('click', function() {
            const passwordInput = document.getElementById('signup-password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password strength checker
        const passwordInput = document.getElementById('signup-password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        const passwordStrength = document.getElementById('password-strength');
        const passwordMatch = document.getElementById('password-match');
        const passwordRequirements = document.getElementById('password-requirements').children;
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check length
            if (password.length >= 8) {
                strength += 25;
                updateRequirement(0, true);
            } else {
                updateRequirement(0, false);
            }
            
            // Check uppercase and lowercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                strength += 25;
                updateRequirement(1, true);
            } else {
                updateRequirement(1, false);
            }
            
            // Check numbers or special characters
            if (/\d/.test(password) || /[^A-Za-z0-9]/.test(password)) {
                strength += 25;
                updateRequirement(2, true);
            } else {
                updateRequirement(2, false);
            }
            
            // Check for repeated patterns
            if (password.length > 10) strength += 25;
            
            // Update progress bar
            passwordStrength.style.width = strength + '%';
            
            // Update color based on strength
            if (strength < 50) {
                passwordStrength.className = 'bg-red-500 h-2 rounded-full progress-bar';
            } else if (strength < 75) {
                passwordStrength.className = 'bg-yellow-500 h-2 rounded-full progress-bar';
            } else {
                passwordStrength.className = 'bg-green-500 h-2 rounded-full progress-bar';
            }
            
            // Check password match
            checkPasswordMatch();
        });
        
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            if (confirmPasswordInput.value && passwordInput.value === confirmPasswordInput.value) {
                passwordMatch.classList.remove('hidden');
            } else {
                passwordMatch.classList.add('hidden');
            }
        }
        
        function updateRequirement(index, met) {
            const icon = passwordRequirements[index].querySelector('i');
            const text = passwordRequirements[index].querySelector('span');
            
            if (met) {
                icon.className = 'fas fa-check-circle text-green-500 mr-2 text-xs';
                text.className = 'text-green-600';
            } else {
                icon.className = 'fas fa-circle text-gray-300 mr-2 text-xs';
                text.className = 'text-gray-600';
            }
        }
        
    </script>
</body>
</html>

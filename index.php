<?php  
$sessionLifetime = 60 * 60 * 24;

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => '/',
    'secure'   => false, // true on HTTPS
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
    <title>Smart Wallet - Manage Your Finances Intelligently</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-bg {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .slider-container {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                        <span class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></span>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Features</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Testimonials</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">About</a>
                    <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition-colors">Login</a>
                    <a href="signup.php" class="bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-lg font-medium transition-colors">Sign Up</a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" type="button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden bg-white py-4 px-2 shadow-lg rounded-lg mt-2">
                <div class="flex flex-col space-y-4">
                    <a href="landing.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded hover:bg-gray-100">Home</a>
                    <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded hover:bg-gray-100">Features</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded hover:bg-gray-100">Testimonials</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded hover:bg-gray-100">About</a>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="login.php" class="block bg-blue-600 hover:bg-blue-700 text-white text-center px-5 py-3 rounded-lg font-medium mb-3">Login</a>
                        <a href="signup.php" class="block bg-gray-900 hover:bg-black text-white text-center px-5 py-3 rounded-lg font-medium">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-12 md:mb-0">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">Take Control of Your <span class="text-yellow-300">Financial Future</span></h1>
                    <p class="text-xl mb-8 text-blue-100 max-w-lg">Smart Wallet helps you track income, manage expenses, and achieve your financial goals with powerful insights and easy-to-use tools.</p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="signup.php" class="bg-white text-blue-600 hover:bg-gray-100 font-bold px-8 py-4 rounded-lg text-lg text-center transition-colors">Get Started Free</a>
                        <a href="#features" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-bold px-8 py-4 rounded-lg text-lg text-center transition-colors">Learn More</a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="relative">
                        <div class="absolute -top-6 -left-6 w-64 h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
                        <div class="absolute -bottom-6 -right-6 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-white/20">
                            <img src="stocks.png" alt="Finance Dashboard" class="rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">10K+</div>
                    <div class="text-gray-600">Active Users</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-600 mb-2">$5M+</div>
                    <div class="text-gray-600">Tracked Monthly</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-600 mb-2">98%</div>
                    <div class="text-gray-600">User Satisfaction</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Powerful Features for <span class="text-blue-600">Smart Finance</span></h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Everything you need to manage your finances effectively in one place</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Income Tracking</h3>
                    <p class="text-gray-600 mb-6">Easily log and categorize all your income sources with detailed analytics and reporting.</p>
                    <a href="incomes.php" class="text-blue-600 font-medium flex items-center">Explore <i class="fas fa-arrow-right ml-2"></i></a>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
                    <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-receipt text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Expense Management</h3>
                    <p class="text-gray-600 mb-6">Track every expense, set budgets, and get insights on your spending patterns.</p>
                    <a href="expenses.php" class="text-blue-600 font-medium flex items-center">Explore <i class="fas fa-arrow-right ml-2"></i></a>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Visual Analytics</h3>
                    <p class="text-gray-600 mb-6">Beautiful charts and graphs to visualize your financial data and trends over time.</p>
                    <a href="index.php" class="text-blue-600 font-medium flex items-center">Explore <i class="fas fa-arrow-right ml-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Slider Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How <span class="text-blue-600">Smart Wallet</span> Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Simple steps to achieve financial clarity</p>
            </div>
            
            <div class="slider-container overflow-x-auto flex space-x-6 pb-6" style="scrollbar-width: none;">
                <div class="flex-none w-80 md:w-96 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6">1</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sign Up & Connect</h3>
                    <p class="text-gray-600 mb-6">Create your free account in seconds. No credit card required to start.</p>
                    <div class="flex justify-center">
                        <div class="bg-white rounded-xl p-4 shadow-md">
                            <i class="fas fa-user-plus text-4xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="flex-none w-80 md:w-96 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8">
                    <div class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6">2</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Add Transactions</h3>
                    <p class="text-gray-600 mb-6">Log your income and expenses with our simple, intuitive interface.</p>
                    <div class="flex justify-center">
                        <div class="bg-white rounded-xl p-4 shadow-md">
                            <i class="fas fa-exchange-alt text-4xl text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="flex-none w-80 md:w-96 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8">
                    <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6">3</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Analyze & Optimize</h3>
                    <p class="text-gray-600 mb-6">Get insights, set budgets, and make smarter financial decisions.</p>
                    <div class="flex justify-center">
                        <div class="bg-white rounded-xl p-4 shadow-md">
                            <i class="fas fa-chart-bar text-4xl text-purple-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="flex-none w-80 md:w-96 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-8">
                    <div class="w-12 h-12 bg-orange-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-6">4</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Achieve Your Goals</h3>
                    <p class="text-gray-600 mb-6">Watch your savings grow and reach your financial milestones faster.</p>
                    <div class="flex justify-center">
                        <div class="bg-white rounded-xl p-4 shadow-md">
                            <i class="fas fa-trophy text-4xl text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center mt-8 space-x-4">
                <button id="slider-prev" class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="slider-next" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonial-bg py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">What Our <span class="text-yellow-300">Users Say</span></h2>
                <p class="text-xl max-w-3xl mx-auto opacity-90">Join thousands of satisfied users who transformed their finances</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold mr-4">JS</div>
                        <div>
                            <h4 class="font-bold text-xl">John Smith</h4>
                            <p class="opacity-80">Freelance Designer</p>
                        </div>
                    </div>
                    <p class="mb-6">"Smart Wallet helped me save 30% more each month by showing me exactly where my money was going. The expense tracking is brilliant!"</p>
                    <div class="flex text-yellow-300">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold mr-4">SR</div>
                        <div>
                            <h4 class="font-bold text-xl">Sarah Rodriguez</h4>
                            <p class="opacity-80">Small Business Owner</p>
                        </div>
                    </div>
                    <p class="mb-6">"As a business owner, keeping track of finances was overwhelming. Smart Wallet made it simple and actually enjoyable!"</p>
                    <div class="flex text-yellow-300">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold mr-4">MT</div>
                        <div>
                            <h4 class="font-bold text-xl">Michael Thompson</h4>
                            <p class="opacity-80">Financial Analyst</p>
                        </div>
                    </div>
                    <p class="mb-6">"I've tried many finance apps, but Smart Wallet's analytics and reporting features are by far the best for serious financial management."</p>
                    <div class="flex text-yellow-300">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-900 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Transform Your <span class="text-blue-400">Financial Life</span>?</h2>
            <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">Join thousands of users who have taken control of their finances with Smart Wallet. It's free to start!</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="signup.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-10 py-4 rounded-lg text-lg transition-colors">Start Free Trial</a>
                <a href="login.php" class="bg-transparent border-2 border-gray-600 hover:border-gray-500 text-white font-bold px-10 py-4 rounded-lg text-lg transition-colors">Login to Dashboard</a>
            </div>
            <p class="text-gray-400 mt-6">No credit card required • 30-day free trial • Cancel anytime</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center mb-6">
                        <i class="fas fa-wallet text-blue-400 text-2xl mr-2"></i>
                        <span class="text-2xl font-bold">Smart<span class="text-blue-400">Wallet</span></span>
                    </div>
                    <p class="text-gray-400 mb-6">Making personal finance management simple, intuitive, and effective for everyone.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition-colors">Login</a></li>
                        <li><a href="signup.php" class="text-gray-400 hover:text-white transition-colors">Sign Up</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition-colors">Incomes</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition-colors">Expenses</a></li>
                    </ul>
                </div>
                
                <!-- Resources -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Resources</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-blue-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">support@smartwallet.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-blue-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-blue-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">123 Finance Street, San Francisco, CA</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">© 2023 Smart Wallet. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Slider functionality
        const slider = document.querySelector('.slider-container');
        const prevBtn = document.getElementById('slider-prev');
        const nextBtn = document.getElementById('slider-next');
        
        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: 300, behavior: 'smooth' });
        });
        
        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -300, behavior: 'smooth' });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const mobileMenu = document.getElementById('mobile-menu');
                    if(!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });

        // Add animation delay class
        document.head.insertAdjacentHTML('beforeend', '<style>.animation-delay-2000 { animation-delay: 2s; }</style>');
    </script>
</body>
</html>
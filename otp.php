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
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .otp-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .otp-input {
            width: 3.5rem !important;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .otp-input.filled {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        
        .otp-input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .checkmark-animation {
            animation: checkmark 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center">
                    <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></h1>
                </a>
            </div>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Home</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Login</a>
                <a href="signup.php" class="bg-gray-900 hover:bg-black text-white font-medium px-4 py-2 rounded transition-colors">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Column - Illustration & Info -->
            <div class="text-center lg:text-left">
                <div class="otp-gradient rounded-2xl p-8 md:p-12 shadow-2xl">
                    <div class="mb-8">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-6">
                            <i class="fas fa-shield-alt text-white text-3xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-4">Secure Verification</h2>
                        <p class="text-blue-100 text-lg">We've sent a 6-digit verification code to protect your account security.</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Mobile Security</h4>
                                <p class="text-sm text-blue-100">Code sent to your registered device</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Time Limited</h4>
                                <p class="text-sm text-blue-100">Code expires in 10 minutes</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center text-white">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h4 class="font-bold">Bank-Level Security</h4>
                                <p class="text-sm text-blue-100">Enterprise-grade encryption</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-white/20">
                        <p class="text-blue-100 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Never share your OTP with anyone. Smart Wallet will never ask for your verification code.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - OTP Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 card-hover">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-key text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Verify Your Identity</h2>
                    <p class="text-gray-600 mb-1">Enter the 6-digit code sent to</p>
                    <p class="text-gray-900 font-semibold mb-6" id="user-email">user@example.com</p>
                    
                    <div class="inline-flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-lg mb-6">
                        <i class="fas fa-envelope mr-2"></i>
                        <span id="timer">Code expires in <span id="countdown">10:00</span></span>
                    </div>
                </div>
                
                <form id="otp-form" class="space-y-8" action="OTPverification.php" method="POST">
                    <!-- OTP Inputs -->
                    <div class="flex justify-center space-x-3 mb-2" id="otp-container">
                        <input type="text" maxlength="1" name="1" class="otp-input" data-index="0" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" name="2" class="otp-input" data-index="1" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" name="3" class="otp-input" data-index="2" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" name="4" class="otp-input" data-index="3" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" name="5" class="otp-input" data-index="4" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" name="6" class="otp-input" data-index="5" inputmode="numeric" pattern="[0-9]*">
                    </div>
                    
                    <input type="hidden" id="full-otp" name="otp">
                    
                    <!-- Error Message
                    <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="error-text">Invalid OTP. Please try again.</span>
                        </div>
                    </div> -->
                    
                    <!-- Success Message -->
                    <!-- <div id="success-message" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span id="success-text">OTP verified successfully!</span>
                        </div>
                    </div> -->
                    
                    <!-- <div class="flex items-center justify-center">
                        <div class="flex items-center">
                            <input type="checkbox" id="trust-device" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="trust-device" class="ml-2 text-gray-700">Trust this device for 30 days</label>
                        </div>
                    </div> -->
                    
                    <button type="submit" id="verify-btn" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check-circle mr-2"></i> Verify & Continue
                    </button>
                    
                    <!-- <div class="text-center space-y-4">
                        <div>
                            <p class="text-gray-600">Didn't receive the code?</p>
                            <div class="flex justify-center space-x-4 mt-2">
                                <button type="button" id="resend-btn" 
                                        class="text-blue-600 hover:text-blue-800 font-medium disabled:text-gray-400 disabled:cursor-not-allowed">
                                    <i class="fas fa-redo-alt mr-1"></i> Resend OTP
                                </button>
                                <button type="button" id="call-btn" 
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-phone-alt mr-1"></i> Call Me
                                </button>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-gray-600">Having trouble?</p>
                            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center mt-1">
                                <i class="fas fa-question-circle mr-1"></i> Get Help
                            </a>
                        </div>
                    </div> -->
                </form>
                
                <div class="mt-8 text-center">
                    <a href="login.php" class="text-gray-600 hover:text-gray-800 font-medium inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <!-- <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2" id="loading-title">Verifying OTP</h3>
            <p class="text-gray-600" id="loading-message">Please wait while we verify your code...</p>
        </div>
    </div> -->

    <!-- Success Modal -->
    <!-- <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 checkmark-animation">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Verification Successful!</h3>
            <p class="text-gray-600 mb-6">Your identity has been verified successfully.</p>
            <a href="index.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                Go to Dashboard
            </a>
        </div>
    </div> -->

    <script>
        
        // OTP Configuration
        const OTP_EXPIRY_TIME = 600; // 10 minutes in seconds
        const RESEND_COOLDOWN = 60; // 60 seconds cooldown for resend
        let timer = OTP_EXPIRY_TIME;
        let resendTimer = RESEND_COOLDOWN;
        let countdownInterval;
        let resendInterval;
        
        
        // DOM Elements
        const otpInputs = document.querySelectorAll('.otp-input');
        const fullOtpInput = document.getElementById('full-otp');
        // const verifyBtn = document.getElementById('verify-btn');
        // const resendBtn = document.getElementById('resend-btn');
        // const callBtn = document.getElementById('call-btn');
        // const errorMessage = document.getElementById('error-message');
        // const successMessage = document.getElementById('success-message');
        // const loadingOverlay = document.getElementById('loading-overlay');
        // const successModal = document.getElementById('success-modal');
        const countdownElement = document.getElementById('countdown');
        const timerElement = document.getElementById('timer');
        
        // Initialize OTP input functionality
        function initOTPInputs() {
            otpInputs.forEach((input, index) => {
                // Handle input
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    // If a number is entered, move to next input
                    if (value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    
                    // Update styling
                    updateInputStyles();
                    
                    // Update hidden full OTP
                    updateFullOTP();
                });
                
                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');
                    if (/^\d{6}$/.test(pastedData)) {
                        pasteOTP(pastedData);
                    }
                });
                
                // Handle backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });
            
            // Focus first input on load
            otpInputs[0].focus();
        }
        
        // Paste OTP into all inputs
        function pasteOTP(otp) {
            const digits = otp.split('');
            otpInputs.forEach((input, index) => {
                if (index < digits.length) {
                    input.value = digits[index];
                    input.classList.add('filled');
                    input.classList.remove('error');
                }
            });
            
            // Focus last input
            if (otpInputs[digits.length - 1]) {
                otpInputs[digits.length - 1].focus();
            }
            
            updateFullOTP();
        }
        
        // Update input styling based on content
        function updateInputStyles() {
            otpInputs.forEach(input => {
                if (input.value) {
                    input.classList.add('filled');
                    input.classList.remove('error');
                } else {
                    input.classList.remove('filled');
                }
            });
        }
        
        // Update hidden full OTP field
        function updateFullOTP() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            fullOtpInput.value = otp;
            
            // Enable/disable verify button
            verifyBtn.disabled = otp.length !== 6;
        }
        
        // Start countdown timer
        function startCountdown() {
            clearInterval(countdownInterval);
            
            countdownInterval = setInterval(() => {
                const minutes = Math.floor(timer / 60);
                const seconds = timer % 60;
                
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Update timer styling when less than 1 minute
                if (timer <= 60) {
                    timerElement.classList.add('text-red-600');
                    countdownElement.classList.add('font-bold');
                }
                
                if (timer <= 0) {
                    clearInterval(countdownInterval);
                    countdownElement.textContent = '00:00';
                    showError('OTP has expired. Please request a new code.');
                    disableOTPInputs();
                } else {
                    timer--;
                }
            }, 1000);
        }
        
        // Start resend cooldown
        // function startResendCooldown() {
        //     resendBtn.disabled = true;
        //     clearInterval(resendInterval);
            
        //     resendInterval = setInterval(() => {
        //         if (resendTimer <= 0) {
        //             clearInterval(resendInterval);
        //             resendBtn.disabled = false;
        //             resendBtn.innerHTML = '<i class="fas fa-redo-alt mr-1"></i> Resend OTP';
        //             resendTimer = RESEND_COOLDOWN;
        //         } else {
        //             resendBtn.innerHTML = `<i class="fas fa-redo-alt mr-1"></i> Resend (${resendTimer}s)`;
        //             resendTimer--;
        //         }
        //     }, 1000);
        // }
        
        // Disable OTP inputs
        function disableOTPInputs() {
            otpInputs.forEach(input => {
                input.disabled = true;
                input.classList.add('opacity-50');
            });
            verifyBtn.disabled = true;
        }
        
        // Enable OTP inputs
        function enableOTPInputs() {
            otpInputs.forEach(input => {
                input.disabled = false;
                input.classList.remove('opacity-50');
            });
        }
        
        // Show error message
        function showError(message) {
            errorMessage.classList.remove('hidden');
            document.getElementById('error-text').textContent = message;
            successMessage.classList.add('hidden');
            
            // Add shake animation to OTP inputs
            otpInputs.forEach(input => {
                input.classList.add('shake', 'error');
                input.classList.remove('filled');
            });
            
            // Clear OTP after error
            setTimeout(() => {
                otpInputs.forEach(input => {
                    input.classList.remove('shake');
                });
            }, 500);
        }
        
        // Show success message
        function showSuccess(message) {
            successMessage.classList.remove('hidden');
            document.getElementById('success-text').textContent = message;
            errorMessage.classList.add('hidden');
        }
        
        // Clear OTP inputs
        function clearOTP() {
            otpInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled', 'error');
            });
            updateFullOTP();
            otpInputs[0].focus();
        }
        
        // Show loading overlay
        function showLoading(title, message) {
            document.getElementById('loading-title').textContent = title;
            document.getElementById('loading-message').textContent = message;
            loadingOverlay.classList.remove('hidden');
        }
        
        // Hide loading overlay
        function hideLoading() {
            loadingOverlay.classList.add('hidden');
        }
        
        // Show success modal
        function showSuccessModal() {
            successModal.classList.remove('hidden');
        }
        
        // // Simulate OTP verification (replace with actual API call)
        // function verifyOTP(otp) {
        //     return new Promise((resolve, reject) => {
        //         setTimeout(() => {
        //             // Demo verification - compare with demoOTP
        //             if (otp === demoOTP) {
        //                 resolve({ success: true, message: 'OTP verified successfully!' });
        //             } else {
        //                 reject({ success: false, message: 'Invalid OTP. Please try again.' });
        //             }
        //         }, 1500);
        //     });
        // }
        
        // Simulate sending OTP (replace with actual API call)
        // function sendOTP() {
        //     return new Promise((resolve) => {
        //         setTimeout(() => {
        //             // In real app, this would trigger SMS/email
        //             console.log(`OTP ${demoOTP} sent to ${email}`);
        //             resolve({ success: true, message: 'New OTP sent successfully!' });
        //         }, 1000);
        //     });
        // }
        
        // Form submission
        // document.getElementById('otp-form').addEventListener('submit', async (e) => {
        //     e.preventDefault();
            
        //     const otp = fullOtpInput.value;
            
        //     if (otp.length !== 6) {
        //         showError('Please enter a complete 6-digit OTP');
        //         return;
        //     }
            
        //     // Show loading
        //     showLoading('Verifying OTP', 'Please wait while we verify your code...');
            
        //     try {
        //         const result = await verifyOTP(otp);
                
        //         // Hide loading
        //         hideLoading();
                
        //         if (result.success) {
        //             showSuccess(result.message);
                    
        //             // Disable inputs
        //             disableOTPInputs();
                    
        //             // Show success modal after delay
        //             setTimeout(() => {
        //                 showSuccessModal();
        //             }, 1000);
                    
        //             // In real app, you would redirect to dashboard or next step
        //             // window.location.href = 'index.php';
        //         }
        //     } catch (error) {
        //         hideLoading();
        //         showError(error.message);
        //         clearOTP();
        //     }
        // });
        
        // Resend OTP
        // resendBtn.addEventListener('click', async () => {
        //     if (resendBtn.disabled) return;
            
        //     // Show loading
        //     showLoading('Sending OTP', 'Sending new verification code...');
            
        //     try {
        //         const result = await sendOTP();
                
        //         // Hide loading
        //         hideLoading();
                
        //         if (result.success) {
        //             // Reset timer
        //             timer = OTP_EXPIRY_TIME;
        //             startCountdown();
                    
        //             // Start resend cooldown
        //             startResendCooldown();
                    
        //             // Clear previous OTP
        //             clearOTP();
                    
        //             // Enable inputs if they were disabled
        //             enableOTPInputs();
                    
        //             // Show success message
        //             showSuccess(result.message);
                    
        //             // Auto hide success message
        //             setTimeout(() => {
        //                 successMessage.classList.add('hidden');
        //             }, 3000);
        //         }
        //     } catch (error) {
        //         hideLoading();
        //         showError('Failed to send OTP. Please try again.');
        //     }
        // });
        
        // Call me button
        // callBtn.addEventListener('click', () => {
        //     // In real app, this would trigger a voice call
        //     alert('In a real application, this would initiate a voice call with your OTP.');
        // });
        
        // // Trust device checkbox
        // document.getElementById('trust-device').addEventListener('change', (e) => {
        //     if (e.target.checked) {
        //         console.log('Device will be trusted for 30 days');
        //         // In real app, set a cookie or local storage flag
        //     }
        // });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initOTPInputs();
            startCountdown();
            // startResendCooldown();
            
            // Auto-fill with demo OTP for testing (remove in production)
            // pasteOTP(demoOTP);
        });
    </script>
</body>
</html>

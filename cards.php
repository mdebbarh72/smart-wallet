<?php
require "connection.php";


function categoryname($category) {
    $categories = [
        'food' => 'Food & Dining',
        'shopping' => 'Shopping',
        'entertainment' => 'Entertainment',
        'transport' => 'Transportation',
        'bills' => 'Bills & Utilities',
        'healthcare' => 'Healthcare',
        'education' => 'Education',
        'travel' => 'Travel',
        'groceries' => 'Groceries'
    ];
    
    return $categories[$category] ?? ucfirst($category);
}

function rendercard($card, $primarycard = false, $categoryLimits = []) {
    $cardtype = '';
    
    $cardTypeValue = strtolower($card['card_type']);
    
    switch($cardTypeValue) {
        case 'credit':
            $cardtype = 'credit-card';
            break;
        case 'prepaid':
            $cardtype = 'prepaid-card';
            break;
        default:
            $cardtype = 'debit-card';
    }
    
    $typeDisplay = ucfirst($cardTypeValue);
    
    $spent = 0;
    foreach($categoryLimits as $cl) {
        $spent += floatval($cl['consumed_amount'] ?? 0);
    }
    
    $monthlyLimit = floatval($card['spending_limit'] ?? 0);
    $amountused = $monthlyLimit > 0 ? ($spent / $monthlyLimit) * 100 : 0;
    
    $limitColor = 'text-green-400';
    $progressColor = 'bg-green-500';
    if ($amountused >= 100) {
        $limitColor = 'text-red-400';
        $progressColor = 'bg-red-500';
    } elseif ($amountused >= 80) {
        $limitColor = 'text-yellow-400';
        $progressColor = 'bg-yellow-500';
    }
    
    
    $last4Digits = substr($card['card_number'], -4);
    
    ob_start();
    
    if ($primarycard) {
        
        ?>
        <div class="mb-8">
           
            
            <div class="<?php echo $cardtype; ?> rounded-2xl p-8 shadow-lg">
                
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <div class="flex items-center mb-6">
                          
                            <div class="bg-white/30 w-12 h-10 rounded-md mr-4 flex items-center justify-center">
                                <span class="font-mono font-bold"><?php echo $last4Digits; ?></span>
                            </div>
                            <div>
                                <p class="text-sm opacity-80 mb-1">Primary Card</p>
                                <p class="text-2xl font-bold"><?php echo htmlspecialchars($card['card_name']); ?></p>
                            </div>
                        </div>
                        
                        
                        <div class="mb-6">
                            <p class="text-sm opacity-80 mb-1">Card Number</p>
                            <p class="text-xl font-mono font-bold tracking-wider">
                                •••• •••• •••• <?php echo $last4Digits; ?>
                            </p>
                        </div>
                        
                        
                        <div class="flex space-x-8">
                            <div>
                                <p class="text-sm opacity-80 mb-1">Bank</p>
                                <p class="text-lg font-bold"><?php echo htmlspecialchars($card['bank_name']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm opacity-80 mb-1">Type</p>
                                <p class="text-lg font-bold"><?php echo $typeDisplay; ?></p>
                            </div>
                            <div>
                                <p class="text-sm opacity-80 mb-1">Balance</p>
                                <p class="text-2xl font-bold"><?php echo number_format($card['card_total'], 2); ?> DH</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="text-right">
                        <div class="bg-white/20 rounded-full px-4 py-2 inline-block mb-4">
                            <i class="fas fa-check-circle mr-1"></i> Primary
                        </div>
                        <div class="space-x-2">
                            <button onclick="editCard(<?php echo $card['card_id']; ?>)" 
                                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCard(<?php echo $card['card_id']; ?>, true)" 
                                    class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                
                <?php if ($monthlyLimit > 0 || count($categoryLimits) > 0): ?>
                <div class="mt-6 pt-6 border-t border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-white/80 text-lg font-bold">Card Limits</span>
                        <?php if ($amountused > 0): ?>
                            <span class="<?php echo $limitColor; ?> text-xl font-bold">
                                <?php echo number_format($amountused, 1); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($monthlyLimit > 0): ?>
                        <div class="mb-6">
                            <div class="flex justify-between text-white/80 mb-2">
                                <span class="font-medium">Monthly Limit</span>
                                <span><?php echo number_format($spent, 2); ?> / <?php echo number_format($monthlyLimit, 2); ?> DH</span>
                            </div>
                            <div class="w-full bg-white/30 rounded-full h-3">
                                <div class="h-3 rounded-full <?php echo $progressColor; ?> limit-progress" 
                                     style="width: <?php echo min(100, $amountused); ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($categoryLimits) > 0): ?>
                        <div class="space-y-3">
                            <?php foreach($categoryLimits as $cl): ?>
                                <?php 
                                    $catSpent = floatval($cl['consumed_amount'] ?? 0);
                                    $catLimit = floatval($cl['amount_limit'] ?? 0);
                                    $catUsage = $catLimit > 0 ? ($catSpent / $catLimit) * 100 : 0;
                                    
                                    $catProgressColor = 'bg-green-500';
                                    if ($catUsage >= 100) {
                                        $catProgressColor = 'bg-red-500';
                                    } elseif ($catUsage >= 80) {
                                        $catProgressColor = 'bg-yellow-500';
                                    }
                                ?>
                                <div>
                                    <div class="flex justify-between text-white/80 mb-1">
                                        <span class="font-medium"><?php echo categoryname($cl['category_name']); ?></span>
                                        <span><?php echo number_format($catSpent, 2); ?> / <?php echo number_format($catLimit, 2); ?> DH</span>
                                    </div>
                                    <div class="w-full bg-white/30 rounded-full h-2">
                                        <div class="h-2 rounded-full <?php echo $catProgressColor; ?>" 
                                             style="width: <?php echo min(100, $catUsage); ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    } else {
        
        ?>
        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover">
            
            <div class="<?php echo $cardtype; ?> p-4 rounded-xl mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90"><?php echo htmlspecialchars($card['bank_name']); ?></p>
                        <p class="font-bold text-lg"><?php echo htmlspecialchars($card['card_name']); ?></p>
                    </div>
                    <i class="fas fa-credit-card text-2xl opacity-70"></i>
                </div>
                <div class="mt-4">
                    <p class="text-xs opacity-80">Card Number</p>
                    <p class="font-mono font-bold tracking-wider">•••• •••• •••• <?php echo $last4Digits; ?></p>
                </div>
            </div>
            
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-bold"><?php echo $typeDisplay; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Balance:</span>
                    <span class="text-xl font-bold text-gray-900"><?php echo number_format($card['card_total'], 2); ?> DH</span>
                </div>
            </div>
            
            
            <?php if ($monthlyLimit > 0 || count($categoryLimits) > 0): ?>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Card Limits</span>
                    <?php if ($amountused > 0): ?>
                        <span class="<?php echo $limitColor; ?> font-bold">
                            <?php echo number_format($amountused, 1); ?>%
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($monthlyLimit > 0): ?>
                    <div class="mb-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Monthly Limit</span>
                            <span><?php echo number_format($spent, 2); ?> / <?php echo number_format($monthlyLimit, 2); ?> DH</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full <?php echo $progressColor; ?> limit-progress" 
                                 style="width: <?php echo min(100, $amountused); ?>%"></div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (count($categoryLimits) > 0): ?>
                    <div class="space-y-2">
                        <?php foreach($categoryLimits as $cl): ?>
                            <div class="flex items-center justify-between text-sm py-1">
                                <span class="text-gray-600"><?php echo categoryname($cl['category_name']); ?></span>
                                <span class="font-medium"><?php echo number_format($cl['consumed_amount'], 2); ?> / <?php echo number_format($cl['amount_limit'], 2); ?> DH</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 mt-4">
                <form action="cardsqueries.php" method="POST" style="display: inline;">
                    <input type="hidden" name="operation" value="set_primary">
                    <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        <i class="fas fa-crown mr-2"></i> Set Primary
                    </button>
                </form>
                <div class="space-x-3">
                    <button onclick="editCard(<?php echo $card['card_id']; ?>)" 
                            class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteCard(<?php echo $card['card_id']; ?>, false)" 
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    return ob_get_clean();
}


function renderAllCards($pdo, $userId) {
    
    $selectPrimary = $pdo->prepare("
        SELECT c.*, 
               (SELECT amount_limit FROM cards_limits cl2 
                INNER JOIN categories cat2 ON cl2.category_id = cat2.category_id
                WHERE cl2.card_id = c.card_id AND cat2.category_name = 'total' 
                LIMIT 1) as spending_limit
        FROM cards c
        INNER JOIN main_cards m ON c.card_id = m.card_id
        WHERE c.user_id = ? AND m.user_id = ?
    ");
    $selectPrimary->execute([$userId, $userId]);
    $primaryCard = $selectPrimary->fetch(PDO::FETCH_ASSOC);
    
    
    $selectOther = $pdo->prepare("
        SELECT c.*,
               (SELECT amount_limit FROM cards_limits cl2 
                INNER JOIN categories cat2 ON cl2.category_id = cat2.category_id
                WHERE cl2.card_id = c.card_id AND cat2.category_name = 'total' 
                LIMIT 1) as spending_limit
        FROM cards c
        LEFT JOIN main_cards m ON c.card_id = m.card_id AND m.user_id = ?
        WHERE c.user_id = ? AND (m.card_id IS NULL OR m.user_id IS NULL)
    ");
    $selectOther->execute([$userId, $userId]);
    $otherCards = $selectOther->fetchAll(PDO::FETCH_ASSOC);
    
    
    $selectLimits = $pdo->prepare("
        SELECT cl.amount_limit, cl.consumed_amount, cat.category_name, cl.card_id
        FROM cards_limits cl
        INNER JOIN categories cat ON cl.category_id = cat.category_id
        INNER JOIN cards c ON cl.card_id = c.card_id
        WHERE c.user_id = ? AND cat.category_name != 'total'
    ");
    $selectLimits->execute([$userId]);
    $allLimits = $selectLimits->fetchAll(PDO::FETCH_ASSOC);
    
    
    $limitsByCard = [];
    foreach($allLimits as $limit) {
        $limitsByCard[$limit['card_id']][] = $limit;
    }
    
    ob_start();
    ?>
    
    <?php if ($primaryCard): ?>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-crown text-yellow-500 mr-2"></i> Primary Card
            </h2>
            <?php 
                $cardLimits = $limitsByCard[$primaryCard['card_id']] ?? [];
                echo rendercard($primaryCard, true, $cardLimits); 
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($otherCards) > 0): ?>
        
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">All Cards</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($otherCards as $card): ?>
                    <?php 
                        $cardLimits = $limitsByCard[$card['card_id']] ?? [];
                        echo rendercard($card, false, $cardLimits); 
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}


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
    header("Location: login.php?expired=1");
    exit;
}

$selectLimitsCount=$pdo->prepare("SELECT COUNT(*) 
                                  FROM users u
                                  INNER JOIN cards c
                                  ON u.user_id = c.user_id
                                  INNER JOIN cards_limits cl
                                  ON c.card_id= cl.card_id
                                  WHERE u.user_id=?");
$selectLimitsCount->execute([$_SESSION['user_id']]);
$LimitsCount= $selectLimitsCount->fetchColumn();

$selectLimitsSum=$pdo->prepare("SELECT SUM(amount_limit) 
                                FROM users u
                                INNER JOIN cards c
                                ON u.user_id=c.user_id
                                INNER JOIN cards_limits cl
                                ON c.card_id= cl.card_id
                                WHERE u.user_id= ?");
$selectLimitsSum->execute([$_SESSION['user_id']]);
$LimitsSum= $selectLimitsCount->fetchColumn();

$selectConsumption= $pdo->prepare("SELECT amount_limit,consumed_amount 
                                   FROM users u
                                   INNER JOIN cards c
                                   ON u.user_id= c.user_id
                                   INNER JOIN cards_limits cl
                                   ON c.card_id= cl.card_id
                                   WHERE u.user_id=?");
$selectConsumption->execute([$_SESSION['user_id']]);
$consumption= $selectConsumption->fetch(PDO::FETCH_ASSOC);

$selectBalance= $pdo->prepare("SELECT balance 
                                   FROM users u
                                   INNER JOIN balances b
                                   ON u.user_id= b.user_id
                                   WHERE u.user_id=?");
$selectBalance->execute([$_SESSION['user_id']]);
$balance= $selectBalance->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cards - Smart Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .card-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .credit-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .debit-card { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .prepaid-card { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .card-chip { 
            background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
            border-radius: 8px;
            width: 50px;
            height: 40px;
            position: relative;
        }
        .limit-progress { transition: width 0.5s ease; }
        .limit-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
        .limit-danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .pulse-demo {
            animation: pulse 2s infinite;
        }

        /* Add to your existing CSS */
        .card-chip-display {
            background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
            border-radius: 8px;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            backdrop-filter: blur(5px);
        }

        .primary-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .limit-bar {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            background: rgba(255,255,255,0.2);
        }

        .limit-bar-fill {
            height: 100%;
            transition: width 0.5s ease;
        }

        /* Make category limit progress bars thinner */
        .category-limit .limit-bar {
            height: 4px;
        }

        /* Improve card shadow for primary */
        .primary-card-shadow {
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="home.html" class="flex items-center">
                    <i class="fas fa-wallet text-blue-600 text-2xl mr-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Smart<span class="text-blue-600">Wallet</span></h1>
                </a>
            </div>
            <div class="space-x-4">
                <a href="home.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Home</a>
                <a href="incomes.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Incomes</a>
                <a href="expenses.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Expenses</a>
                <a href="cards.php" class="bg-blue-600 text-white font-medium px-4 py-2 rounded transition-colors">Cards</a>
                <a href="transfers.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Transfers</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    <!-- <div id="demo-success" class="max-w-7xl mx-auto px-4 pt-6 hidden">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span id="success-message"></span>
            </div>
        </div>
    </div> -->

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Cards</h1>
                <p class="text-gray-600 mt-2">Manage your banking cards, set limits, and track balances</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-500">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-balance">
                        <?php echo $balance; ?>
                    </p>
                </div>
                <button onclick="openAddCardModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add New Card
                </button>
            </div>
        </div>

        <!-- Cards Limits Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Active Limits</p>
                        <p class="text-3xl font-bold text-gray-900" id="active-limits">
                            <?php echo $LimitsCount; ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Across your cards</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Total Limits</p>
                        <p class="text-3xl font-bold text-gray-900" id="total-limit-amount">
                            <?php echo $LimitsSum; ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Monthly spending limits</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Limit Usage</p>
                        <p class="text-3xl font-bold text-gray-900" id="limit-usage">
                            <?php 
                                
                            ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Average across cards</div>
            </div>
        </div>

        <!-- Cards List -->
        <div id="cards-container">
            <?php
                echo renderAllCards($pdo, $_SESSION['user_id']);
            ?>
        </div>
    </div>

    <!-- Add Card Modal -->
    <div id="add-card-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Add New Card</h3>
                <button onclick="closeAddCardModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="add-card-form" class="space-y-4" action="cardsqueries.php" method="POST">
                <input type="hidden" name="operation" value="add">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Name *</label>
                    <input type="text" id="card-name" required  name="card_name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                           placeholder="e.g., My Personal Card">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Bank Name *</label>
                    <select id="bank-name" required name="bank_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Select Bank</option>
                        <option value="Banque Populaire">Banque Populaire</option>
                        <option value="CIH" selected>CIH</option>
                        <option value="Attijariwafa Bank">Attijariwafa Bank</option>
                        <option value="BMCE">BMCE</option>
                        <option value="Société Générale">Société Générale</option>
                        <option value="BMCI">BMCI</option>
                        <option value="Crédit du Maroc">Crédit du Maroc</option>
                        <option value="Crédit Agricole">Crédit Agricole</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Number</label>
                    <input type="text" id="card-number" name="card_number"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                           placeholder="XXXX XXXX XXXX XXXX">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Type *</label>
                    <select id="card-type" required name="card_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="Debit" selected>Debit Card</option>
                        <option value="Credit">Credit Card</option>
                        <option value="Prepaid">Prepaid Card</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Initial Balance (DH)</label>
                    <input type="number" step="0.01" id="card-balance" name="card_balance"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                           placeholder="0.00" value="0">
                </div>
                
                <!-- Card Limits Section -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Card Limits (Optional)</h4>
                        <button type="button" onclick="toggleLimitsSection()" 
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <span id="limits-toggle-text">Add Limits</span>
                        </button>
                    </div>
                    
                    <div id="limits-section" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Monthly Spending Limit (DH)</label>
                            <input type="number" step="0.01" id="monthly-limit" name="spending_limit"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                   placeholder="e.g., 5000.00">
                            <p class="text-sm text-gray-500 mt-1">Maximum amount that can be spent with this card per month</p>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Category Limit</label>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <select id="limit-category" name="limit_category"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="">Select Category</option>
                                        <option value="food">Food & Dining</option>
                                        <option value="shopping">Shopping</option>
                                        <option value="entertainment">Entertainment</option>
                                        <option value="transport">Transportation</option>
                                        <option value="bills">Bills & Utilities</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="education">Education</option>
                                        <option value="travel">Travel</option>
                                        <option value="groceries">Groceries</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="number" step="0.01" id="category-limit-amount" name="card_limit"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                           placeholder="Limit amount">
                                    <button type="button" onclick="addCategoryLimit()" 
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-3 rounded-lg">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="category-limits-list" class="space-y-2">
                            <!-- Category limits will be added here -->
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is-primary" name="primary" value="yes" class="h-4 w-4 text-blue-600 rounded">
                    <label for="is-primary" class="ml-2 text-gray-700">Set as primary card</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddCardModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"  
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Add Card
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Card Modal -->
    <div id="edit-card-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Edit Card</h3>
                <button onclick="closeEditCardModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="edit-card-form" class="space-y-4" action="cardsqueries.php" method="POST">
                <input type="hidden" id="edit-card-id">
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Name *</label>
                    <input type="text" id="edit-card-name" required name="card_name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Bank Name *</label>
                    <select id="edit-bank-name" required name="bank_name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Select Bank</option>
                        <option value="Banque Populaire">Banque Populaire</option>
                        <option value="CIH">CIH</option>
                        <option value="Attijariwafa Bank">Attijariwafa Bank</option>
                        <option value="BMCE">BMCE</option>
                        <option value="Société Générale">Société Générale</option>
                        <option value="BMCI">BMCI</option>
                        <option value="Crédit du Maroc">Crédit du Maroc</option>
                        <option value="Crédit Agricole">Crédit Agricole</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Number</label>
                    <input type="text" id="edit-card-number" c
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Card Type *</label>
                    <select id="edit-card-type" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="debit">Debit Card</option>
                        <option value="credit">Credit Card</option>
                        <option value="prepaid">Prepaid Card</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Balance (DH)</label>
                    <input type="number" step="0.01" id="edit-card-balance" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                
                <!-- Edit Card Limits Section -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Card Limits</h4>
                        <button type="button" onclick="toggleEditLimitsSection()" 
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <span id="edit-limits-toggle-text">Show Limits</span>
                        </button>
                    </div>
                    
                    <div id="edit-limits-section" class="space-y-4 hidden">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Monthly Spending Limit (DH)</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" step="0.01" id="edit-monthly-limit" 
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                       placeholder="e.g., 5000.00">
                                <button type="button" onclick="removeMonthlyLimit()" 
                                        class="bg-red-100 hover:bg-red-200 text-red-600 px-4 py-3 rounded-lg">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Category Limits</label>
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center space-x-2">
                                    <select id="edit-limit-category" 
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="">Select Category</option>
                                        <option value="food">Food & Dining</option>
                                        <option value="shopping">Shopping</option>
                                        <option value="entertainment">Entertainment</option>
                                        <option value="transport">Transportation</option>
                                        <option value="bills">Bills & Utilities</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="education">Education</option>
                                        <option value="travel">Travel</option>
                                        <option value="groceries">Groceries</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="number" step="0.01" id="edit-category-limit-amount"  name="limit_amount"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                           placeholder="Limit amount">
                                    <button type="button" onclick="addEditCategoryLimit()" 
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-3 rounded-lg">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="edit-category-limits-list" class="space-y-2">
                            <!-- Category limits will be added here -->
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center" id="edit-primary-container">
                    <input type="checkbox" id="edit-is-primary" class="h-4 w-4 text-blue-600 rounded">
                    <label for="edit-is-primary" class="ml-2 text-gray-700">Set as primary card</label>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditCardModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="saveCardEdit()" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-card-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Card</h3>
                <p class="text-gray-600">Are you sure you want to delete this card? This action cannot be undone.</p>
                <p class="text-sm text-gray-500 mt-2" id="delete-card-name"></p>
                <p id="delete-primary-warning" class="text-sm text-red-600 mt-2 hidden">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    This is your primary card. Deleting it will set another card as primary.
                </p>
            </div>
            
            <div class="space-y-4">
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteCardModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmDeleteCard()" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Delete Card
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        
        category_limits_count=1;
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();
            renderCards();
            setupEventListeners();
        });

        // Render all cards
        // function renderCards() {
        //     const container = document.getElementById('cards-container');
        //     const primaryCard = cards.find(card => card.isPrimary);
        //     const otherCards = cards.filter(card => !card.isPrimary);
            
        //     let html = '';
            
        //     // Primary card section
        //     if (primaryCard) {
        //         html += `
        //             <div class="mb-8">
        //                 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
        //                     <i class="fas fa-crown text-yellow-500 mr-2"></i> Primary Card
        //                 </h2>
        //                 ${renderCardHTML(primaryCard, true)}
        //             </div>
        //         `;
        //     }
            
        //     // Other cards section
        //     if (otherCards.length > 0) {
        //         html += `
        //             <div>
        //                 <h2 class="text-xl font-bold text-gray-800 mb-4">All Cards</h2>
        //                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        //                     ${otherCards.map(card => renderCardHTML(card, false)).join('')}
        //                 </div>
        //             </div>
        //         `;
        //     }
            
        //     container.innerHTML = html;
        // }

        // // Render single card HTML
        // function renderCardHTML(card, isPrimary) {
        //     const cardClass = card.type === 'credit' ? 'credit-card' : card.type === 'prepaid' ? 'prepaid-card' : 'debit-card';
        //     const typeDisplay = card.type.charAt(0).toUpperCase() + card.type.slice(1);
            
        //     // Calculate limit usage
        //     const spent = card.categoryLimits.reduce((sum, cl) => sum + cl.spent, 0);
        //     const monthlyLimit = card.monthlyLimit || 0;
        //     const usagePercentage = monthlyLimit > 0 ? (spent / monthlyLimit) * 100 : 0;
            
        //     // Determine limit status
        //     let limitStatus = '';
        //     if (monthlyLimit > 0) {
        //         if (usagePercentage >= 100) {
        //             limitStatus = 'limit-danger';
        //         } else if (usagePercentage >= 80) {
        //             limitStatus = 'limit-warning';
        //         }
        //     }
            
        //     const categoryLimitsHTML = card.categoryLimits.map(cl => `
        //         <div class="flex items-center justify-between text-sm py-1">
        //             <span class="text-gray-600">${getCategoryName(cl.category)}</span>
        //             <span class="font-medium">${cl.spent.toFixed(2)} / ${cl.limit.toFixed(2)} DH</span>
        //         </div>
        //     `).join('');
            
        //     return `
        //         <div class="${isPrimary ? '' : 'bg-white rounded-2xl shadow-lg p-6 card-hover'}">
        //             <div class="${isPrimary ? cardClass + ' p-8' : ''}">
        //                 ${isPrimary ? '' : `
        //                     <div class="flex justify-between items-start mb-4">
        //                         <div class="${cardClass} p-4 rounded-lg w-full">
        //                             <div class="flex justify-between items-center">
        //                                 <div>
        //                                     <p class="text-sm opacity-90">${card.bank}</p>
        //                                     <p class="font-bold">${card.name}</p>
        //                                 </div>
        //                                 <i class="fas fa-credit-card text-2xl opacity-70"></i>
        //                             </div>
        //                             <div class="mt-4">
        //                                 <p class="text-xs opacity-80">Card Number</p>
        //                                 <p class="font-mono tracking-wider">•••• •••• •••• ${card.cardNumber}</p>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 `}
                        
        //                 ${isPrimary ? `
        //                     <div class="flex justify-between items-start">
        //                         <div>
        //                             <div class="flex items-center mb-6">
        //                                 <div class="card-chip mr-4"></div>
        //                                 <div>
        //                                     <p class="text-sm opacity-80">Primary Card</p>
        //                                     <p class="text-xl font-bold">${card.name}</p>
        //                                 </div>
        //                             </div>
        //                             <div class="mb-2">
        //                                 <p class="text-sm opacity-80">Card Number</p>
        //                                 <p class="text-xl font-mono tracking-wider">•••• •••• •••• ${card.cardNumber}</p>
        //                             </div>
        //                             <div class="flex space-x-6">
        //                                 <div>
        //                                     <p class="text-sm opacity-80">Bank</p>
        //                                     <p class="font-bold">${card.bank}</p>
        //                                 </div>
        //                                 <div>
        //                                     <p class="text-sm opacity-80">Type</p>
        //                                     <p class="font-bold">${typeDisplay}</p>
        //                                 </div>
        //                                 <div>
        //                                     <p class="text-sm opacity-80">Balance</p>
        //                                     <p class="text-2xl font-bold">${card.balance.toFixed(2)} DH</p>
        //                                 </div>
        //                             </div>
        //                         </div>
        //                         <div class="text-right">
        //                             <div class="bg-white/20 rounded-full px-4 py-2 inline-block mb-4">
        //                                 <i class="fas fa-check-circle mr-1"></i> Primary
        //                             </div>
        //                             <div class="space-x-2">
        //                                 <button onclick="editCard(${card.id})" 
        //                                         class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
        //                                     <i class="fas fa-edit"></i>
        //                                 </button>
        //                                 <button onclick="deleteCard(${card.id}, true)" 
        //                                         class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
        //                                     <i class="fas fa-trash"></i>
        //                                 </button>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 ` : ''}
                        
        //                 ${!isPrimary ? `
        //                     <div class="space-y-3 mt-4">
        //                         <div class="flex justify-between items-center">
        //                             <span class="text-gray-600">Type:</span>
        //                             <span class="font-bold">${typeDisplay}</span>
        //                         </div>
        //                         <div class="flex justify-between items-center">
        //                             <span class="text-gray-600">Balance:</span>
        //                             <span class="text-xl font-bold text-gray-900">${card.balance.toFixed(2)} DH</span>
        //                         </div>
        //                 ` : ''}
                        
        //                 <!-- Limits Display -->
        //                 ${card.monthlyLimit || card.categoryLimits.length > 0 ? `
        //                     <div class="mt-4 pt-4 ${isPrimary ? 'border-t border-white/20' : 'border-t border-gray-200'}">
        //                         <div class="flex items-center justify-between mb-2">
        //                             <span class="${isPrimary ? 'text-white/80' : 'text-gray-600'} text-sm font-medium">Card Limits</span>
        //                             ${usagePercentage > 0 ? `
        //                                 <span class="${usagePercentage >= 100 ? 'text-red-400' : usagePercentage >= 80 ? 'text-yellow-400' : 'text-green-400'} font-bold">
        //                                     ${usagePercentage.toFixed(1)}%
        //                                 </span>
        //                             ` : ''}
        //                         </div>
                                
        //                         ${card.monthlyLimit ? `
        //                             <div class="mb-3">
        //                                 <div class="flex justify-between text-xs ${isPrimary ? 'text-white/80' : 'text-gray-500'} mb-1">
        //                                     <span>Monthly Limit</span>
        //                                     <span>${spent.toFixed(2)} / ${card.monthlyLimit.toFixed(2)} DH</span>
        //                                 </div>
        //                                 <div class="w-full ${isPrimary ? 'bg-white/30' : 'bg-gray-200'} rounded-full h-2">
        //                                     <div class="h-2 rounded-full ${usagePercentage >= 100 ? 'bg-red-500' : usagePercentage >= 80 ? 'bg-yellow-500' : 'bg-green-500'} limit-progress" 
        //                                          style="width: ${Math.min(100, usagePercentage)}%"></div>
        //                                 </div>
        //                             </div>
        //                         ` : ''}
                                
        //                         ${card.categoryLimits.length > 0 ? `
        //                             <div class="space-y-1">
        //                                 ${categoryLimitsHTML}
        //                             </div>
        //                         ` : ''}
        //                     </div>
        //                 ` : ''}
                        
        //                 ${!isPrimary ? `
        //                         <div class="flex justify-between items-center pt-3 border-t">
        //                             <button onclick="setAsPrimary(${card.id})" 
        //                                     class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
        //                                 <i class="fas fa-crown mr-1"></i> Set Primary
        //                             </button>
        //                             <div class="space-x-2">
        //                                 <button onclick="editCard(${card.id})" 
        //                                         class="text-gray-600 hover:text-gray-800">
        //                                     <i class="fas fa-edit"></i>
        //                                 </button>
        //                                 <button onclick="deleteCard(${card.id}, false)" 
        //                                         class="text-red-600 hover:text-red-800">
        //                                     <i class="fas fa-trash"></i>
        //                                 </button>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 ` : ''}
        //             </div>
        //         </div>
        //     `;
        // }

        // Get category display name

        // Modal functions
        function openAddCardModal() {
            document.getElementById('add-card-modal').classList.remove('hidden');
            document.getElementById('add-card-form').reset();
            document.getElementById('card-type').value = 'debit';
            document.getElementById('bank-name').value = 'CIH';
        }
        
        function closeAddCardModal() {
            document.getElementById('add-card-modal').classList.add('hidden');
        }
        
        // function openEditCardModal() {


        //     // document.getElementById('edit-card-id').value = card.id;
        //     // document.getElementById('edit-card-name').value = card.name;
        //     // document.getElementById('edit-bank-name').value = card.bank;
        //     // document.getElementById('edit-card-number').value = card.cardNumber;
        //     // document.getElementById('edit-card-type').value = card.type;
        //     // document.getElementById('edit-card-balance').value = card.balance;
        //     // document.getElementById('edit-monthly-limit').value = card.monthlyLimit || '';
        //     // document.getElementById('edit-is-primary').checked = card.isPrimary;
            
        //     // // Set up category limits
        //     // tempEditCategoryLimits = [...card.categoryLimits];
        //     // updateEditCategoryLimitsList();
            
        //     // // Hide primary checkbox if already primary
        //     // const container = document.getElementById('edit-primary-container');
        //     // if (card.isPrimary) {
        //     //     container.classList.add('hidden');
        //     // } else {
        //     //     container.classList.remove('hidden');
        //     // }
            
        //     // document.getElementById('edit-card-modal').classList.remove('hidden');
            
        // }
        
        // function closeEditCardModal() {
        //     document.getElementById('edit-card-modal').classList.add('hidden');
        // }

        function toggleLimitsSection() {
            const section = document.getElementById('limits-section');
            const toggleText = document.getElementById('limits-toggle-text');
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                toggleText.textContent = 'Hide Limits';
            } else {
                section.classList.add('hidden');
                toggleText.textContent = 'Add Limits';
            }
        }

        // function toggleEditLimitsSection() {
        //     const section = document.getElementById('edit-limits-section');
        //     const toggleText = document.getElementById('edit-limits-toggle-text');
        //     if (section.classList.contains('hidden')) {
        //         section.classList.remove('hidden');
        //         toggleText.textContent = 'Hide Limits';
        //     } else {
        //         section.classList.add('hidden');
        //         toggleText.textContent = 'Show Limits';
        //     }
        // }

        function addCategoryLimit() {
            const category = document.getElementById('limit-category').value;
            const amount = parseFloat(document.getElementById('category-limit-amount').value);
            
            if (!category || !amount || amount <= 0) {
                alert('Please select a category and enter a valid amount');
                return;
            }
            
            // Check if category already has a limit
            if (tempCategoryLimits.some(cl => cl.category === category)) {
                alert('This category already has a limit');
                return;
            }
            
            tempCategoryLimits.push({
                category: category,
                limit: amount,
                spent: 0
            });
            
            updateCategoryLimitsList();
            
            // Clear inputs
            document.getElementById('limit-category').value = '';
            document.getElementById('category-limit-amount').value = '';
        }

        // function addEditCategoryLimit() {
        //     const category = document.getElementById('edit-limit-category').value;
        //     const amount = parseFloat(document.getElementById('edit-category-limit-amount').value);
            
        //     if (!category || !amount || amount <= 0) {
        //         alert('Please select a category and enter a valid amount');
        //         return;
        //     }
            
        //     // Check if category already has a limit
        //     if (tempEditCategoryLimits.some(cl => cl.category === category)) {
        //         alert('This category already has a limit');
        //         return;
        //     }
            
        //     tempEditCategoryLimits.push({
        //         category: category,
        //         limit: amount,
        //         spent: 0
        //     });
            
        //     updateEditCategoryLimitsList();
            
        //     // Clear inputs
        //     document.getElementById('edit-limit-category').value = '';
        //     document.getElementById('edit-category-limit-amount').value = '';
        // }

        // function removeCategoryLimit(category) {
        //     tempCategoryLimits = tempCategoryLimits.filter(cl => cl.category !== category);
        //     updateCategoryLimitsList();
        // }

        // function removeEditCategoryLimit(category) {
        //     tempEditCategoryLimits = tempEditCategoryLimits.filter(cl => cl.category !== category);
        //     updateEditCategoryLimitsList();
        // }

        // function removeMonthlyLimit() {
        //     document.getElementById('edit-monthly-limit').value = '';
        // }

        function updateCategoryLimitsList() {
            const container = document.getElementById('category-limits-list');
            if (tempCategoryLimits.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center py-2">No category limits added</p>';
                return;
            }
            
            container.innerHTML = tempCategoryLimits.map(cl => `
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <div>
                        <span class="font-medium">${getCategoryName(cl.category)}</span>
                        <span class="text-sm text-gray-600 ml-2">${cl.limit.toFixed(2)} DH</span>
                    </div>
                    <button type="button" onclick="removeCategoryLimit('${cl.category}')" 
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        }

        function updateEditCategoryLimitsList() {
            const container = document.getElementById('edit-category-limits-list');
            if (tempEditCategoryLimits.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center py-2">No category limits</p>';
                return;
            }
            
            container.innerHTML = tempEditCategoryLimits.map(cl => `
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <div>
                        <span class="font-medium">${getCategoryName(cl.category)}</span>
                        <span class="text-sm text-gray-600 ml-2">${cl.limit.toFixed(2)} DH</span>
                    </div>
                    <button type="button" onclick="removeEditCategoryLimit('${cl.category}')" 
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        }

        // // Action functions
        // function addNewCard() {
        //     const name = document.getElementById('card-name').value.trim();
        //     const bank = document.getElementById('bank-name').value;
        //     const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '').slice(-4);
        //     const type = document.getElementById('card-type').value;
        //     const balance = parseFloat(document.getElementById('card-balance').value) || 0;
        //     const monthlyLimit = parseFloat(document.getElementById('monthly-limit').value) || null;
        //     const isPrimary = document.getElementById('is-primary').checked;
            
        //     if (!name || !bank) {
        //         alert('Please fill in all required fields');
        //         return;
        //     }
            
        //     // If setting as primary, remove primary from other cards
        //     if (isPrimary) {
        //         cards.forEach(card => card.isPrimary = false);
        //     }
            
        //     const newCard = {
        //         id: nextCardId++,
        //         name: name,
        //         bank: bank,
        //         cardNumber: cardNumber || '0000',
        //         type: type,
        //         balance: balance,
        //         isPrimary: isPrimary || cards.length === 0,
        //         monthlyLimit: monthlyLimit,
        //         categoryLimits: [...tempCategoryLimits]
        //     };
            
        //     cards.push(newCard);
            
        //     closeAddCardModal();
        //     renderCards();
        //     updateStats();
        //     showSuccess('Card added successfully!');
        // }

        // function saveCardEdit() {
        //     const cardId = parseInt(document.getElementById('edit-card-id').value);
        //     const cardIndex = cards.findIndex(c => c.id === cardId);
            
        //     if (cardIndex !== -1) {
        //         const name = document.getElementById('edit-card-name').value.trim();
        //         const bank = document.getElementById('edit-bank-name').value;
        //         const cardNumber = document.getElementById('edit-card-number').value.replace(/\s/g, '').slice(-4);
        //         const type = document.getElementById('edit-card-type').value;
        //         const balance = parseFloat(document.getElementById('edit-card-balance').value) || 0;
        //         const monthlyLimit = parseFloat(document.getElementById('edit-monthly-limit').value) || null;
        //         const isPrimary = document.getElementById('edit-is-primary').checked;
                
        //         if (!name || !bank) {
        //             alert('Please fill in all required fields');
        //             return;
        //         }
                
        //         // If setting as primary, remove primary from other cards
        //         if (isPrimary) {
        //             cards.forEach(card => card.isPrimary = false);
        //         }
                
        //         cards[cardIndex] = {
        //             ...cards[cardIndex],
        //             name: name,
        //             bank: bank,
        //             cardNumber: cardNumber || cards[cardIndex].cardNumber,
        //             type: type,
        //             balance: balance,
        //             monthlyLimit: monthlyLimit,
        //             categoryLimits: [...tempEditCategoryLimits],
        //             isPrimary: isPrimary || cards[cardIndex].isPrimary
        //         };
                
        //         closeEditCardModal();
        //         renderCards();
        //         updateStats();
        //         showSuccess('Card updated successfully!');
        //     }
        // }

        // function deleteCard(cardId, isPrimary) {
        //     openDeleteCardModal(cardId, isPrimary);
        // }

        // function confirmDeleteCard() {
        //     if (cardToDelete) {
        //         const { id, isPrimary } = cardToDelete;
                
        //         // Remove card
        //         cards = cards.filter(card => card.id !== id);
                
        //         // If we deleted the primary card and there are other cards, make the first one primary
        //         if (isPrimary && cards.length > 0) {
        //             cards[0].isPrimary = true;
        //         }
                
        //         closeDeleteCardModal();
        //         renderCards();
        //         updateStats();
        //         showSuccess('Card deleted successfully!');
        //     }
        // }

        // function setAsPrimary(cardId) {
        //     // Remove primary from all cards
        //     cards.forEach(card => card.isPrimary = false);
            
        //     // Set new primary
        //     const card = cards.find(c => c.id === cardId);
        //     if (card) {
        //         card.isPrimary = true;
        //     }
            
        //     renderCards();
        //     showSuccess('Primary card updated successfully!');
        // }

        // function openDeleteCardModal(cardId, isPrimary) {
        //     const card = cards.find(c => c.id === cardId);
        //     if (card) {
        //         cardToDelete = {id: cardId, isPrimary: isPrimary};
        //         document.getElementById('delete-card-name').textContent = card.name;
                
        //         const warning = document.getElementById('delete-primary-warning');
        //         if (isPrimary) {
        //             warning.classList.remove('hidden');
        //         } else {
        //             warning.classList.add('hidden');
        //         }
                
        //         document.getElementById('delete-card-modal').classList.remove('hidden');
        //     }
        // }

        // function closeDeleteCardModal() {
        //     cardToDelete = null;
        //     document.getElementById('delete-card-modal').classList.add('hidden');
        // }

        // // Show success message
        // function showSuccess(message) {
        //     const container = document.getElementById('demo-success');
        //     const messageSpan = document.getElementById('success-message');
            
        //     messageSpan.textContent = message;
        //     container.classList.remove('hidden');
            
        //     // Auto hide after 3 seconds
        //     setTimeout(() => {
        //         container.classList.add('hidden');
        //     }, 3000);
        // }

    </script>
</body>
</html>

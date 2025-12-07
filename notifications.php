<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$notifications = [];
$userOrders = [];

try {
    $stmt = $pdo->prepare('SELECT o.id, o.product_id, o.quantity, o.total_price, o.order_date, p.title, p.image_url FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = ? ORDER BY o.order_date DESC');
    $stmt->execute([$userId]);
    $userOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Orders fetch error: ' . $e->getMessage());
}

$notifications = [];

foreach ($userOrders as $order) {
    $notifications[] = [
        'id' => 'order_' . $order['id'],
        'type' => 'order',
        'title' => 'Order Confirmed',
        'message' => 'You successfully ordered "' . $order['title'] . '" (Qty: ' . $order['quantity'] . ')',
        'created_at' => $order['order_date'],
        'icon' => 'fa-check-circle',
        'color' => '#4ecdc4',
        'order' => $order
    ];
}

$systemNotifications = [

    ['id' => 1, 'type' => 'system', 'title' => 'Welcome!', 'message' => 'Welcome to Marketplace! Check out the new arrivals.', 'created_at' => date('Y-m-d H:i:s'), 'icon' => 'fa-star', 'color' => '#ffd700'],
];

$notifications = array_merge($notifications, $systemNotifications);

usort($notifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications - Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { primary: '#4ecdc4', accent: '#44a08d' } } } };
    </script>
    <link rel="stylesheet" href="assets/css/dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <style>
        .notification-item {
            background: rgba(255,255,255,0.03);
            border-left: 4px solid;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        
        .notification-item:hover {
            background: rgba(255,255,255,0.06);
            transform: translateX(4px);
        }
        
        .notification-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
            margin-top: 4px;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #fff;
        }
        
        .notification-message {
            color: rgba(255,255,255,0.8);
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .notification-date {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        
        .order-notification-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        
        .notification-item.order {
            cursor: pointer;
        }
        
        .notification-item.order:hover {
            border-left-color: #4ecdc4;
        }
    </style>
</head>
<body class="dashboard-page">

<div class="main-wrapper">
    <div class="dashboard-layout">
        <?php include __DIR__ . '/layout.php'; ?>

        <main class="main-content">
            <div style="max-width:900px;margin:0 auto;padding:0 18px;color:#fff;">
                <h2 style="margin-bottom: 24px; font-size: 2rem; display: flex; align-items: center; gap: 12px;"><i class="fas fa-bell"></i> Notifications</h2>
                
                <div style="margin-top:12px;">
                    <?php if (empty($notifications)): ?>
                        <div style="text-align: center; padding: 40px 20px; color: rgba(255,255,255,0.6);">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                            <p style="font-size: 1.1rem;">No notifications yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <div class="notification-item <?php echo $n['type']; ?>" 
                                 style="border-left-color: <?php echo $n['color'] ?? '#4ecdc4'; ?>" 
                                 <?php if ($n['type'] === 'order'): ?>onclick="viewOrderDetails(<?php echo substr($n['id'], 6); ?>)"<?php endif; ?>>
                                
                                <?php if ($n['type'] === 'order' && !empty($n['order']['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($n['order']['image_url']); ?>" alt="Product" class="order-notification-img">
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <div class="notification-title">
                                        <i class="fas <?php echo $n['icon']; ?>" style="color: <?php echo $n['color'] ?? '#4ecdc4'; ?>; margin-right: 8px;"></i>
                                        <?php echo htmlspecialchars($n['title']); ?>
                                    </div>
                                    <div class="notification-message">
                                        <?php echo htmlspecialchars($n['message']); ?>
                                        <?php if ($n['type'] === 'order'): ?>
                                            <br><small style="color: #4ecdc4; cursor: pointer; text-decoration: underline;">Click to view details &rarr;</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="notification-date">
                                        <?php echo date('M d, Y • H:i', strtotime($n['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="script.js"></script>
<script>
    if (typeof AOS !== 'undefined') AOS.init();
</script>

</body>
</html>

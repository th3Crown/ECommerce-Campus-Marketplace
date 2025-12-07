<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$orderId = intval($_GET['order_id']);
$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare('SELECT id, product_id, quantity, total_price, order_date FROM orders WHERE id = ? AND user_id = ?');
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    $stmt = $pdo->prepare('SELECT id, title, price, image_url FROM products WHERE id = ?');
    $stmt->execute([$order['product_id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'product' => $product ?: ['id' => $order['product_id'], 'title' => 'Product', 'price' => '0', 'image_url' => '']
    ]);
} catch (Exception $e) {
    error_log('Order details error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>

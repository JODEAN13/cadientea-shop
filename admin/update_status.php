<?php
require_once '../function.php';
require_once '../validation.php';  // ← ADD THIS LINE
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Invalid request method.');
    redirect('orders.php');
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

$validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled'];

if ($orderId <= 0) {
    setFlash('error', 'Invalid order ID.');
    redirect('orders.php');
}

if (!in_array($status, $validStatuses)) {
    setFlash('error', 'Invalid status value.');
    redirect('orders.php');
}

global $conn;
$checkStmt = $conn->prepare("SELECT id, order_number FROM orders WHERE id = ?");
$checkStmt->bind_param("i", $orderId);
$checkStmt->execute();
$result = $checkStmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('orders.php');
}

$updateStmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$updateStmt->bind_param("si", $status, $orderId);

if ($updateStmt->execute()) {
    $statusDisplay = ucwords(str_replace('_', ' ', $status));
    setFlash('success', '✅ Order #' . htmlspecialchars($order['order_number']) . ' updated to <strong>' . $statusDisplay . '</strong>!');
} else {
    setFlash('error', 'Failed to update order status.');
}

redirect('orders.php');
?>
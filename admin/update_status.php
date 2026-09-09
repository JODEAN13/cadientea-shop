<?php
require_once '../function.php';
requireLogin();
requireAdmin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('error', 'Invalid request method.');
    redirect('orders.php');
}

// Get and validate inputs
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

// Define valid statuses
$validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled'];

// Validate order ID
if ($orderId <= 0) {
    setFlash('error', 'Invalid order ID.');
    redirect('orders.php');
}

// Validate status
if (!in_array($status, $validStatuses)) {
    setFlash('error', 'Invalid status value.');
    redirect('orders.php');
}

// Check if order exists
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

// Update order status
$updateStmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$updateStmt->bind_param("si", $status, $orderId);

if ($updateStmt->execute()) {
    // Format status for display
    $statusDisplay = str_replace('_', ' ', $status);
    $statusDisplay = ucwords($statusDisplay);
    
    setFlash('success', '✅ Order #' . htmlspecialchars($order['order_number']) . ' status updated to <strong>' . $statusDisplay . '</strong>!');
} else {
    setFlash('error', 'Failed to update order status. Please try again.');
}

redirect('orders.php');
?>
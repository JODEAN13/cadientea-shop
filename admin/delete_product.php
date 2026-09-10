<?php
require_once '../function.php';
require_once '../validation.php';  // ← ADD THIS LINE
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($productId <= 0) {
    setFlash('error', 'Invalid product ID.');
    redirect('products.php');
}

global $conn;

// Get product name first
$stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    setFlash('error', 'Product not found.');
    redirect('products.php');
}

// Delete product
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);

if ($stmt->execute()) {
    setFlash('success', '🗑️ Product "' . $product['name'] . '" deleted successfully!');
} else {
    setFlash('error', 'Failed to delete product.');
}

redirect('products.php');
?>
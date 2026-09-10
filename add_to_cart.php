<?php
require_once 'function.php';
require_once 'validation.php';

// Start session and initialize cart
initCart();

// Get product details from POST
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$sizeId    = isset($_POST['size_id']) ? (int)$_POST['size_id'] : 0;
$quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate input
if ($productId <= 0 || $sizeId <= 0) {
    setFlash('error', 'Invalid product selection.');
    redirect('menu.php');
}

// Get product details from database
global $conn;

$stmt = $conn->prepare("SELECT name FROM products WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    setFlash('error', 'Product not available.');
    redirect('menu.php');
}

$stmt = $conn->prepare("SELECT id, size, price FROM product_sizes WHERE id = ? AND product_id = ?");
$stmt->bind_param("ii", $sizeId, $productId);
$stmt->execute();
$size = $stmt->get_result()->fetch_assoc();

if (!$size) {
    setFlash('error', 'Invalid size selection.');
    redirect('menu.php');
}

// ── CHECK IF USER IS LOGGED IN ──────────────────────────────────────────────
if (!isLoggedIn()) {
    // Save item details in session so we can add it after login
    $_SESSION['pending_cart'] = [
        'product_id'   => $productId,
        'product_name' => $product['name'],
        'size_id'      => $size['id'],
        'size'         => $size['size'],
        'price'        => $size['price'],
        'quantity'     => $quantity
    ];
    
    // Flash message telling the user to log in
    setFlash('error', 'Please log in to add items to your order. 🧋');
    
    // Redirect to login page
    redirect('login.php');
}

// ── USER IS LOGGED IN — ADD TO CART ────────────────────────────────────────
addToCart($productId, $product['name'], $size['id'], $size['size'], $size['price'], $quantity);

setFlash('success', $product['name'] . ' (' . $size['size'] . ') added to your order! 🧋');
redirect('menu.php');
?>
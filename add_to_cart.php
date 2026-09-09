<?php
require_once 'function.php';

// Start session and initialize cart
initCart();

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Please log in to add items to your order.');
    redirect('login.php');
}

// Get product details from POST
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$sizeId = isset($_POST['size_id']) ? (int)$_POST['size_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate input
if ($productId <= 0) {
    setFlash('error', 'Invalid product selection.');
    redirect('index.php#menu');
}

if ($sizeId <= 0) {
    setFlash('error', 'Please select a size.');
    redirect('index.php#menu');
}

// Get product and size details from database
global $conn;

// Get product name
$stmt = $conn->prepare("SELECT name FROM products WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    setFlash('error', 'Product not available.');
    redirect('index.php#menu');
}

// Get size details
$stmt = $conn->prepare("SELECT id, size, price FROM product_sizes WHERE id = ? AND product_id = ?");
$stmt->bind_param("ii", $sizeId, $productId);
$stmt->execute();
$result = $stmt->get_result();
$size = $result->fetch_assoc();

if (!$size) {
    setFlash('error', 'Invalid size selection.');
    redirect('index.php#menu');
}

// Add to cart
addToCart($productId, $product['name'], $size['id'], $size['size'], $size['price'], $quantity);

// Check if AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax) {
    // Return JSON for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $product['name'] . ' (' . $size['size'] . ') added to cart!',
        'cart_count' => getCartCount()
    ]);
    exit;
}

// Regular form submission
setFlash('success', $product['name'] . ' (' . $size['size'] . ') added to your order! 🧋');
redirect('cart.php');
?>
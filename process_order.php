<?php
require_once 'function.php';
require_once 'validation.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

// CSRF Check
$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission. Please try again.');
    redirect('checkout.php');
}

// Get cart items
$cartItems = getCartItems();
if (empty($cartItems)) {
    setFlash('error', 'Your cart is empty.');
    redirect('cart.php');
}

// Get form data
$phone = sanitize($_POST['phone'] ?? '');
$deliveryType = sanitize($_POST['delivery_type'] ?? 'delivery');
$deliveryAddress = sanitize($_POST['delivery_address'] ?? '');
$paymentMethod = sanitize($_POST['payment_method'] ?? '');

// Validate delivery type
if (!in_array($deliveryType, ['delivery', 'pickup'])) {
    $deliveryType = 'delivery';
}

// Validate
$errors = [];

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

// Only require delivery address if delivery is selected
if ($deliveryType === 'delivery' && empty($deliveryAddress)) {
    $errors['delivery_address'] = 'Delivery address is required.';
}

if (empty($paymentMethod)) {
    $errors['payment_method'] = 'Please select a payment method.';
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    redirect('checkout.php');
}

// Calculate totals
$subtotal = getCartTotal();
$deliveryFee = ($deliveryType === 'delivery') ? 50 : 0;
$total = $subtotal + $deliveryFee;

// If pickup, set delivery address to store location
if ($deliveryType === 'pickup') {
    $deliveryAddress = 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental';
}

// Create order with delivery type
$userId = $_SESSION['user_id'];
$orderId = createOrderWithType($userId, $cartItems, $subtotal, $deliveryFee, $total, $paymentMethod, $deliveryAddress, $deliveryType);

if (!$orderId) {
    setFlash('error', 'Failed to place order. Please try again.');
    redirect('checkout.php');
}

// Clear cart
clearCart();

// Redirect to success page
setFlash('success', 'Your order has been placed successfully! 🎉');
redirect('order_success.php?order_id=' . $orderId);
?>
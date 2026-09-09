<?php
require_once 'function.php';
require_once 'validation.php';  // ← Required for sanitize() function
requireLogin();

// Only accept POST requests
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

// Get and sanitize form data
$phone = sanitize($_POST['phone'] ?? '');
$deliveryAddress = sanitize($_POST['delivery_address'] ?? '');
$paymentMethod = sanitize($_POST['payment_method'] ?? '');

// Validate form data
$errors = [];

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

if (empty($deliveryAddress)) {
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
$deliveryFee = 50; // Fixed delivery fee
$total = $subtotal + $deliveryFee;

// Create order in database
$userId = $_SESSION['user_id'];
$orderId = createOrder($userId, $cartItems, $subtotal, $deliveryFee, $total, $paymentMethod, $deliveryAddress);

if (!$orderId) {
    setFlash('error', 'Failed to place order. Please try again.');
    redirect('checkout.php');
}

// Clear the cart
clearCart();

// Set success message and redirect
setFlash('success', 'Your order has been placed successfully! 🎉');
redirect('order_success.php?order_id=' . $orderId);
?>
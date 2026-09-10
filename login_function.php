<?php
require_once 'function.php';
require_once 'validation.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// ── CSRF CHECK ──────────────────────────────────────────────────────────────
$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission. Please try again.');
    redirect('login.php');
}
unset($_SESSION['csrf_token']);

// ── COLLECT & SANITIZE INPUT ────────────────────────────────────────────────
$email    = sanitize($_POST['email']    ?? '');
$password = $_POST['password'] ?? '';

// ── VALIDATE FIELDS ─────────────────────────────────────────────────────────
$errors = validateLoginForm(['email' => $email, 'password' => $password]);

if (hasErrors($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old']    = ['email' => $email];
    redirect('login.php');
}

// ── CHECK CREDENTIALS ───────────────────────────────────────────────────────
$user = getUserByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_errors'] = [
        'general' => 'Incorrect email or password. Please try again.',
    ];
    $_SESSION['login_old'] = ['email' => $email];
    redirect('login.php');
}

// ── SUCCESS — LOG THE USER IN ───────────────────────────────────────────────
loginUser($user);

// ── CHECK FOR PENDING CART (item added before login) ────────────────────────
if (isset($_SESSION['pending_cart']) && !empty($_SESSION['pending_cart'])) {
    $pending = $_SESSION['pending_cart'];
    
    // Add the pending item to cart
    addToCart(
        $pending['product_id'],
        $pending['product_name'],
        $pending['size_id'],
        $pending['size'],
        $pending['price'],
        $pending['quantity'] ?? 1
    );
    
    unset($_SESSION['pending_cart']);
    
    setFlash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '! 🧋 Your item has been added to your order.');
    redirect('cart.php');
}

// ── ADMIN REDIRECT ──────────────────────────────────────────────────────────
if (($user['role'] ?? '') === 'admin') {
    setFlash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '! 🛡️');
    redirect('admin/index.php');
}

// ── REGULAR CUSTOMER ────────────────────────────────────────────────────────
setFlash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '! 🧋');
redirect('success.php');
?>
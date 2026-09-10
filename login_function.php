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
// Rotate the token after use
unset($_SESSION['csrf_token']);

// ── COLLECT & SANITIZE INPUT ────────────────────────────────────────────────
$email    = sanitize($_POST['email']    ?? '');
$password = $_POST['password'] ?? ''; // do NOT sanitize — verify raw against hash

// ── VALIDATE FIELDS ─────────────────────────────────────────────────────────
$errors = validateLoginForm(['email' => $email, 'password' => $password]);

if (hasErrors($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old']    = ['email' => $email];
    redirect('login.php');
}

// ── CHECK CREDENTIALS AGAINST DATABASE ──────────────────────────────────────
$user = getUserByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
    // Use a vague error — don't reveal whether the email or password was wrong
    $_SESSION['login_errors'] = [
        'general' => 'Incorrect email or password. Please try again.',
    ];
    $_SESSION['login_old'] = ['email' => $email];
    redirect('login.php');
}

// ── SUCCESS — LOG THE USER IN ────────────────────────────────────────────────
loginUser($user);

// Get full name for the welcome message
$fullName = getUserFullName($user);
setFlash('success', 'Welcome back, ' . htmlspecialchars($fullName) . '! 🧋');

// ── REDIRECT BASED ON USER ROLE ──────────────────────────────────────────────
// Check if the user is an admin
if (($user['role'] ?? '') === 'admin') {
    // Admin goes to admin dashboard
    redirect('admin/index.php');
} else {
    // Customer goes to success page
    redirect('success.php');
}
?>
<?php
require_once '../function.php';
require_once '../validation.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// CSRF Check
$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission. Please try again.');
    redirect('login.php');
}
unset($_SESSION['csrf_token']);

// Collect & sanitize input
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate fields
$errors = validateLoginForm(['email' => $email, 'password' => $password]);

if (hasErrors($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old'] = ['email' => $email];
    redirect('login.php');
}

// Check credentials against database
$user = getUserByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_errors'] = [
        'general' => 'Incorrect email or password. Please try again.',
    ];
    $_SESSION['login_old'] = ['email' => $email];
    redirect('login.php');
}

// Check if user is admin
if (($user['role'] ?? '') !== 'admin') {
    $_SESSION['login_errors'] = [
        'general' => 'This account does not have admin access.',
    ];
    $_SESSION['login_old'] = ['email' => $email];
    redirect('login.php');
}

// Success — log the user in
loginUser($user);
setFlash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '! 🛡️');

// IMPORTANT: Redirect to index.php (NOT admin/index.php)
// Since we're already in the admin folder, just use index.php
redirect('index.php');
?>
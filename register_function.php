<?php
require_once 'function.php';
require_once 'validation.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// ── CSRF CHECK ──────────────────────────────────────────────────────────────
$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission. Please try again.');
    redirect('register.php');
}
// Rotate the token after use
unset($_SESSION['csrf_token']);

// ── COLLECT & SANITIZE INPUT ────────────────────────────────────────────────
$first_name      = sanitize($_POST['first_name'] ?? '');
$last_name       = sanitize($_POST['last_name'] ?? '');
$email           = sanitize($_POST['email'] ?? '');
$phone           = sanitize($_POST['phone'] ?? '');
$address         = sanitize($_POST['address'] ?? '');
$password        = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ── VALIDATE FIELDS ─────────────────────────────────────────────────────────
$errors = [];

// First Name
if (trim($first_name) === '') {
    $errors['first_name'] = 'First name is required.';
} elseif (strlen($first_name) < 2) {
    $errors['first_name'] = 'First name must be at least 2 characters.';
} elseif (strlen($first_name) > 100) {
    $errors['first_name'] = 'First name must not exceed 100 characters.';
} elseif (!preg_match('/^[\pL\s\-]+$/u', $first_name)) {
    $errors['first_name'] = 'First name may only contain letters, spaces, and hyphens.';
}

// Last Name
if (trim($last_name) === '') {
    $errors['last_name'] = 'Last name is required.';
} elseif (strlen($last_name) < 2) {
    $errors['last_name'] = 'Last name must be at least 2 characters.';
} elseif (strlen($last_name) > 100) {
    $errors['last_name'] = 'Last name must not exceed 100 characters.';
} elseif (!preg_match('/^[\pL\s\-]+$/u', $last_name)) {
    $errors['last_name'] = 'Last name may only contain letters, spaces, and hyphens.';
}

// Email
$emailError = validateEmail($email);
if ($emailError) $errors['email'] = $emailError;

// Check if email already exists
if (!isset($errors['email'])) {
    $existingUser = getUserByEmail($email);
    if ($existingUser) {
        $errors['email'] = 'This email is already registered. Please login or use a different email.';
    }
}

// Phone (optional - but validate if provided)
if (!empty($phone) && !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

// Address (optional - but validate if provided)
if (!empty($address) && strlen($address) > 500) {
    $errors['address'] = 'Address must not exceed 500 characters.';
}

// Password
$passwordError = validatePassword($password);
if ($passwordError) $errors['password'] = $passwordError;

// Confirm Password
if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

// ── IF ERRORS EXIST ─────────────────────────────────────────────────────────
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
    ];
    redirect('register.php');
}

// ── CREATE USER IN DATABASE ──────────────────────────────────────────────────
global $conn;

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO users (first_name, last_name, email, password, phone, address, role, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, 'customer', NOW())
");
$stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashedPassword, $phone, $address);

if (!$stmt->execute()) {
    // Check for duplicate email (safety net)
    if ($conn->errno === 1062) { // Duplicate entry error
        $_SESSION['register_errors'] = ['email' => 'This email is already registered.'];
        $_SESSION['register_old'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ];
        redirect('register.php');
    }
    
    // Other database error
    setFlash('error', 'Registration failed. Please try again later.');
    redirect('register.php');
}

// ── SUCCESS — LOG THE USER IN AUTOMATICALLY ──────────────────────────────────
// Get the newly created user
$newUser = getUserByEmail($email);

if ($newUser) {
    loginUser($newUser);
    setFlash('success', 'Welcome to CadienTea, ' . htmlspecialchars($first_name) . '! 🧋 Your account has been created.');
    redirect('success.php');
} else {
    // Fallback - user created but login failed
    setFlash('success', 'Account created successfully! Please login.');
    redirect('login.php');
}
?>
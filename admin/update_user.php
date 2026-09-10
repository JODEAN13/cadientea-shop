<?php
require_once '../function.php';
require_once '../validation.php';  // ← ADD THIS LINE
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}

$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission.');
    redirect('users.php');
}

$userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$firstName = sanitize($_POST['first_name'] ?? '');
$lastName = sanitize($_POST['last_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$address = sanitize($_POST['address'] ?? '');
$role = sanitize($_POST['role'] ?? 'customer');
$password = $_POST['password'] ?? '';

if ($userId <= 0 || empty($firstName) || empty($lastName) || empty($email)) {
    setFlash('error', 'Required fields missing.');
    redirect('edit_user.php?id=' . $userId);
}

global $conn;

if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, role = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $firstName, $lastName, $email, $phone, $address, $role, $hashedPassword, $userId);
} else {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $firstName, $lastName, $email, $phone, $address, $role, $userId);
}

if ($stmt->execute()) {
    setFlash('success', '✅ User updated successfully!');
} else {
    setFlash('error', 'Failed to update user.');
}

redirect('users.php');
?>
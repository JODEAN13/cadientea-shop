<?php
require_once '../function.php';
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}

$name = sanitize($_POST['name'] ?? '');

if (empty($name)) {
    setFlash('error', 'Category name is required.');
    redirect('categories.php');
}

global $conn;
$stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $name);

if ($stmt->execute()) {
    setFlash('success', '✅ Category "' . $name . '" added successfully!');
} else {
    setFlash('error', 'Failed to add category.');
}

redirect('categories.php');
?>
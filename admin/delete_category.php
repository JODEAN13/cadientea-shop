<?php
require_once '../function.php';
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}

$categoryId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($categoryId <= 0) {
    setFlash('error', 'Invalid category ID.');
    redirect('categories.php');
}

global $conn;

// Get category name first
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect('categories.php');
}

// Delete category (products will have category_id set to NULL)
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $categoryId);

if ($stmt->execute()) {
    setFlash('success', '🗑️ Category "' . $category['name'] . '" deleted successfully!');
} else {
    setFlash('error', 'Failed to delete category.');
}

redirect('categories.php');
?>
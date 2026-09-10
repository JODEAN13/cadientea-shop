<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

// CSRF Check
$submittedToken = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
    setFlash('error', 'Invalid form submission.');
    redirect('products.php');
}

$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($productId <= 0) {
    setFlash('error', 'Invalid product ID.');
    redirect('products.php');
}

// Get form data
$name = sanitize($_POST['name'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$tag = sanitize($_POST['tag'] ?? '');
$status = sanitize($_POST['status'] ?? 'available');

// Get sizes and prices
$sizes = $_POST['sizes'] ?? [];
$prices = $_POST['prices'] ?? [];
$sizeIds = $_POST['size_ids'] ?? [];

// Validate
$errors = [];

if (empty($name)) {
    $errors['name'] = 'Product name is required.';
} elseif (strlen($name) > 150) {
    $errors['name'] = 'Name must not exceed 150 characters.';
}

if (!empty($tag) && strlen($tag) > 50) {
    $errors['tag'] = 'Tag must not exceed 50 characters.';
}

if (empty($sizes) || empty($prices)) {
    $errors['sizes'] = 'At least one size is required.';
}

if (!empty($_FILES['image']['name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowed)) {
        $errors['image'] = 'Only JPG, PNG, WEBP, AVIF, and GIF images are allowed.';
    }
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $errors['image'] = 'Image size must be less than 2MB.';
    }
}

if (!empty($errors)) {
    $_SESSION['product_errors'] = $errors;
    $_SESSION['product_old'] = ['name' => $name, 'description' => $description, 'category_id' => $category_id, 'tag' => $tag, 'status' => $status];
    redirect('edit_product.php?id=' . $productId);
}

global $conn;

// Handle image upload
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $target = '../images/' . $filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $imagePath = 'images/' . $filename;
}

// Update product
if ($imagePath) {
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, category_id = ?, image = ?, tag = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssisssi", $name, $description, $category_id, $imagePath, $tag, $status, $productId);
} else {
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, category_id = ?, tag = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $name, $description, $category_id, $tag, $status, $productId);
}

if (!$stmt->execute()) {
    setFlash('error', 'Failed to update product.');
    redirect('products.php');
}

// Delete all existing sizes for this product
$stmt = $conn->prepare("DELETE FROM product_sizes WHERE product_id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();

// Insert new sizes
foreach ($sizes as $index => $size) {
    $size = sanitize($size);
    $price = (float)$prices[$index];
    
    if (!empty($size) && $price > 0) {
        $stmt2 = $conn->prepare("INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)");
        $stmt2->bind_param("isd", $productId, $size, $price);
        $stmt2->execute();
    }
}

setFlash('success', '✅ Product "' . $name . '" updated successfully with ' . count($sizes) . ' size(s)!');
redirect('products.php');
?>
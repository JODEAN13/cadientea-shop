<?php
require_once '../function.php';
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

// Get form data
$name = sanitize($_POST['name'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$tag = sanitize($_POST['tag'] ?? '');
$status = sanitize($_POST['status'] ?? 'available');

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
    redirect('add_product.php');
}

// Handle image upload
$imagePath = '';
if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $target = '../images/' . $filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $imagePath = 'images/' . $filename;
}

// Insert product
global $conn;
$stmt = $conn->prepare("INSERT INTO products (name, description, category_id, image, tag, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $name, $description, $category_id, $imagePath, $tag, $status);

if ($stmt->execute()) {
    setFlash('success', '✅ Product "' . $name . '" added successfully!');
} else {
    setFlash('error', 'Failed to add product. Please try again.');
}

redirect('products.php');
?>
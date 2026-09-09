<?php
require_once 'function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if ($index >= 0) {
    removeFromCart($index);
    setFlash('success', 'Item removed from your order.');
} else {
    setFlash('error', 'Invalid item.');
}

redirect('cart.php');
?>
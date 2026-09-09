<?php
require_once 'function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($index >= 0) {
    updateCartQuantity($index, $quantity);
}

redirect('cart.php');
?>
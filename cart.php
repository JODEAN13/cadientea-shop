<?php
require_once 'function.php';
requireLogin();

$cartItems = getCartItems();
$total = getCartTotal();
$count = getCartCount();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Order – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .cart-page {
            min-height: 100vh;
            padding-top: 68px;
            background: #fff8fb;
        }
        
        .cart-banner {
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 100%);
            padding: 3rem 1.5rem 2rem;
            text-align: center;
        }
        .cart-banner h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700;
            color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .cart-banner p {
            font-size: 0.95rem;
            color: #5c3a43;
        }
        
        .cart-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
        }
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .cart-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table th {
            text-align: left;
            padding: 0.75rem 0.5rem;
            border-bottom: 2px solid #f5c6d8;
            font-family: 'Fredoka', sans-serif;
            color: #5c3a43;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .cart-table td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #fce8f1;
            vertical-align: middle;
        }
        .cart-table tr:last-child td {
            border-bottom: none;
        }
        
        .cart-item-name {
            font-weight: 600;
            color: #1a0a10;
        }
        .cart-item-size {
            font-size: 0.8rem;
            color: #8a4a60;
        }
        .cart-item-price {
            font-weight: 600;
            color: #1a0a10;
        }
        
        .qty-input {
            width: 60px;
            padding: 0.3rem 0.5rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.5rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.9rem;
            text-align: center;
        }
        .qty-input:focus {
            border-color: #ec008c;
            outline: none;
        }
        
        .btn-remove {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: background 0.15s;
        }
        .btn-remove:hover {
            background: #fee2e2;
        }
        
        .cart-summary {
            background: #fce8f1;
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-top: 1.5rem;
        }
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 1rem;
        }
        .cart-summary-row.total {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            border-top: 2px solid #f5c6d8;
            margin-top: 0.5rem;
            padding-top: 1rem;
            color: #1a0a10;
        }
        .cart-summary-row.total .amount {
            color: #ec008c;
        }
        
        .cart-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .btn-checkout {
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.85rem 2.5rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: transform 0.15s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
        }
        .btn-checkout:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-continue {
            background: transparent;
            color: #ec008c;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.85rem 2rem;
            border: 2px solid #ec008c;
            border-radius: 999px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-continue:hover {
            background: #fce8f1;
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-cart .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .empty-cart h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.5rem;
            color: #1a0a10;
            margin-bottom: 0.5rem;
        }
        .empty-cart p {
            color: #8a4a60;
            margin-bottom: 1.5rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #8a4a60;
            transition: color 0.2s;
            text-decoration: none;
        }
        .back-link:hover {
            color: #ec008c;
        }
        
        @media (max-width: 600px) {
            .cart-card { padding: 1rem; }
            .cart-table { font-size: 0.85rem; }
            .cart-table th, .cart-table td { padding: 0.5rem 0.3rem; }
            .cart-summary { padding: 1rem; }
            .cart-actions { flex-direction: column; }
            .btn-checkout, .btn-continue { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">
            <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        </a>
        <div class="nav-links">
            <a href="index.php#menu">Menu</a>
            <a href="index.php#about">About</a>
            <a href="index.php#community">Community</a>
            <a href="cart.php">🛒 Cart (<?= getCartCount() ?>)</a>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <span style="font-weight:600; color:#5c3a43;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
            <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
            <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
        </div>
    </div>
</nav>

<!-- CART PAGE -->
<div class="cart-page">
    <div class="cart-banner">
        <h1>🧋 Your Order</h1>
        <p>Review your items before checkout.</p>
    </div>

    <div class="cart-content">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="cart-card">
                <div class="empty-cart">
                    <div class="icon">🍵</div>
                    <h2>Your order is empty</h2>
                    <p>Looks like you haven't added any items yet. Browse our menu and find your perfect bubble tea!</p>
                    <a href="index.php#menu" class="btn-checkout" style="display:inline-block;">Browse Menu</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="cart-card">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $index => $item): ?>
                            <tr>
                                <td class="cart-item-name"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="cart-item-size"><?= htmlspecialchars($item['size']) ?></td>
                                <td class="cart-item-price">₱<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <form action="update_cart.php" method="POST" style="display:flex; align-items:center; gap:0.5rem;">
                                        <input type="hidden" name="index" value="<?= $index ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" class="qty-input" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="cart-item-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td>
                                    <form action="remove_from_cart.php" method="POST">
                                        <input type="hidden" name="index" value="<?= $index ?>">
                                        <button type="submit" class="btn-remove" title="Remove">✕</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span>₱<?= number_format($total, 2) ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>Delivery Fee</span>
                    <span>₱50.00</span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total</span>
                    <span class="amount">₱<?= number_format($total + 50, 2) ?></span>
                </div>
            </div>

            <!-- Cart Actions -->
            <div class="cart-actions">
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout →</a>
                <a href="index.php#menu" class="btn-continue">Continue Shopping</a>
            </div>
        <?php endif; ?>

        <a href="index.php" class="back-link">← Back to Home</a>
    </div>
</div>

</body>
</html>
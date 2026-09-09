<?php
require_once 'function.php';
require_once 'validation.php';  // ← Add this line
requireLogin();

$cartItems = getCartItems();
$total = getCartTotal();
$deliveryFee = 50;
$grandTotal = $total + $deliveryFee;


if (empty($cartItems)) {
    setFlash('error', 'Your cart is empty. Please add items first.');
    redirect('cart.php');
}

$user = getUserById($_SESSION['user_id']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .checkout-page {
            min-height: 100vh;
            padding-top: 68px;
            background: #fff8fb;
        }
        .checkout-banner {
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 100%);
            padding: 3rem 1.5rem 2rem;
            text-align: center;
        }
        .checkout-banner h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700;
            color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .checkout-banner p {
            font-size: 0.95rem;
            color: #5c3a43;
        }
        
        .checkout-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .checkout-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .checkout-card h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #1a0a10;
        }
        
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1a0a10;
            margin-bottom: 0.3rem;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.75rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.95rem;
            color: #1a0a10;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #ec008c;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .order-summary {
            background: #fce8f1;
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(245, 198, 216, 0.5);
            font-size: 0.9rem;
        }
        .order-summary-item:last-child {
            border-bottom: none;
        }
        .order-summary-total {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            padding-top: 0.75rem;
            border-top: 2px solid #f5c6d8;
            margin-top: 0.5rem;
        }
        
        .btn-place-order {
            width: 100%;
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.85rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: transform 0.15s;
            margin-top: 1rem;
        }
        .btn-place-order:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        @media (max-width: 700px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }
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
            <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
        </div>
    </div>
</nav>

<!-- CHECKOUT -->
<div class="checkout-page">
    <div class="checkout-banner">
        <h1>📋 Checkout</h1>
        <p>Confirm your order and delivery details.</p>
    </div>

    <div class="checkout-content">
        
        <!-- Left: Form -->
        <div>
            <?php if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="checkout-card">
                <h2>📦 Delivery Details</h2>
                <form action="process_order.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address</label>
                        <textarea id="delivery_address" name="delivery_address" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="cash">Cash on Delivery</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-place-order">Place Order 🧋</button>
                </form>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div>
            <div class="order-summary">
                <h2 style="font-family:'Fredoka',sans-serif; font-size:1.1rem; margin-bottom:1rem; color:#1a0a10;">🛒 Order Summary</h2>
                
                <?php foreach ($cartItems as $item): ?>
                    <div class="order-summary-item">
                        <span>
                            <?= htmlspecialchars($item['product_name']) ?>
                            <small style="color:#8a4a60;"> (<?= htmlspecialchars($item['size']) ?>)</small>
                            <br><small style="color:#8a4a60;">× <?= $item['quantity'] ?></small>
                        </span>
                        <span>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-summary-item">
                    <span>Subtotal</span>
                    <span>₱<?= number_format($total, 2) ?></span>
                </div>
                <div class="order-summary-item">
                    <span>Delivery Fee</span>
                    <span>₱<?= number_format($deliveryFee, 2) ?></span>
                </div>
                <div class="order-summary-total">
                    <span>Total</span>
                    <span>₱<?= number_format($grandTotal, 2) ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
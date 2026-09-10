<?php
require_once 'function.php';
require_once 'validation.php';
requireLogin();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId <= 0) {
    redirect('index.php');
}

$order = getOrderById($orderId);
$orderItems = getOrderItems($orderId);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    setFlash('error', 'Order not found.');
    redirect('index.php');
}

$flash = getFlash();
$isPickup = ($order['order_type'] ?? 'delivery') === 'pickup';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmed – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .success-page { min-height: 100vh; padding-top: 68px; background: #fff8fb; }
        .success-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
            text-align: center;
        }
        .success-icon { font-size: 5rem; margin-bottom: 1rem; }
        .success-content h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 2.2rem;
            color: #1a0a10;
            margin-bottom: 0.5rem;
        }
        .success-content .order-number {
            font-size: 1.1rem;
            color: #8a4a60;
            margin-bottom: 0.5rem;
        }
        .success-content .order-number strong {
            color: #ec008c;
            font-size: 1.3rem;
        }
        .success-content p {
            color: #5c3a43;
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }
        
        /* Pickup/Delivery Badge */
        .delivery-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .delivery-badge.delivery {
            background: #dbeafe;
            color: #1e40af;
        }
        .delivery-badge.pickup {
            background: #d1fae5;
            color: #065f46;
        }
        
        .pickup-reminder {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .pickup-reminder h3 {
            font-family: 'Fredoka', sans-serif;
            color: #166534;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        .pickup-reminder p {
            color: #166534;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .order-details {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .order-details h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #1a0a10;
        }
        .order-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #fce8f1;
            font-size: 0.9rem;
        }
        .order-detail-row:last-child {
            border-bottom: none;
        }
        .order-detail-row .label {
            color: #8a4a60;
        }
        
        .success-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn-primary-custom {
            display: inline-block;
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.8rem 2rem;
            border-radius: 999px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: transform 0.15s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
        }
        .btn-outline-custom {
            display: inline-block;
            background: transparent;
            color: #ec008c;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.8rem 2rem;
            border: 2px solid #ec008c;
            border-radius: 999px;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn-outline-custom:hover {
            background: #fce8f1;
        }
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #fef3c7;
            color: #92400e;
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
            <a href="menu.php">Menu</a>
            <a href="about.php">About</a>
            <a href="community.php">Community</a>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
            <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
        </div>
    </div>
</nav>

<!-- SUCCESS -->
<div class="success-page">
    <div class="success-content">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="success-icon">🎉</div>
        <h1>Order Confirmed!</h1>
        <p class="order-number">Order #<strong><?= htmlspecialchars($order['order_number']) ?></strong></p>
        
        <!-- Delivery Type Badge -->
        <?php if ($isPickup): ?>
            <div class="delivery-badge pickup">🏪 PICKUP ORDER</div>
        <?php else: ?>
            <div class="delivery-badge delivery">🚚 DELIVERY ORDER</div>
        <?php endif; ?>
        
        <p>Thank you for your order! We'll start preparing your bubble teas right away. 🧋</p>

        <?php if ($isPickup): ?>
            <!-- Pickup Reminder -->
            <div class="pickup-reminder">
                <h3>🏪 Pickup Instructions</h3>
                <p><strong>📍 Location:</strong> CadienTea Main Branch, Dumaguete City, Negros Oriental</p>
                <p><strong>🕐 Store Hours:</strong></p>
                <p style="margin-left: 1rem;">
                    Mon–Fri: 7:00 AM – 8:00 PM<br>
                    Saturday: 8:00 AM – 9:00 PM<br>
                    Sunday: 9:00 AM – 6:00 PM
                </p>
                <p style="margin-top: 0.75rem; font-style: italic;">
                    💡 We'll notify you when your order is ready for pickup!
                </p>
            </div>
        <?php endif; ?>

        <div class="order-details">
            <h2>📋 Order Summary</h2>
            
            <?php foreach ($orderItems as $item): ?>
                <div class="order-detail-row">
                    <span>
                        <?= htmlspecialchars($item['product_name']) ?>
                        <small style="color:#8a4a60;"> (<?= htmlspecialchars($item['size']) ?>) × <?= $item['quantity'] ?></small>
                    </span>
                    <span>₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            
            <div class="order-detail-row">
                <span class="label">Subtotal</span>
                <span>₱<?= number_format($order['subtotal'], 2) ?></span>
            </div>
            <div class="order-detail-row">
                <span class="label"><?= $isPickup ? 'Pickup Fee' : 'Delivery Fee' ?></span>
                <span>
                    <?php if ($isPickup): ?>
                        <span style="color:#059669; font-weight:700;">FREE</span>
                    <?php else: ?>
                        ₱<?= number_format($order['delivery_fee'], 2) ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="order-detail-row" style="font-weight:700; font-size:1.1rem; border-top:2px solid #f5c6d8; padding-top:0.75rem;">
                <span>Total</span>
                <span style="color:#ec008c;">₱<?= number_format($order['total_amount'], 2) ?></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Status</span>
                <span><span class="status-badge"><?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?></span></span>
            </div>
            <div class="order-detail-row">
                <span class="label">Payment</span>
                <span><?= str_replace('_', ' ', htmlspecialchars($order['payment_method'] ?? 'Cash')) ?></span>
            </div>
            
            <!-- Order Address (works for both pickup & delivery) -->
            <div class="order-detail-row">
                <span class="label"><?= $isPickup ? 'Pickup Location' : 'Delivery Address' ?></span>
                <span><?= htmlspecialchars($order['order_address'] ?? 'N/A') ?></span>
            </div>
        </div>

        <div class="success-actions">
            <a href="menu.php" class="btn-primary-custom">Order More 🧋</a>
            <a href="purchases.php" class="btn-outline-custom">View My Orders</a>
        </div>
    </div>
</div>

</body>
</html>
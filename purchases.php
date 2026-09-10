<?php
require_once 'function.php';
require_once 'validation.php';
requireLogin();

$user = getUserById((int) $_SESSION['user_id']);
$orders = getUserOrders($user['id']);
$flash = getFlash();

if (!$user) {
    logoutUser();
    redirect('login.php');
}

$fullName = getUserFullName($user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Purchases – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .nav-user { display: flex; align-items: center; gap: 1rem; }
        .btn-logout {
            font-family: 'Fredoka', sans-serif;
            font-weight: 600; font-size: 0.85rem;
            color: #ec008c; background: #fce8f1;
            border: 1.5px solid #f5c6d8;
            padding: 0.4rem 1rem; border-radius: 999px;
            cursor: pointer; transition: background 0.15s;
            text-decoration: none;
        }
        .btn-logout:hover { background: #f8b5c2; }

        .purchases-page { min-height: 100vh; padding-top: 68px; background: #fff8fb; }
        .purchases-banner {
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 100%);
            padding: 3rem 1.5rem 2rem;
            text-align: center;
        }
        .purchases-banner h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700; color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .purchases-banner p { font-size: 0.95rem; color: #5c3a43; }

        .purchases-content { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }

        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .order-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #fce8f1;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .order-card-header .order-num {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1a0a10;
        }
        .order-card-header .order-date {
            font-size: 0.85rem;
            color: #8a4a60;
        }
        .order-body {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .order-body .label {
            font-size: 0.75rem;
            color: #8a4a60;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }
        .order-body .value {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            color: #1a0a10;
        }
        .order-status {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dbeafe; color: #1e40af; }
        .status-preparing { background: #e0e7ff; color: #3730a3; }
        .status-ready { background: #d1fae5; color: #065f46; }
        .status-out_for_delivery { background: #fce4ec; color: #9a3412; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        /* Order Type Badge */
        .order-type-badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .type-delivery { background: #dbeafe; color: #1e40af; }
        .type-pickup { background: #d1fae5; color: #065f46; }

        .btn-view-order {
            display: inline-block;
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-view-order:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(236,0,140,0.3);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .empty-state h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.5rem;
            color: #1a0a10;
            margin-bottom: 0.5rem;
        }
        .empty-state p {
            color: #8a4a60;
            margin-bottom: 1.5rem;
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
            transition: all 0.15s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236,0,140,0.35);
        }

        @media (max-width: 600px) {
            .order-body { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 400px) {
            .order-body { grid-template-columns: 1fr; }
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
        <div class="nav-user">
            <a href="cart.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">🛒 Cart (<?= getCartCount() ?>)</a>
            <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<!-- PURCHASES PAGE -->
<div class="purchases-page">
    <div class="purchases-banner">
        <h1>📦 My Purchases</h1>
        <p>Track and view all your CadienTea orders.</p>
    </div>

    <div class="purchases-content">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="icon">🍵</div>
                <h2>No orders yet</h2>
                <p>You haven't placed any orders. Start your boba journey now!</p>
                <a href="menu.php" class="btn-primary-custom">🧋 Browse Menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php $isPickup = ($order['order_type'] ?? 'delivery') === 'pickup'; ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <div>
                            <div class="order-num">#<?= htmlspecialchars($order['order_number']) ?></div>
                            <div class="order-date"><?= date('F j, Y - h:i A', strtotime($order['created_at'])) ?></div>
                        </div>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <span class="order-type-badge type-<?= $isPickup ? 'pickup' : 'delivery' ?>">
                                <?= $isPickup ? '🏪 Pickup' : '🚚 Delivery' ?>
                            </span>
                            <span class="order-status status-<?= str_replace(' ', '_', $order['order_status']) ?>">
                                <?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-body">
                        <div>
                            <div class="label">Total Amount</div>
                            <div class="value">₱<?= number_format($order['total_amount'], 2) ?></div>
                        </div>
                        <div>
                            <div class="label">Payment Method</div>
                            <div class="value"><?= str_replace('_', ' ', htmlspecialchars($order['payment_method'] ?? 'Cash')) ?></div>
                        </div>
                        <div>
                            <div class="label"><?= $isPickup ? 'Pickup Location' : 'Delivery Address' ?></div>
                            <div class="value" style="font-size:0.85rem; font-weight:500;">
                                <?= htmlspecialchars($order['order_address'] ?? 'N/A') ?>
                            </div>
                        </div>
                    </div>
                    <a href="order_success.php?order_id=<?= $order['id'] ?>" class="btn-view-order">View Details →</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
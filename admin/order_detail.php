<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId <= 0) {
    setFlash('error', 'Invalid order ID.');
    redirect('orders.php');
}

global $conn;

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, u.first_name, u.last_name, u.email, u.phone, u.address 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('orders.php');
}

// Get order items
$itemsStmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemsStmt->bind_param("i", $orderId);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
$isPickup = ($order['order_type'] ?? 'delivery') === 'pickup';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order #<?= htmlspecialchars($order['order_number']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        .admin-wrapper { display: flex; min-height: 100vh; padding-top: 68px; }
        .admin-sidebar {
            width: 260px;
            background: #1a0a10;
            color: #fff;
            padding: 2rem 1.5rem;
            position: fixed;
            height: calc(100vh - 68px);
            overflow-y: auto;
            flex-shrink: 0;
        }
        .admin-sidebar .logo { text-align: center; margin-bottom: 2rem; }
        .admin-sidebar .logo img { width: 150px; margin: 0 auto; }
        .admin-sidebar .logo h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.2rem;
            color: #fff;
            margin-top: 0.5rem;
        }
        .admin-sidebar .logo h2 span { color: #f8b5c2; }
        
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 0.3rem; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            border-radius: 0.75rem;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: all 0.15s;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-menu a.active { background: #ec008c; color: #fff; }
        .sidebar-menu a .icon { font-size: 1.2rem; width: 28px; text-align: center; }
        .sidebar-divider { border: none; border-top: 1px solid rgba(255,255,255,0.08); margin: 1rem 0; }
        .sidebar-footer { margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.08); }
        .sidebar-footer a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 0.85rem;
            display: block;
            padding: 0.4rem 0;
        }
        .sidebar-footer a:hover { color: #fff; }
        
        .admin-main {
            margin-left: 260px;
            flex: 1;
            background: #fff8fb;
            min-height: calc(100vh - 68px);
            padding: 2rem;
        }
        .admin-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header-bar h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.8rem;
            color: #1a0a10;
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
        
        .detail-card {
            background: #fff;
            border-radius: 1.25rem;
            border: 1px solid #f5c6d8;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .detail-card h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.1rem;
            color: #1a0a10;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #fce8f1;
        }
        
        .detail-row {
            display: flex;
            padding: 0.5rem 0;
            border-bottom: 1px solid #fce8f1;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            width: 180px;
            font-weight: 600;
            color: #5c3a43;
            flex-shrink: 0;
        }
        .detail-value { color: #1a0a10; }
        
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
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
            padding: 0.25rem 0.9rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .type-delivery { background: #dbeafe; color: #1e40af; }
        .type-pickup { background: #d1fae5; color: #065f46; }
        
        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table th {
            text-align: left;
            padding: 0.5rem 0.5rem;
            border-bottom: 2px solid #f5c6d8;
            font-size: 0.8rem;
            color: #5c3a43;
            font-weight: 600;
            text-transform: uppercase;
        }
        .item-table td {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #fce8f1;
        }
        
        .back-link {
            display: inline-block;
            color: #ec008c;
            font-weight: 600;
            text-decoration: none;
            margin-top: 1rem;
        }
        .back-link:hover { text-decoration: underline; }
        
        .total-row {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #ec008c;
        }
        
        .view-store-btn {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: #10b981;
            color: #fff;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.15s;
        }
        .view-store-btn:hover { background: #059669; transform: translateY(-1px); }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; }
            .admin-main { margin-left: 0; }
            .admin-wrapper { flex-direction: column; }
            .detail-row { flex-direction: column; }
            .detail-label { width: 100%; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="../index.php" class="nav-logo">
            <img src="../images/cadienteamainlogo.png" alt="CadienTea logo" />
        </a>
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
            <a href="../logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
        </div>
    </div>
</nav>

<!-- PAGE -->
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="logo">
            <img src="../images/cadienteamainlogo.png" alt="CadienTea" />
            <h2>Cadien<span>Tea</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">📊</span> Dashboard</a></li>
            <li><a href="orders.php" class="active"><span class="icon">📦</span> Orders</a></li>
            <li><a href="products.php"><span class="icon">🧋</span> Products</a></li>
            <li><a href="categories.php"><span class="icon">🏷️</span> Categories</a></li>
            <li><a href="users.php"><span class="icon">👤</span> Users</a></li>
        </ul>
        <hr class="sidebar-divider">
        <div class="sidebar-footer">
            <a href="../index.php">👁 View Store</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </aside>
    
    <main class="admin-main">
        
        <div class="admin-header-bar">
            <h1>📋 Order #<?= htmlspecialchars($order['order_number']) ?></h1>
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Order Status & Type -->
        <div class="detail-card">
            <h2>📊 Order Status</h2>
            <div class="detail-row">
                <span class="detail-label">Order Type</span>
                <span class="detail-value">
                    <span class="order-type-badge type-<?= $isPickup ? 'pickup' : 'delivery' ?>">
                        <?= $isPickup ? '🏪 Pickup' : '🚚 Delivery' ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Current Status</span>
                <span class="detail-value">
                    <span class="status-badge status-<?= str_replace(' ', '_', $order['order_status']) ?>">
                        <?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method</span>
                <span class="detail-value"><?= str_replace('_', ' ', htmlspecialchars($order['payment_method'] ?? 'Cash')) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date</span>
                <span class="detail-value"><?= date('F j, Y h:i A', strtotime($order['created_at'])) ?></span>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="detail-card">
            <h2>👤 Customer Information</h2>
            <div class="detail-row">
                <span class="detail-label">Name</span>
                <span class="detail-value"><?= htmlspecialchars($order['first_name'] ?? '') . ' ' . htmlspecialchars($order['last_name'] ?? '') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value"><?= htmlspecialchars($order['email'] ?? '') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone</span>
                <span class="detail-value"><?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><?= $isPickup ? 'Pickup Location' : 'Delivery Address' ?></span>
                <span class="detail-value"><?= htmlspecialchars($order['order_address'] ?? 'N/A') ?></span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="detail-card">
            <h2>🧋 Order Items</h2>
            <table class="item-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['size'] ?? 'N/A') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                            <td>₱<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right; font-weight:600;">Subtotal:</td>
                        <td>₱<?= number_format($order['subtotal'], 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right; font-weight:600;"><?= $isPickup ? 'Pickup Fee:' : 'Delivery Fee:' ?></td>
                        <td>
                            <?php if ($isPickup): ?>
                                <span style="color:#059669; font-weight:700;">FREE</span>
                            <?php else: ?>
                                ₱<?= number_format($order['delivery_fee'], 2) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right; font-weight:700; font-size:1.1rem; color:#ec008c;">Total:</td>
                        <td class="total-row">₱<?= number_format($order['total_amount'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Actions -->
        <div class="detail-card">
            <h2>⚙️ Update Status</h2>
            <form action="update_status.php" method="POST" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status" class="status-select" style="padding:0.5rem 1rem; border-radius:0.75rem; border:1px solid #f5c6d8; font-family:'Fredoka', sans-serif;">
                    <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                    <option value="confirmed" <?= $order['order_status'] == 'confirmed' ? 'selected' : '' ?>>✅ Confirm</option>
                    <option value="preparing" <?= $order['order_status'] == 'preparing' ? 'selected' : '' ?>>🔪 Preparing</option>
                    <option value="ready" <?= $order['order_status'] == 'ready' ? 'selected' : '' ?>>📦 Ready</option>
                    <option value="out_for_delivery" <?= $order['order_status'] == 'out_for_delivery' ? 'selected' : '' ?>>🚚 Out for Delivery</option>
                    <option value="completed" <?= $order['order_status'] == 'completed' ? 'selected' : '' ?>>🎉 Completed</option>
                    <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : '' ?>>❌ Cancel</option>
                </select>
                <button type="submit" class="btn-primary" style="padding:0.5rem 1.5rem;">Update Status</button>
            </form>
        </div>

        <a href="orders.php" class="back-link">← Back to Orders</a>
    </main>
</div>

</body>
</html>
<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

// Get all orders with user details
global $conn;
$sql = "SELECT o.*, u.first_name, u.last_name, u.email, u.phone 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Get order counts by status
$statusCounts = [];
$statusQuery = "SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status";
$statusResult = $conn->query($statusQuery);
while ($row = $statusResult->fetch_assoc()) {
    $statusCounts[$row['order_status']] = $row['count'];
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Orders</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            padding-top: 68px;
        }
        
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
        .sidebar-menu .badge {
            margin-left: auto;
            background: #ec008c;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
        }
        
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1rem;
            text-align: center;
            border: 1px solid #f5c6d8;
            box-shadow: 0 2px 8px rgba(236,0,140,0.05);
        }
        .stat-card .number {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a0a10;
        }
        .stat-card .label {
            font-size: 0.75rem;
            color: #8a4a60;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .stat-card .label.pending { color: #92400e; }
        .stat-card .label.completed { color: #065f46; }
        .stat-card .label.cancelled { color: #991b1b; }
        
        .table-wrapper {
            overflow-x: auto;
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #f5c6d8;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        .order-table th {
            background: #fce8f1;
            padding: 0.75rem 1rem;
            text-align: left;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.8rem;
            color: #5c3a43;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        .order-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #fce8f1;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        .order-table tr:hover { background: #fff8fb; }
        
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 999px;
            font-size: 0.7rem;
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
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .type-delivery { background: #dbeafe; color: #1e40af; }
        .type-pickup { background: #d1fae5; color: #065f46; }
        
        .status-select {
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #f5c6d8;
            font-size: 0.75rem;
            font-family: 'Fredoka', sans-serif;
            background: #fff;
            cursor: pointer;
        }
        .btn-status {
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            background: #ec008c;
            color: #fff;
            transition: all 0.15s;
        }
        .btn-status:hover { opacity: 0.8; }
        
        .order-detail-link {
            color: #ec008c;
            font-weight: 600;
            text-decoration: none;
        }
        .order-detail-link:hover { text-decoration: underline; }
        
        .customer-info .name { font-weight: 600; color: #1a0a10; }
        .customer-info .email { color: #8a4a60; font-size: 0.75rem; }
        
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
        .view-store-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 {
            font-family: 'Fredoka', sans-serif;
            color: #1a0a10;
            margin-bottom: 0.5rem;
        }
        .empty-state p { color: #8a4a60; }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; }
            .admin-main { margin-left: 0; }
            .admin-wrapper { flex-direction: column; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .admin-header-bar h1 { font-size: 1.4rem; }
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

<!-- ADMIN -->
<div class="admin-wrapper">
    
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="logo">
            <img src="../images/cadienteamainlogo.png" alt="CadienTea" />
            <h2>Cadien<span>Tea</span></h2>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">📊</span> Dashboard</a></li>
            <li><a href="orders.php" class="active"><span class="icon">📦</span> Orders <span class="badge"><?= $statusCounts['pending'] ?? 0 ?></span></a></li>
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
    
    <!-- MAIN -->
    <main class="admin-main">
        
        <div class="admin-header-bar">
            <h1>📦 Orders</h1>
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= count($orders) ?></div>
                <div class="label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $statusCounts['pending'] ?? 0 ?></div>
                <div class="label pending">⏳ Pending</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $statusCounts['completed'] ?? 0 ?></div>
                <div class="label completed">✅ Completed</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $statusCounts['cancelled'] ?? 0 ?></div>
                <div class="label cancelled">❌ Cancelled</div>
            </div>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="icon">🍵</div>
                <h3>No Orders Yet</h3>
                <p>When customers place orders, they'll appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php $isPickup = ($order['order_type'] ?? 'delivery') === 'pickup'; ?>
                            <tr>
                                <td>
                                    <a href="order_detail.php?order_id=<?= $order['id'] ?>" class="order-detail-link">
                                        #<?= htmlspecialchars($order['order_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="name">
                                            <?= htmlspecialchars($order['first_name'] ?? '') . ' ' . htmlspecialchars($order['last_name'] ?? '') ?>
                                        </div>
                                        <div class="email"><?= htmlspecialchars($order['email'] ?? '') ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="order-type-badge type-<?= $isPickup ? 'pickup' : 'delivery' ?>">
                                        <?= $isPickup ? '🏪 Pickup' : '🚚 Delivery' ?>
                                    </span>
                                </td>
                                <td><strong>₱<?= number_format($order['total_amount'], 2) ?></strong></td>
                                <td><?= str_replace('_', ' ', htmlspecialchars($order['payment_method'] ?? 'Cash')) ?></td>
                                <td>
                                    <span class="status-badge status-<?= str_replace(' ', '_', $order['order_status']) ?>">
                                        <?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?>
                                    </span>
                                </td>
                                <td style="font-size:0.8rem; color:#8a4a60; white-space:nowrap;">
                                    <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                    <br><small><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <form action="update_status.php" method="POST" style="display:flex; gap:0.3rem; flex-wrap:wrap; align-items:center;">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <select name="status" class="status-select">
                                            <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                                            <option value="confirmed" <?= $order['order_status'] == 'confirmed' ? 'selected' : '' ?>>✅ Confirm</option>
                                            <option value="preparing" <?= $order['order_status'] == 'preparing' ? 'selected' : '' ?>>🔪 Preparing</option>
                                            <option value="ready" <?= $order['order_status'] == 'ready' ? 'selected' : '' ?>>📦 Ready</option>
                                            <option value="out_for_delivery" <?= $order['order_status'] == 'out_for_delivery' ? 'selected' : '' ?>>🚚 Out for Delivery</option>
                                            <option value="completed" <?= $order['order_status'] == 'completed' ? 'selected' : '' ?>>🎉 Completed</option>
                                            <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : '' ?>>❌ Cancel</option>
                                        </select>
                                        <button type="submit" class="btn-status">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
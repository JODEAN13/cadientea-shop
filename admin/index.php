<?php
require_once '../function.php';

// If not logged in, redirect to admin login
if (!isLoggedIn()) {
    redirect('login.php');
}

// If logged in but not admin, show error
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    setFlash('error', 'You do not have admin access.');
    redirect('../index.php');
}

// If logged in and admin, continue
// ... rest of your admin dashboard code
requireLogin();
requireAdmin();

global $conn;

// Get statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['count'];

// Pending orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['total_products'] = $result->fetch_assoc()['count'];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status = 'completed'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$recentOrders = $conn->query("
    SELECT o.*, u.first_name, u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard – CadienTea</title>
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
        
        /* ── SIDEBAR ── */
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
        .admin-sidebar .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .admin-sidebar .logo img {
            width: 150px;
            margin: 0 auto;
        }
        .admin-sidebar .logo h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.2rem;
            color: #fff;
            margin-top: 0.5rem;
        }
        .admin-sidebar .logo h2 span {
            color: #f8b5c2;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 0.3rem;
        }
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
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .sidebar-menu a.active {
            background: #ec008c;
            color: #fff;
        }
        .sidebar-menu a .icon {
            font-size: 1.2rem;
            width: 28px;
            text-align: center;
        }
        .sidebar-menu .badge {
            margin-left: auto;
            background: #ec008c;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
        }
        
        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 1rem 0;
        }
        
        .sidebar-footer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-footer a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 0.85rem;
            display: block;
            padding: 0.4rem 0;
        }
        .sidebar-footer a:hover {
            color: #fff;
        }
        
        /* ── MAIN CONTENT ── */
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
        .admin-header-bar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .admin-header-bar .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ec008c;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .admin-header-bar .user-info .name {
            font-weight: 600;
            color: #1a0a10;
        }
        .admin-header-bar .user-info .role {
            font-size: 0.75rem;
            color: #8a4a60;
            text-transform: uppercase;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #f5c6d8;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-number {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #1a0a10;
        }
        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #8a4a60;
            font-weight: 500;
        }
        .stat-card .stat-change {
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }
        .stat-card .stat-change.positive { color: #059669; }
        .stat-card .stat-change.negative { color: #dc2626; }
        
        /* Recent Orders */
        .recent-orders {
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #f5c6d8;
            padding: 1.5rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .recent-orders h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.1rem;
            color: #1a0a10;
            margin-bottom: 1rem;
        }
        .recent-orders table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-orders th {
            text-align: left;
            padding: 0.5rem 0.5rem;
            border-bottom: 2px solid #fce8f1;
            font-size: 0.8rem;
            color: #5c3a43;
            text-transform: uppercase;
        }
        .recent-orders td {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #fce8f1;
            font-size: 0.9rem;
        }
        .recent-orders .status-badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.65rem;
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
        
        .view-all-link {
            display: inline-block;
            margin-top: 1rem;
            color: #ec008c;
            font-weight: 600;
            text-decoration: none;
        }
        .view-all-link:hover {
            text-decoration: underline;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .quick-action {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #f5c6d8;
            text-decoration: none;
            transition: all 0.15s;
        }
        .quick-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(236,0,140,0.12);
        }
        .quick-action .icon {
            font-size: 2rem;
            margin-bottom: 0.3rem;
        }
        .quick-action .label {
            font-size: 0.85rem;
            font-weight: 600;
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
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }
            .admin-main {
                margin-left: 0;
            }
            .admin-wrapper {
                flex-direction: column;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .admin-header-bar h1 {
                font-size: 1.4rem;
            }
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
            <a href="../index.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#10b981; box-shadow:none;">👁 View Store</a>
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
            <li><a href="index.php" class="active"><span class="icon">📊</span> Dashboard</a></li>
            <li><a href="orders.php"><span class="icon">📦</span> Orders <span class="badge"><?= $stats['pending_orders'] ?? 0 ?></span></a></li>
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
            <h1>Dashboard</h1>
            <div class="user-info">
                <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
                <div>
                    <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
                    <div class="role">Administrator</div>
                </div>
            </div>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="orders.php" class="quick-action">
                <div class="icon">📦</div>
                <div class="label">View Orders</div>
            </a>
            <a href="products.php" class="quick-action">
                <div class="icon">🧋</div>
                <div class="label">Manage Products</div>
            </a>
            <a href="add_product.php" class="quick-action">
                <div class="icon">➕</div>
                <div class="label">Add Product</div>
            </a>
            <a href="../index.php" class="quick-action">
                <div class="icon">👁</div>
                <div class="label">View Store</div>
            </a>
        </div>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number">₱<?= number_format($stats['revenue'], 2) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?= $stats['total_orders'] ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-number" style="color:#92400e;"><?= $stats['pending_orders'] ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧋</div>
                <div class="stat-number"><?= $stats['total_products'] ?></div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Customers</div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="recent-orders">
            <h2>📋 Recent Orders</h2>
            
            <?php if (empty($recentOrders)): ?>
                <p style="color:#8a4a60; padding:1rem 0;">No orders yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="order_detail.php?order_id=<?= $order['id'] ?>" style="color:#ec008c; font-weight:600; text-decoration:none;">#<?= htmlspecialchars($order['order_number']) ?></a></td>
                                <td><?= htmlspecialchars($order['first_name'] ?? '') . ' ' . htmlspecialchars($order['last_name'] ?? '') ?></td>
                                <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                <td><span class="status-badge status-<?= str_replace(' ', '_', $order['order_status']) ?>"><?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?></span></td>
                                <td style="font-size:0.8rem; color:#8a4a60;"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="orders.php" class="view-all-link">View All Orders →</a>
            <?php endif; ?>
        </div>
        
    </main>
</div>

</body>
</html>
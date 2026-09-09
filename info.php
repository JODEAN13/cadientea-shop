<?php
require_once 'function.php';
requireLogin();

// Fetch fresh user data from DB
$user = getUserById((int) $_SESSION['user_id']);
$flash = getFlash();

if (!$user) {
    // User no longer exists in the DB — force logout
    logoutUser();
    redirect('login.php');
}

$fullName = getUserFullName($user);
$orders = getUserOrders($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Account – CadienTea</title>
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

        .info-page { min-height: 100vh; padding-top: 68px; background: #fff8fb; }
        .info-banner {
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 100%);
            padding: 3.5rem 1.5rem 2.5rem;
            text-align: center;
        }
        .info-banner h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700; color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .info-banner p { font-size: 0.95rem; color: #5c3a43; }

        .info-content { max-width: 860px; margin: 0 auto; padding: 3rem 1.5rem 5rem; }

        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .info-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem 2.25rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .info-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f5c6d8;
        }
        .info-card-header h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.15rem; font-weight: 700;
            color: #1a0a10;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.85rem 0;
            border-bottom: 1px solid #fce8f1;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #8a4a60;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            width: 140px;
            flex-shrink: 0;
            padding-top: 2px;
        }
        .info-value { font-size: 0.975rem; color: #1a0a10; font-weight: 500; }

        .avatar-circle {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8b5c2, #ec008c);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fredoka', sans-serif;
            font-size: 1.9rem; font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(236,0,140,0.25);
        }
        .user-header { display: flex; align-items: center; gap: 1.25rem; margin-bottom: 0.25rem; }
        .user-header-text h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.3rem; font-weight: 700; color: #1a0a10;
        }
        .user-header-text p { font-size: 0.875rem; color: #8a4a60; }
        .member-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            background: #fce8f1; color: #ec008c;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.75rem; font-weight: 700;
            padding: 0.25rem 0.75rem; border-radius: 999px;
            margin-top: 0.4rem;
            text-transform: uppercase; letter-spacing: 0.06em;
        }
        .role-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            background: #e0e7ff; color: #4338ca;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.7rem; font-weight: 700;
            padding: 0.2rem 0.6rem; border-radius: 999px;
            margin-left: 0.5rem;
            text-transform: uppercase; letter-spacing: 0.06em;
        }

        .order-status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
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

        .action-row { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 2rem; }
        .btn-action {
            font-family: 'Fredoka', sans-serif;
            font-weight: 600; font-size: 0.9rem;
            padding: 0.6rem 1.4rem;
            border-radius: 999px; border: none; cursor: pointer;
            transition: all 0.15s; text-decoration: none;
            display: inline-block;
        }
        .btn-action-primary { background: #ec008c; color: #fff; box-shadow: 0 4px 14px rgba(236,0,140,0.28); }
        .btn-action-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(236,0,140,0.38); }
        .btn-action-outline { background: transparent; color: #ec008c; border: 2px solid #ec008c; }
        .btn-action-outline:hover { background: #fce8f1; }
        .btn-action-danger { background: #fee2e2; color: #dc2626; border: 1.5px solid #fca5a5; }
        .btn-action-danger:hover { background: #fca5a5; color: #fff; }

        .order-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .order-table th { text-align: left; padding: 0.5rem 0.75rem; border-bottom: 2px solid #f5c6d8; font-family: 'Fredoka', sans-serif; color: #5c3a43; }
        .order-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #fce8f1; }
        .order-table tr:hover { background: #fff8fb; }
        .text-muted { color: #8a4a60; }
        .text-center { text-align: center; }
        .empty-state { padding: 2rem; text-align: center; color: #8a4a60; }

        @media (max-width: 600px) {
            .info-card { padding: 1.25rem; }
            .info-label { width: 100px; font-size: 0.75rem; }
            .info-row { flex-wrap: wrap; }
            .order-table { font-size: 0.75rem; }
            .order-table th, .order-table td { padding: 0.3rem 0.4rem; }
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
        </div>
        <div class="nav-user">
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<!-- BANNER -->
<div class="info-page">
    <div class="info-banner">
        <h1>My Account</h1>
        <p>Manage your CadienTea profile and preferences.</p>
    </div>

    <div class="info-content">

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Profile Overview Card -->
        <div class="info-card">
            <div class="info-card-header">
                <h2>👤 Profile Overview</h2>
            </div>

            <div class="user-header">
                <div class="avatar-circle">
                    <?= htmlspecialchars(mb_strtoupper(mb_substr($user['first_name'], 0, 1))) ?>
                </div>
                <div class="user-header-text">
                    <h3>
                        <?= htmlspecialchars($fullName) ?>
                        <span class="role-badge"><?= htmlspecialchars($user['role'] ?? 'customer') ?></span>
                    </h3>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                    <span class="member-badge">🌸 CadienTea Member</span>
                </div>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="info-card">
            <div class="info-card-header">
                <h2>📋 Account Details</h2>
            </div>

            <div class="info-row">
                <span class="info-label">First Name</span>
                <span class="info-value"><?= htmlspecialchars($user['first_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Last Name</span>
                <span class="info-value"><?= htmlspecialchars($user['last_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address</span>
                <span class="info-value"><?= htmlspecialchars($user['address'] ?? 'Not provided') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Password</span>
                <span class="info-value">••••••••••</span>
            </div>
            <div class="info-row">
                <span class="info-label">Member Since</span>
                <span class="info-value">
                    <?= isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'N/A' ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Account ID</span>
                <span class="info-value">#<?= str_pad((string)$user['id'], 5, '0', STR_PAD_LEFT) ?></span>
            </div>
        </div>

        <!-- Order History Card -->
        <div class="info-card">
            <div class="info-card-header">
                <h2>📦 Order History</h2>
            </div>

            <?php if (!empty($orders)): ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($order['order_number']) ?></strong></td>
                                <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="order-status status-<?= str_replace(' ', '_', $order['order_status']) ?>">
                                        <?= str_replace('_', ' ', htmlspecialchars($order['order_status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p style="font-size:1.5rem; margin-bottom:0.5rem;">🧋</p>
                    <p>You haven't placed any orders yet.</p>
                    <a href="index.php#menu" class="btn-action btn-action-primary" style="margin-top:1rem; display:inline-block;">Order Now</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions Card -->
        <div class="info-card">
            <div class="info-card-header">
                <h2>⚙️ Actions</h2>
            </div>
            <div class="action-row">
                <a href="index.php#menu" class="btn-action btn-action-primary">🧋 Order Boba</a>
                <a href="success.php" class="btn-action btn-action-outline">← Back to Dashboard</a>
                <a href="logout.php" class="btn-action btn-action-danger">Sign Out</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
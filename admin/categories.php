<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

global $conn;
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Count unread messages for sidebar badge
$unreadMessages = $conn->query("SELECT COUNT(*) as count FROM messages WHERE sender = 'customer' AND is_read = 0")->fetch_assoc()['count'];

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Categories</title>
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
        .admin-sidebar .logo h2 { font-family: 'Fredoka', sans-serif; font-size: 1.2rem; color: #fff; margin-top: 0.5rem; }
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
        .sidebar-footer a { color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.85rem; display: block; padding: 0.4rem 0; }
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
        .admin-header-bar h1 { font-family: 'Fredoka', sans-serif; font-size: 1.8rem; color: #1a0a10; }
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .btn-add { display: inline-block; background: #ec008c; color: #fff; padding: 0.5rem 1.5rem; border-radius: 999px; text-decoration: none; font-weight: 600; font-family: 'Fredoka', sans-serif; transition: all 0.15s; }
        .btn-add:hover { background: #c40075; transform: translateY(-1px); }
        
        .table-wrapper {
            overflow-x: auto;
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #f5c6d8;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .category-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 400px;
        }
        .category-table th {
            background: #fce8f1;
            padding: 0.75rem 1rem;
            text-align: left;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.8rem;
            color: #5c3a43;
            text-transform: uppercase;
        }
        .category-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #fce8f1;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        .category-table tr:hover { background: #fff8fb; }
        
        .btn-delete { padding: 0.25rem 0.6rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.7rem; font-weight: 600; background: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background: #fca5a5; color: #fff; }
        
        .form-inline { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
        .form-inline input { padding: 0.5rem 0.75rem; border: 1.5px solid #f5c6d8; border-radius: 0.75rem; font-family: 'Fredoka', sans-serif; font-size: 0.9rem; flex: 1; min-width: 150px; }
        .form-inline input:focus { border-color: #ec008c; outline: none; }
        
        .view-store-btn { display: inline-block; padding: 0.4rem 1rem; background: #10b981; color: #fff; border-radius: 999px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.15s; }
        .view-store-btn:hover { background: #059669; transform: translateY(-1px); }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; }
            .admin-main { margin-left: 0; }
            .admin-wrapper { flex-direction: column; }
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
    <aside class="admin-sidebar">
        <div class="logo">
            <img src="../images/cadienteamainlogo.png" alt="CadienTea" />
            <h2>Cadien<span>Tea</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><span class="icon">📊</span> Dashboard</a></li>
            <li><a href="orders.php"><span class="icon">📦</span> Orders</a></li>
            <li><a href="products.php"><span class="icon">🧋</span> Products</a></li>
            <li><a href="categories.php" class="active"><span class="icon">🏷️</span> Categories</a></li>
            <li><a href="users.php"><span class="icon">👤</span> Users</a></li>
            <li><a href="messages.php"><span class="icon">💬</span> Messages <?php if ($unreadMessages > 0): ?><span class="badge"><?= $unreadMessages ?></span><?php endif; ?></a></li>
        </ul>
        <hr class="sidebar-divider">
        <div class="sidebar-footer">
            <a href="../index.php">👁 View Store</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </aside>
    
    <main class="admin-main">
        <div class="admin-header-bar">
            <h1>🏷️ Categories</h1>
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Add Category Form -->
        <div style="background:#fff; border-radius:1rem; border:1px solid #f5c6d8; padding:1.5rem; margin-bottom:2rem;">
            <form action="add_category.php" method="POST" class="form-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="text" name="name" placeholder="New category name..." required>
                <button type="submit" class="btn-add" style="padding:0.5rem 1rem;">+ Add</button>
            </form>
        </div>
        
        <div class="table-wrapper">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; color:#8a4a60; padding:2rem;">No categories yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?= $cat['id'] ?></td>
                                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                <td>
                                    <form action="delete_category.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <button type="submit" class="btn-delete">🗑️ Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
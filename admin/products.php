<?php
require_once '../function.php';
requireLogin();
requireAdmin();

global $conn;

// Get all products with categories
$products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        /* Same sidebar styles as orders.php */
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
        
        .btn-add {
            display: inline-block;
            background: #ec008c;
            color: #fff;
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Fredoka', sans-serif;
            transition: all 0.15s;
        }
        .btn-add:hover { background: #c40075; transform: translateY(-1px); }
        
        .table-wrapper {
            overflow-x: auto;
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #f5c6d8;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        .product-table th {
            background: #fce8f1;
            padding: 0.75rem 1rem;
            text-align: left;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.8rem;
            color: #5c3a43;
            text-transform: uppercase;
        }
        .product-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #fce8f1;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        .product-table tr:hover { background: #fff8fb; }
        .product-table .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .product-table .status-badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-available { background: #d1fae5; color: #065f46; }
        .status-unavailable { background: #fee2e2; color: #991b1b; }
        
        .btn-edit {
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            background: #dbeafe;
            color: #1e40af;
            text-decoration: none;
        }
        .btn-edit:hover { background: #bfdbfe; }
        
        .btn-delete {
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 600;
            background: #fee2e2;
            color: #dc2626;
        }
        .btn-delete:hover { background: #fca5a5; color: #fff; }
        
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
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-family: 'Fredoka', sans-serif; color: #1a0a10; margin-bottom: 0.5rem; }
        .empty-state p { color: #8a4a60; }
        
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
            <li><a href="products.php" class="active"><span class="icon">🧋</span> Products</a></li>
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
            <h1>🧋 Products</h1>
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <a href="add_product.php" class="btn-add">+ Add New Product</a>
                <a href="../index.php" class="view-store-btn">👁 View Store</a>
            </div>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="icon">🧋</div>
                <h3>No Products Yet</h3>
                <p>Add your first product to start selling!</p>
                <a href="add_product.php" class="btn-add" style="margin-top:1rem; display:inline-block;">Add Product</a>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="../<?= htmlspecialchars($product['image'] ?? 'images/default-boba.jpg') ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="product-img"
                                         onerror="this.src='https://via.placeholder.com/60/ec008c/ffffff?text=🍧'">
                                </td>
                                <td><strong><?= htmlspecialchars($product['name']) ?></strong></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                                <td>
                                    <span class="status-badge status-<?= $product['status'] ?? 'available' ?>">
                                        <?= htmlspecialchars($product['status'] ?? 'available') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn-edit">✏️ Edit</a>
                                    <form action="delete_product.php" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn-delete">🗑️ Delete</button>
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
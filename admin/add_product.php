<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

global $conn;

// Get categories for dropdown
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
$errors = $_SESSION['product_errors'] ?? [];
$old = $_SESSION['product_old'] ?? [];
unset($_SESSION['product_errors'], $_SESSION['product_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product – Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
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
        
        .form-card {
            background: #fff;
            border-radius: 1.25rem;
            border: 1px solid #f5c6d8;
            padding: 2rem;
            max-width: 700px;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1a0a10;
            margin-bottom: 0.3rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
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
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #ec008c;
            box-shadow: 0 0 0 3px rgba(236,0,140,0.08);
        }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group .hint { font-size: 0.75rem; color: #8a4a60; margin-top: 0.2rem; }
        .field-error { font-size: 0.8rem; color: #dc2626; margin-top: 0.2rem; }
        
        /* ── SIZE INPUTS ── */
        .sizes-section {
            background: #fce8f1;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .sizes-section h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1rem;
            color: #1a0a10;
            margin-bottom: 1rem;
        }
        .size-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            align-items: center;
        }
        .size-row input {
            padding: 0.6rem 0.9rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.5rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.9rem;
            background: #fff;
            outline: none;
        }
        .size-row input:focus { border-color: #ec008c; }
        .btn-remove-size {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 0.5rem;
            padding: 0.6rem 0.9rem;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.15s;
        }
        .btn-remove-size:hover { background: #fca5a5; color: #fff; }
        .btn-add-size {
            background: #ec008c;
            color: #fff;
            border: none;
            border-radius: 0.75rem;
            padding: 0.6rem 1.25rem;
            cursor: pointer;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.15s;
            margin-top: 0.5rem;
        }
        .btn-add-size:hover { background: #c40075; }
        
        .btn-submit {
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.7rem 2rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-submit:hover { background: #c40075; transform: translateY(-1px); }
        .btn-cancel {
            background: transparent;
            color: #5c3a43;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.7rem 2rem;
            border: 2px solid #f5c6d8;
            border-radius: 999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s;
        }
        .btn-cancel:hover { background: #fce8f1; }
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .view-store-btn { display: inline-block; padding: 0.4rem 1rem; background: #10b981; color: #fff; border-radius: 999px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.15s; }
        .view-store-btn:hover { background: #059669; transform: translateY(-1px); }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; }
            .admin-main { margin-left: 0; }
            .admin-wrapper { flex-direction: column; }
            .form-card { padding: 1.25rem; }
            .size-row { grid-template-columns: 1fr 1fr auto; }
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
            <h1>➕ Add Product</h1>
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-card">
            <form action="save_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                
                <!-- Product Name -->
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="e.g., Brown Sugar Tiger" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Describe your product..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>
                
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tag -->
                <div class="form-group">
                    <label for="tag">Tag (e.g., Best Seller, New)</label>
                    <input type="text" id="tag" name="tag" value="<?= htmlspecialchars($old['tag'] ?? '') ?>" placeholder="Best Seller">
                    <div class="hint">Optional: Add a tag to highlight this product</div>
                </div>
                
                <!-- Status -->
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="available" <?= ($old['status'] ?? 'available') == 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="unavailable" <?= ($old['status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                    </select>
                </div>
                
                <!-- Image -->
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div class="hint">Recommended: 300x200px. Leave empty to use default.</div>
                    <?php if (isset($errors['image'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['image']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- ── PRODUCT SIZES ── -->
                <div class="sizes-section">
                    <h3>📏 Product Sizes & Prices</h3>
                    <p style="font-size: 0.8rem; color: #8a4a60; margin-bottom: 1rem;">Add sizes and prices for this product (e.g., Regular, Large)</p>
                    
                    <div id="sizesContainer">
                        <!-- Default sizes -->
                        <div class="size-row">
                            <input type="text" name="sizes[]" placeholder="Size (e.g., Regular)" value="Regular" required>
                            <input type="number" name="prices[]" placeholder="Price (₱)" step="0.01" min="0" value="89.00" required>
                            <button type="button" class="btn-remove-size" onclick="removeSize(this)">✕</button>
                        </div>
                        <div class="size-row">
                            <input type="text" name="sizes[]" placeholder="Size (e.g., Large)" value="Large" required>
                            <input type="number" name="prices[]" placeholder="Price (₱)" step="0.01" min="0" value="109.00" required>
                            <button type="button" class="btn-remove-size" onclick="removeSize(this)">✕</button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-add-size" onclick="addSize()">+ Add Another Size</button>
                </div>
                
                <div style="display:flex; gap:1rem; margin-top:1.5rem; flex-wrap:wrap;">
                    <button type="submit" class="btn-submit">Save Product</button>
                    <a href="products.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    function addSize() {
        const container = document.getElementById('sizesContainer');
        const row = document.createElement('div');
        row.className = 'size-row';
        row.innerHTML = `
            <input type="text" name="sizes[]" placeholder="Size (e.g., Medium)" required>
            <input type="number" name="prices[]" placeholder="Price (₱)" step="0.01" min="0" required>
            <button type="button" class="btn-remove-size" onclick="removeSize(this)">✕</button>
        `;
        container.appendChild(row);
    }
    
    function removeSize(button) {
        const container = document.getElementById('sizesContainer');
        if (container.children.length > 1) {
            button.closest('.size-row').remove();
        } else {
            alert('At least one size is required.');
        }
    }
</script>

</body>
</html>
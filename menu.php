<?php
require_once "db.php";
require_once "function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$products = getProducts();
$categories = getCategories();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Menu – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>


<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">
            <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        </a>
        <div class="nav-links">
            <a href="menu.php" class="active">Menu</a>
            <a href="about.php">About</a>
            <a href="community.php">Community</a>
            <a href="contact.php">Contact</a>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <!-- Logged In - Show Cart, Account, Logout -->
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="cart.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; position:relative;">
                    🛒 Cart
                    <?php if ($cartCount > 0): ?>
                        <span style="background:#dc2626; color:#fff; border-radius:50%; padding:0.1rem 0.4rem; font-size:0.7rem; margin-left:0.2rem; font-weight:700;"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
                <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
            </div>
        <?php else: ?>
            <!-- Not Logged In - Show Login and Sign Up -->
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="login.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1.25rem;">Login</a>
                <a href="register.php" class="btn-outline" style="padding:0.4rem 1.25rem; font-size:0.85rem;">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- MENU PAGE -->
<section id="menu" style="padding-top: 100px; min-height: 100vh;">
    <div class="section-inner">
        <div class="section-head">
            <div class="badge">🧋 Our Menu</div>
            <h2>Signature Bubble Teas</h2>
            <p>Every sip tells a story. Fresh milk teas, fruity blends, popping pearls — made to order, made for you.</p>
            <div class="menu-cats">
                <button class="cat-btn active" data-category="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button class="cat-btn" data-category="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="menu-grid">
            <?php foreach ($products as $product): ?>
                <div class="menu-card" data-category="<?= htmlspecialchars($product['category_id']) ?>">
                    <div class="menu-card-img">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy" onerror="this.src='https://via.placeholder.com/300x200/ec008c/ffffff?text=🧋'" />
                        <?php if ($product['tag']): ?>
                            <span class="menu-tag"><?= htmlspecialchars($product['tag']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="menu-card-body">
                        <div class="menu-card-top">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                        </div>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        
                        <?php 
                        $sizes = getProductSizes($product['id']);
                        if (!empty($sizes)): 
                        ?>
                            <select class="size-select" data-product-id="<?= $product['id'] ?>" style="width:100%; padding:0.5rem; border-radius:0.5rem; border:1px solid #f5c6d8; font-family:var(--font-body); margin-bottom:0.75rem;">
                                <?php foreach ($sizes as $size): ?>
                                    <option value="<?= $size['id'] ?>" data-price="<?= $size['price'] ?>">
                                        <?= htmlspecialchars($size['size']) ?> - ₱<?= number_format($size['price'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                        
                        <button class="btn-add" data-product-id="<?= $product['id'] ?>" data-product-name="<?= htmlspecialchars($product['name']) ?>">
                            Add to Order
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script src="javascript.js"></script>
</body>
</html>
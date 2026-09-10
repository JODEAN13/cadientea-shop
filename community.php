<?php
require_once "db.php";
require_once "function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$testimonials = [
    ['name' => 'Yah T.', 'quote' => 'CadienTea is my daily ritual. The Brown Sugar Tiger is unreal — perfectly chewy pearls and that caramel swirl just hits different every time.'],
    ['name' => 'Puh K.', 'quote' => 'Best bubble tea I have had outside of Taiwan. The pearls are always fresh and the staff genuinely care. This place is special.'],
    ['name' => 'Kissie E.', 'quote' => 'I came for the Pink Lychee Boba and I cannot stop coming back. The vibes, the flavours, the whole experience — it is my happy place.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Community – CadienTea</title>
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
            <a href="menu.php">Menu</a>
            <a href="about.php">About</a>
            <a href="community.php" class="active">Community</a>
            <a href="contact.php">Contact</a>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="cart.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; position:relative;">
                    🛒 Cart
                    <?php if (getCartCount() > 0): ?>
                        <span style="background:#dc2626; color:#fff; border-radius:50%; padding:0.1rem 0.4rem; font-size:0.7rem; margin-left:0.2rem; font-weight:700;"><?= getCartCount() ?></span>
                    <?php endif; ?>
                </a>
                <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
                <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
            </div>
        <?php else: ?>
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="login.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1.25rem;">Login</a>
                <a href="register.php" class="btn-outline" style="padding:0.4rem 1.25rem; font-size:0.85rem;">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- COMMUNITY PAGE -->
<section id="community" style="padding-top: 100px; min-height: 100vh;">
    <div class="section-inner">
        <div class="section-head">
            <div class="badge">💬 Community Love</div>
            <h2>What our community says</h2>
        </div>
        <div class="testi-grid">
            <?php foreach ($testimonials as $t): ?>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <blockquote>"<?= htmlspecialchars($t['quote']) ?>"</blockquote>
                    <div class="testi-author">
                        <div class="testi-avatar"><?= htmlspecialchars($t['name'][0]) ?></div>
                        <span class="name"><?= htmlspecialchars($t['name']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
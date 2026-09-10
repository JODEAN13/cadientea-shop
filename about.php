<?php
require_once "db.php";
require_once "function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$about_img = 'images/bobareviews.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us – CadienTea</title>
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
            <a href="about.php" class="active">About</a>
            <a href="community.php">Community</a>
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

<!-- ABOUT PAGE -->
<section id="about" style="padding-top: 100px; min-height: 100vh;">
    <div class="section-inner">
        <div class="section-head">
            <div class="badge">🌸 Our Story</div>
            <h2>Where every pearl carries the <span style="color:var(--pink);">glow</span></h2>
        </div>
        <div class="about-grid">
            <div class="about-img-wrap">
                <div class="photo">
                    <img src="<?= htmlspecialchars($about_img) ?>" alt="Hands holding a boba cup" />
                </div>
                <div class="rating-badge">
                    <div class="num">4.9★</div>
                    <div class="lbl">Community Rating</div>
                </div>
            </div>
            <div class="about-text">
                <p>We are a community-focused bubble tea shop that blends authentic Asian tea traditions with bold, modern flavors. Every cup is handcrafted — from the perfectly brewed base to the carefully measured pearls.</p>
                <p>We aim to create unique, welcoming spaces where everyone belongs — whether you are here for your daily boba ritual or your very first sip of something magical.</p>
                <div class="pillars">
                    <div class="pillar">
                        <div class="icon">🧋</div>
                        <h4>Real Pearls</h4>
                        <p>Fresh tapioca cooked in-house every day</p>
                    </div>
                    <div class="pillar">
                        <div class="icon">♻️</div>
                        <h4>Eco Cups</h4>
                        <p>Compostable cups and sustainable straws</p>
                    </div>
                    <div class="pillar">
                        <div class="icon">🤝</div>
                        <h4>Community First</h4>
                        <p>Events, boba nights &amp; loyalty rewards</p>
                    </div>
                    <div class="pillar">
                        <div class="icon">💖</div>
                        <h4>Made to Order</h4>
                        <p>Every cup shaken fresh, just for you</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
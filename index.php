<?php
require_once "db.php";
require_once "function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$hero_img = 'images/classicbobamain.png';
$about_img = 'images/bobareviews.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CadienTea – Sip the glow, love the flow!</title>
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
            <a href="community.php">Community</a>
            <a href="contact.php">Contact</a>
        </div>
        <?php if (isLoggedIn()): ?>
            <div style="display:flex; align-items:center; gap:1rem;">
                <span style="font-weight:600; color:#5c3a43;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
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

<!-- HERO -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-text">
        <div class="badge">🧋 Bubble Tea Shop</div>
        <h1>Sip the glow,<br /><span>love the flow.</span></h1>
        <p>Hand-crafted bubble teas bursting with flavor — chewy pearls, popping boba, and dreamy milk teas made fresh for you every time.</p>
        <div class="hero-btns">
            <a href="menu.php" class="btn-hero">Order Now 🧋</a>
            <a href="about.php" class="btn-outline">Our Story</a>
        </div>
        <div class="hero-stats">
            <div><div class="num">500+</div><div class="lbl">Happy Customers</div></div>
            <div><div class="num">20+</div><div class="lbl">Boba Flavours</div></div>
            <div><div class="num">3</div><div class="lbl">Locations</div></div>
        </div>
    </div>
    <div class="hero-image">
        <div class="hero-photo-wrap">
            <img src="<?= htmlspecialchars($hero_img) ?>" alt="Four colorful boba teas with straws" />
        </div>
        <div class="hero-badge">
            <span class="icon">🍃</span>
            <div>
                <div class="title">Fresh Daily</div>
                <div class="sub">Shaken with love</div>
            </div>
        </div>
    </div>
</section>

<!-- QUICK PREVIEW SECTION -->
<section style="padding: 4rem 1.5rem; background: var(--pink-pale);">
    <div class="section-inner" style="text-align: center;">
        <div class="badge">🧋 Ready to Order?</div>
        <h2 style="font-family: 'Fredoka', sans-serif; font-size: clamp(1.8rem, 4vw, 2.5rem); margin-bottom: 1rem;">Your perfect cup is waiting</h2>
        <p style="color: var(--muted); max-width: 500px; margin: 0 auto 2rem;">Browse our full menu of signature bubble teas, customize your order, and enjoy handcrafted perfection.</p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="menu.php" class="btn-hero" style="padding: 1rem 2.5rem;">🧋 Order Now</a>
            <a href="community.php" class="btn-outline" style="padding: 1rem 2.5rem;">See Reviews</a>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<div class="cta-banner">
    <h2>Ready to find your glow?</h2>
    <p>Visit us in store or order online — your perfect cup is waiting.</p>
    <div class="cta-btns">
        <a href="menu.php" class="btn-white">Order Online</a>
        <a href="contact.php" class="btn-ghost">Find a Location</a>
    </div>
</div>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script src="javascript.js"></script>
</body>
</html>
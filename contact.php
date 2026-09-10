<?php
require_once "db.php";
require_once "function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us – CadienTea</title>
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
            <a href="contact.php" class="active">Contact</a>
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

<!-- CONTACT PAGE -->
<section id="contact" style="padding-top: 100px; min-height: 100vh;">
    <div class="section-inner">
        <div class="section-head">
            <div class="badge">📍 Contact Us</div>
            <h2>Find a Location</h2>
            <p>Visit us in store or reach out — we'd love to hear from you!</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 900px; margin: 0 auto;">
            <!-- Contact Info -->
            <div style="background: #fff; border-radius: 1.25rem; padding: 2rem; border: 1px solid #f5c6d8;">
                <h3 style="font-family: 'Fredoka', sans-serif; margin-bottom: 1.5rem; color: #1a0a10;">📍 Our Locations</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-family: 'Fredoka', sans-serif; color: #ec008c; margin-bottom: 0.3rem;">Main Branch</h4>
                    <p style="color: #5c3a43; font-size: 0.9rem;">Dumaguete City, Negros Oriental</p>
                    <p style="color: #8a4a60; font-size: 0.85rem;">Mon–Fri: 7:00 AM – 8:00 PM</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-family: 'Fredoka', sans-serif; color: #ec008c; margin-bottom: 0.3rem;">📞 Phone</h4>
                    <p style="color: #5c3a43; font-size: 0.9rem;">+639930830701</p>
                </div>
                
                <div>
                    <h4 style="font-family: 'Fredoka', sans-serif; color: #ec008c; margin-bottom: 0.3rem;">✉️ Email</h4>
                    <p style="color: #5c3a43; font-size: 0.9rem;">hello@cadientea.com</p>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div style="background: #fff; border-radius: 1.25rem; padding: 2rem; border: 1px solid #f5c6d8;">
                <h3 style="font-family: 'Fredoka', sans-serif; margin-bottom: 1.5rem; color: #1a0a10;">💬 Send us a Message</h3>
                <form action="#" method="POST">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-family: 'Fredoka', sans-serif; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Your Name</label>
                        <input type="text" placeholder="Juan Dela Cruz" style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #f5c6d8; border-radius: 0.75rem; font-family: 'Fredoka', sans-serif;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-family: 'Fredoka', sans-serif; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Email</label>
                        <input type="email" placeholder="you@example.com" style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #f5c6d8; border-radius: 0.75rem; font-family: 'Fredoka', sans-serif;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-family: 'Fredoka', sans-serif; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem;">Message</label>
                        <textarea placeholder="Your message..." style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #f5c6d8; border-radius: 0.75rem; font-family: 'Fredoka', sans-serif; min-height: 100px;"></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; font-size: 1rem;">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
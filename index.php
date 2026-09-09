<?php
require_once "db.php";
require_once "function.php";

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get products from database
$products = getProducts();
$categories = getCategories();

// Testimonials
$testimonials = [
    ['name' => 'Yah T.', 'quote' => 'CadienTea is my daily ritual. The Brown Sugar Tiger is unreal — perfectly chewy pearls and that caramel swirl just hits different every time.'],
    ['name' => 'Puh K.', 'quote' => 'Best bubble tea I have had outside of Taiwan. The pearls are always fresh and the staff genuinely care. This place is special.'],
    ['name' => 'Kissie E.', 'quote' => 'I came for the Pink Lychee Boba and I cannot stop coming back. The vibes, the flavours, the whole experience — it is my happy place.'],
];

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

<!-- ── ADMIN BAR (Only visible to admins) ── -->
<?php if (isAdmin()): ?>
<div style="background: #1a0a10; color: #fff; padding: 0.5rem 1.5rem; display: flex; justify-content: space-between; align-items: center; font-family: 'Fredoka', sans-serif; font-size: 0.85rem; position: sticky; top: 0; z-index: 999; border-bottom: 2px solid #ec008c;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="background: #ec008c; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.7rem; font-weight: 700; color: #fff;">ADMIN</span>
        <span style="color: #fff;">👑 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <span style="color: rgba(255,255,255,0.4); font-size: 0.7rem;">|</span>
        <a href="admin/index.php" style="color: #ec008c; text-decoration: none; font-weight: 600; font-size: 0.8rem;">⚙️ Dashboard</a>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="admin/orders.php" style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.75rem;">📦 Orders</a>
        <a href="admin/products.php" style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.75rem;">🧋 Products</a>
        <a href="logout.php" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.75rem;">🚪 Logout</a>
    </div>
</div>
<?php endif; ?>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="#" class="nav-logo">
            <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        </a>
        
        <div class="nav-links">
            <a href="index.php#menu">Menu</a>
            <a href="index.php#about">About</a>
            <a href="index.php#community">Community</a>
            <a href="cart.php">🛒 Cart (<?= getCartCount() ?>)</a>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <!-- Logged In - Show user info and logout -->
            <div style="display:flex; align-items:center; gap:1rem;">
                <span style="font-weight:600; color:#5c3a43;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
                <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
                <a href="logout.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem; background:#dc2626; box-shadow:none;">Logout</a>
            </div>
        <?php else: ?>
            <!-- Not Logged In - Show Login and Sign Up buttons -->
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <a href="login.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1.25rem;">Login</a>
                <a href="register.php" class="btn-outline" style="padding:0.4rem 1.25rem; font-size:0.85rem;">Sign Up</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- ── FLOATING ADMIN BUTTON (Bottom Right) ── -->
<?php if (isAdmin()): ?>
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 999;">
    <a href="admin/index.php" style="background: #ec008c; color: #fff; padding: 0.8rem 1.5rem; border-radius: 999px; text-decoration: none; font-weight: 700; box-shadow: 0 4px 20px rgba(236,0,140,0.4); display: flex; align-items: center; gap: 0.5rem;">
        ⚙️ Admin Panel
    </a>
</div>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
    <!-- ... rest of your content ... -->
</section>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>

    <div class="hero-text">
        <div class="badge">🧋 Bubble Tea Shop</div>
        <h1>Sip the glow,<br /><span>love the flow.</span></h1>
        <p>Hand-crafted bubble teas bursting with flavor — chewy pearls, popping boba, and dreamy milk teas made fresh for you every time.</p>
        <div class="hero-btns">
            <a href="#menu" class="btn-hero">Explore Our Menu</a>
            <a href="#about" class="btn-outline">Our Story</a>
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

<!-- MENU -->
<section id="menu">
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
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy" />
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
                        
                        <!-- In the menu card -->
                     <button class="btn-add" data-product-id="<?= $product['id'] ?>" data-product-name="<?= htmlspecialchars($product['name']) ?>">
                        Add to Order
                    </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="section-inner">
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
                <div class="badge">🌸 Our Story</div>
                <h2>Where every pearl<br />carries the <span>glow</span></h2>
                <p>We are a community-focused bubble tea shop that blends authentic Asian tea traditions with bold, modern flavors. Every cup is handcrafted — from the perfectly brewed base to the carefully measured pearls.</p>
                <p>We aim to create unique, welcoming spaces where everyone belongs — whether you are here for your daily boba ritual or your very first sip of something magical.</p>
                <div class="pillars">
                    <?php foreach ([
                        ['🧋', 'Real Pearls', 'Fresh tapioca cooked in-house every day'],
                        ['♻️', 'Eco Cups', 'Compostable cups and sustainable straws'],
                        ['🤝', 'Community First', 'Events, boba nights & loyalty rewards'],
                        ['💖', 'Made to Order', 'Every cup shaken fresh, just for you'],
                    ] as [$icon, $title, $desc]): ?>
                        <div class="pillar">
                            <div class="icon"><?= $icon ?></div>
                            <h4><?= htmlspecialchars($title) ?></h4>
                            <p><?= htmlspecialchars($desc) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section id="community">
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

<!-- CTA BANNER -->
<div class="cta-banner">
    <h2>Ready to find your glow?</h2>
    <p>Visit us in store or order online — your perfect cup is waiting.</p>
    <div class="cta-btns">
        <a href="#menu" class="btn-white">Order Online</a>
        <a href="#contact" class="btn-ghost">Find a Location</a>
    </div>
</div>

<!-- FOOTER -->
<footer id="contact">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="images/cadienteamainlogo.png" alt="CadienTea logo" class="footer-logo" />
                <p>Sip the glow, love the flow. Handcrafted bubble teas brewed with love since 2022.</p>
            </div>
            <div class="footer-col">
                <h4>Explore</h4>
                <?php foreach (['Our Menu', 'About Us', 'Locations', 'Events', 'Blog'] as $link): ?>
                    <a href="#"><?= htmlspecialchars($link) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <?php foreach ([
                    ['📍', 'Dumaguete City, Negros Oriental'],
                    ['📞', '+639930830701'],
                    ['✉️', 'hello@cadientea.com'],
                ] as [$icon, $text]): ?>
                    <div class="footer-contact-item"><span><?= $icon ?></span><span><?= htmlspecialchars($text) ?></span></div>
                <?php endforeach; ?>
            </div>
            <div class="footer-col">
                <h4>Hours</h4>
                <?php foreach ([
                    ['Mon – Fri', '7:00 AM – 8:00 PM'],
                    ['Saturday', '8:00 AM – 9:00 PM'],
                    ['Sunday', '9:00 AM – 6:00 PM'],
                ] as [$day, $hours]): ?>
                    <div class="footer-hours-row">
                        <span class="day"><?= htmlspecialchars($day) ?></span>
                        <span class="time"><?= htmlspecialchars($hours) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> CadienTea. All rights reserved.</p>
            <div class="footer-socials">
                <a href="#">Instagram</a>
                <a href="#">TikTok</a>
                <a href="#">Facebook</a>
            </div>
        </div>
    </div>
</footer>

<script src="javascript.js"></script>
</body>
</html>
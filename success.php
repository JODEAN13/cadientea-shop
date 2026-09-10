<?php
require_once 'function.php';
requireLogin();

$flash    = getFlash();
$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Guest');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        /* ── NAV (logged-in variant) ── */
        .nav-user {
            display: flex; align-items: center; gap: 1rem;
        }
        .nav-user .greeting {
            font-family: 'Fredoka', sans-serif;
            font-size: 0.9rem; font-weight: 600;
            color: #5c3a43;
        }
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

        /* ── SUCCESS HERO ── */
        .success-hero {
            min-height: 55vh;
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 50%, #ec008c 100%);
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            text-align: center;
            padding: 6rem 1.5rem 4rem;
        }
        .success-hero .check-circle {
            width: 90px; height: 90px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            margin: 0 auto 1.75rem;
        }
        .success-hero h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        .success-hero p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.9);
            max-width: 460px;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }
        .success-btns {
            display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;
        }
        .btn-white-solid {
            display: inline-block;
            background: #fff; color: #ec008c;
            font-family: 'Fredoka', sans-serif; font-weight: 700; font-size: 1rem;
            padding: 0.8rem 2rem; border-radius: 999px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
            transition: transform 0.15s;
            text-decoration: none;
        }
        .btn-white-solid:hover { transform: translateY(-2px); }
        .btn-clear {
            display: inline-block;
            background: transparent; color: #fff;
            font-family: 'Fredoka', sans-serif; font-weight: 700; font-size: 1rem;
            padding: 0.8rem 2rem; border-radius: 999px;
            border: 2px solid rgba(255,255,255,0.7);
            transition: background 0.15s;
            text-decoration: none;
        }
        .btn-clear:hover { background: rgba(255,255,255,0.15); }

        /* ── QUICK LINKS SECTION ── */
        .quick-links {
            padding: 5rem 1.5rem;
            background: #fff8fb;
        }
        .quick-links-inner {
            max-width: 900px; margin: 0 auto; text-align: center;
        }
        .quick-links h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.8rem; font-weight: 700;
            color: #1a0a10; margin-bottom: 2.5rem;
        }
        .cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
        }
        .quick-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem 1.25rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: #1a0a10;
            display: block;
        }
        .quick-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(236,0,140,0.12);
        }
        .quick-card .card-icon { font-size: 2.2rem; margin-bottom: 0.75rem; }
        .quick-card h3 {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700; font-size: 1.05rem;
            color: #1a0a10; margin-bottom: 0.4rem;
        }
        .quick-card p { font-size: 0.82rem; color: #8a4a60; line-height: 1.5; }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">
            <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        </a>
        <div class="nav-user">
            <span class="greeting">Hi, <?= $userName ?>! 👋</span>
            <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<!-- SUCCESS HERO -->
<section class="success-hero">
    <?php if ($flash && $flash['type'] === 'success'): ?>
        <div class="check-circle">✅</div>
        <h1><?= htmlspecialchars($flash['message']) ?></h1>
    <?php else: ?>
        <div class="check-circle">🧋</div>
        <h1>You're logged in!</h1>
    <?php endif; ?>
    <p>Your account is ready. Explore our menu, track your orders, or manage your profile below.</p>
    <div class="success-btns">
        <a href="menu.php" class="btn-white-solid">Browse Our Menu</a>
        <a href="info.php" class="btn-clear">My Account Info</a>
    </div>
</section>

<!-- QUICK LINKS -->
<section class="quick-links">
    <div class="quick-links-inner">
        <h2>What would you like to do?</h2>
        <div class="cards-row">
            <!-- Changed from index.php#menu to menu.php -->
            <a href="menu.php" class="quick-card">
                <div class="card-icon">🧋</div>
                <h3>Order Boba</h3>
                <p>Browse our full menu of signature bubble teas.</p>
            </a>
            <a href="info.php" class="quick-card">
                <div class="card-icon">👤</div>
                <h3>My Profile</h3>
                <p>View and manage your account information.</p>
            </a>
            <!-- Changed from index.php#community to community.php -->
            <a href="community.php" class="quick-card">
                <div class="card-icon">💬</div>
                <h3>Community</h3>
                <p>See what our regulars are saying about us.</p>
            </a>
        </div>
    </div>
</section>

</body>
</html>
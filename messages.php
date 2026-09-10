<?php
require_once 'function.php';
requireLogin();

$user = getUserById((int) $_SESSION['user_id']);
$flash = getFlash();

if (!$user) {
    logoutUser();
    redirect('login.php');
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($subject) || empty($message)) {
        setFlash('error', 'Please fill in both subject and message.');
    } else {
        // For now, just show a success message
        // In a real app, you'd save this to a database table
        setFlash('success', '✅ Your message has been sent! We\'ll get back to you soon. 🧋');
    }
    redirect('messages.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Messages – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .nav-user { display: flex; align-items: center; gap: 1rem; }
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

        .messages-page { min-height: 100vh; padding-top: 68px; background: #fff8fb; }
        .messages-banner {
            background: linear-gradient(135deg, #fce8f1 0%, #f8b5c2 100%);
            padding: 3rem 1.5rem 2rem;
            text-align: center;
        }
        .messages-banner h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700; color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .messages-banner p { font-size: 0.95rem; color: #5c3a43; }

        .messages-content { max-width: 700px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }

        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .message-card {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .message-card h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.15rem;
            color: #1a0a10;
            margin-bottom: 0.5rem;
        }
        .message-card .sub {
            font-size: 0.9rem;
            color: #8a4a60;
            margin-bottom: 1.5rem;
        }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
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
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-send {
            width: 100%;
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.85rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: all 0.15s;
        }
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(236,0,140,0.4);
        }

        /* Contact Info */
        .contact-info {
            background: #fce8f1;
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .contact-info h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1rem;
            color: #1a0a10;
            margin-bottom: 1rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            color: #5c3a43;
        }
        .contact-item .icon {
            font-size: 1.2rem;
        }
    </style>
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
        </div>
        <div class="nav-user">
            <a href="cart.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">🛒 Cart (<?= getCartCount() ?>)</a>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<!-- MESSAGES PAGE -->
<div class="messages-page">
    <div class="messages-banner">
        <h1>💬 Messages</h1>
        <p>Send us a message — we'd love to hear from you!</p>
    </div>

    <div class="messages-content">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Contact Info -->
        <div class="contact-info">
            <h3>📍 Other ways to reach us</h3>
            <div class="contact-item">
                <span class="icon">📞</span>
                <span>+639930830701</span>
            </div>
            <div class="contact-item">
                <span class="icon">✉️</span>
                <span>hello@cadientea.com</span>
            </div>
            <div class="contact-item">
                <span class="icon">📍</span>
                <span>Dumaguete City, Negros Oriental</span>
            </div>
        </div>

        <!-- Message Form -->
        <div class="message-card">
            <h2>Send a Message</h2>
            <p class="sub">Fill out the form below and we'll get back to you as soon as possible.</p>
            
            <form action="messages.php" method="POST">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a topic...</option>
                        <option value="Order Inquiry">Order Inquiry</option>
                        <option value="Product Question">Product Question</option>
                        <option value="Delivery Concern">Delivery Concern</option>
                        <option value="Feedback">Feedback</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
                </div>
                
                <button type="submit" class="btn-send">📨 Send Message</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
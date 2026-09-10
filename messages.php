<?php
require_once 'function.php';
require_once 'validation.php';
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
        global $conn;
        $stmt = $conn->prepare("INSERT INTO messages (user_id, subject, message, sender) VALUES (?, ?, ?, 'customer')");
        $stmt->bind_param("iss", $user['id'], $subject, $message);
        
        if ($stmt->execute()) {
            setFlash('success', '✅ Your message has been sent! We\'ll get back to you soon. 🧋');
        } else {
            setFlash('error', 'Failed to send message. Please try again.');
        }
    }
    redirect('messages.php');
}

// Get user's messages
global $conn;
$stmt = $conn->prepare("
    SELECT * FROM messages 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
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

        .messages-content { max-width: 800px; margin: 0 auto; padding: 2rem 1.5rem 5rem; }

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
            margin-bottom: 1.5rem;
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
            min-height: 120px;
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

        /* Message History */
        .message-thread {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .message-thread-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #fce8f1;
        }
        .message-thread-header .subject {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            color: #1a0a10;
        }
        .message-thread-header .date {
            font-size: 0.75rem;
            color: #8a4a60;
        }
        .message-thread-body {
            font-size: 0.9rem;
            color: #5c3a43;
            line-height: 1.6;
        }
        .message-thread-body .sender {
            font-size: 0.75rem;
            color: #ec008c;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }
        .message-reply {
            background: #fce8f1;
            border-left: 3px solid #ec008c;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-top: 0.75rem;
        }
        .message-reply .sender {
            color: #ec008c;
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
            <a href="info.php" class="btn-primary" style="font-size:0.85rem; padding:0.4rem 1rem;">My Account</a>
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

        <!-- Send Message Form -->
        <div class="message-card">
            <h2>📨 Send a Message</h2>
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

        <!-- Message History -->
        <?php if (!empty($messages)): ?>
            <div class="message-card">
                <h2>📜 Your Message History</h2>
                <p class="sub">All your previous conversations with us.</p>
                
                <?php foreach ($messages as $msg): ?>
                    <?php if ($msg['parent_id'] === null): /* Only show original messages */ ?>
                        <div class="message-thread">
                            <div class="message-thread-header">
                                <div class="subject"><?= htmlspecialchars($msg['subject']) ?></div>
                                <div class="date"><?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?></div>
                            </div>
                            <div class="message-thread-body">
                                <div class="sender">You (Customer)</div>
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                            
                            <?php
                            // Get admin replies
                            $replyStmt = $conn->prepare("SELECT * FROM messages WHERE parent_id = ? AND sender = 'admin' ORDER BY created_at ASC");
                            $replyStmt->bind_param("i", $msg['id']);
                            $replyStmt->execute();
                            $replies = $replyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>
                            
                            <?php foreach ($replies as $reply): ?>
                                <div class="message-reply">
                                    <div class="sender">CadienTea Support</div>
                                    <?= nl2br(htmlspecialchars($reply['message'])) ?>
                                    <div class="date" style="margin-top:0.5rem;"><?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
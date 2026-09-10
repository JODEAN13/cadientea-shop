<?php
require_once '../function.php';
require_once '../validation.php';
requireLogin();
requireAdmin();

global $conn;

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $parentId = (int)($_POST['parent_id'] ?? 0);
    $replyText = sanitize($_POST['reply_message'] ?? '');
    $userId = (int)($_POST['user_id'] ?? 0);
    
    if ($parentId > 0 && !empty($replyText) && $userId > 0) {
        // Get original message subject
        $stmt = $conn->prepare("SELECT subject FROM messages WHERE id = ?");
        $stmt->bind_param("i", $parentId);
        $stmt->execute();
        $original = $stmt->get_result()->fetch_assoc();
        
        if ($original) {
            $stmt = $conn->prepare("INSERT INTO messages (user_id, subject, message, sender, parent_id) VALUES (?, ?, ?, 'admin', ?)");
            $stmt->bind_param("issi", $userId, $original['subject'], $replyText, $parentId);
            
            if ($stmt->execute()) {
                setFlash('success', '✅ Reply sent successfully!');
            } else {
                setFlash('error', 'Failed to send reply.');
            }
        }
    }
    redirect('messages.php');
}

// Mark as read when viewing
if (isset($_GET['read']) && (int)$_GET['read'] > 0) {
    $msgId = (int)$_GET['read'];
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $msgId);
    $stmt->execute();
    redirect('messages.php');
}

// Get all customer messages (not replies)
$messages = $conn->query("
    SELECT m.*, u.first_name, u.last_name, u.email 
    FROM messages m 
    LEFT JOIN users u ON m.user_id = u.id 
    WHERE m.parent_id IS NULL AND m.sender = 'customer'
    ORDER BY m.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Count unread
$unreadCount = $conn->query("SELECT COUNT(*) as count FROM messages WHERE sender = 'customer' AND is_read = 0")->fetch_assoc()['count'];

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin – Messages</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
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
        .sidebar-menu .badge {
            margin-left: auto;
            background: #ec008c;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
        }
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
        
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .message-list { display: flex; flex-direction: column; gap: 1rem; }
        
        .message-item {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 16px rgba(236,0,140,0.05);
        }
        .message-item.unread {
            border-left: 4px solid #ec008c;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #fce8f1;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .message-header .customer-info .name {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            color: #1a0a10;
            font-size: 1rem;
        }
        .message-header .customer-info .email {
            font-size: 0.8rem;
            color: #8a4a60;
        }
        .message-header .meta { text-align: right; }
        .message-header .subject {
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            color: #ec008c;
            font-size: 0.9rem;
        }
        .message-header .date {
            font-size: 0.75rem;
            color: #8a4a60;
        }
        
        .message-body {
            background: #fce8f1;
            border-radius: 0.75rem;
            padding: 1rem;
            font-size: 0.9rem;
            color: #5c3a43;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .reply-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .reply-box .reply-label {
            font-size: 0.75rem;
            color: #166534;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }
        .reply-box .reply-text {
            font-size: 0.9rem;
            color: #166534;
            line-height: 1.6;
        }
        
        .reply-form textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.75rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.9rem;
            outline: none;
            min-height: 80px;
            resize: vertical;
            margin-bottom: 0.5rem;
        }
        .reply-form textarea:focus { border-color: #ec008c; }
        .btn-reply {
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1.25rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-reply:hover { background: #c40075; }
        
        .btn-mark-read {
            background: #e0e7ff;
            color: #3730a3;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            text-decoration: none;
            display: inline-block;
        }
        
        .empty-state {
            background: #fff;
            border: 1px solid #f5c6d8;
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-family: 'Fredoka', sans-serif; color: #1a0a10; margin-bottom: 0.5rem; }
        .empty-state p { color: #8a4a60; }
        
        .view-store-btn {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: #10b981;
            color: #fff;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.15s;
        }
        .view-store-btn:hover { background: #059669; transform: translateY(-1px); }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; }
            .admin-main { margin-left: 0; }
            .admin-wrapper { flex-direction: column; }
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
            <li><a href="products.php"><span class="icon">🧋</span> Products</a></li>
            <li><a href="categories.php"><span class="icon">🏷️</span> Categories</a></li>
            <li><a href="users.php"><span class="icon">👤</span> Users</a></li>
            <li><a href="messages.php" class="active"><span class="icon">💬</span> Messages <?php if ($unreadCount > 0): ?><span class="badge"><?= $unreadCount ?></span><?php endif; ?></a></li>
        </ul>
        <hr class="sidebar-divider">
        <div class="sidebar-footer">
            <a href="../index.php">👁 View Store</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </aside>
    
    <main class="admin-main">
        <div class="admin-header-bar">
            <h1>💬 Messages <?php if ($unreadCount > 0): ?><span style="background:#ec008c; color:#fff; font-size:0.8rem; padding:0.2rem 0.7rem; border-radius:999px; margin-left:0.5rem;"><?= $unreadCount ?> new</span><?php endif; ?></h1>
            <a href="../index.php" class="view-store-btn">👁 View Store</a>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>No Messages Yet</h3>
                <p>When customers send messages, they'll appear here.</p>
            </div>
        <?php else: ?>
            <div class="message-list">
                <?php foreach ($messages as $msg): ?>
                    <div class="message-item <?= !$msg['is_read'] ? 'unread' : '' ?>">
                        <div class="message-header">
                            <div class="customer-info">
                                <div class="name">
                                    <?= htmlspecialchars($msg['first_name'] ?? '') . ' ' . htmlspecialchars($msg['last_name'] ?? '') ?>
                                </div>
                                <div class="email"><?= htmlspecialchars($msg['email'] ?? '') ?></div>
                            </div>
                            <div class="meta">
                                <div class="subject"><?= htmlspecialchars($msg['subject']) ?></div>
                                <div class="date"><?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?></div>
                                <?php if (!$msg['is_read']): ?>
                                    <a href="messages.php?read=<?= $msg['id'] ?>" class="btn-mark-read" style="margin-top:0.3rem;">Mark as Read</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="message-body">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                        
                        <?php
                        // Get existing replies
                        $replyStmt = $conn->prepare("SELECT * FROM messages WHERE parent_id = ? AND sender = 'admin' ORDER BY created_at ASC");
                        $replyStmt->bind_param("i", $msg['id']);
                        $replyStmt->execute();
                        $replies = $replyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        ?>
                        
                        <?php foreach ($replies as $reply): ?>
                            <div class="reply-box">
                                <div class="reply-label">✅ Your Reply · <?= date('M d, Y h:i A', strtotime($reply['created_at'])) ?></div>
                                <div class="reply-text"><?= nl2br(htmlspecialchars($reply['message'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Reply Form -->
                        <form action="messages.php" method="POST" class="reply-form">
                            <input type="hidden" name="parent_id" value="<?= $msg['id'] ?>">
                            <input type="hidden" name="user_id" value="<?= $msg['user_id'] ?>">
                            <textarea name="reply_message" placeholder="Type your reply..." required></textarea>
                            <button type="submit" class="btn-reply">📨 Send Reply</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
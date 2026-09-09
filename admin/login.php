<?php
require_once '../function.php';

// If already logged in as admin, go to dashboard
if (isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin') {
    redirect('index.php');
}

// If logged in but not admin, show message
if (isLoggedIn()) {
    setFlash('error', 'You are logged in as a customer. Please logout first.');
    redirect('../index.php');
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Fredoka', sans-serif;
            background: #1a0a10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        
        .admin-login-container {
            width: 100%;
            max-width: 440px;
        }
        
        .admin-login-box {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .admin-login-box .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .admin-login-box .logo img {
            width: 120px;
            margin: 0 auto 0.5rem;
        }
        .admin-login-box .logo h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 1.5rem;
            color: #1a0a10;
        }
        .admin-login-box .logo h1 span {
            color: #ec008c;
        }
        .admin-login-box .logo .sub {
            font-size: 0.8rem;
            color: #8a4a60;
            font-weight: 400;
        }
        
        .admin-login-box .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #1a0a10;
            color: #fff;
            padding: 0.3rem 1rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.5rem;
        }
        
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1a0a10;
            margin-bottom: 0.3rem;
        }
        .form-group input {
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
        .form-group input:focus {
            border-color: #ec008c;
            box-shadow: 0 0 0 3px rgba(236,0,140,0.08);
        }
        .form-group input.is-error { border-color: #ef4444; }
        .field-error {
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 0.2rem;
        }
        
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 3rem; }
        .toggle-pw {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #8a4a60;
            font-size: 1rem;
            padding: 0;
        }
        .toggle-pw:hover { color: #ec008c; }
        
        .btn-login {
            display: block;
            width: 100%;
            background: #ec008c;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.85rem;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: all 0.15s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(236,0,140,0.4);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #8a4a60;
        }
        .login-footer a {
            color: #ec008c;
            font-weight: 600;
            text-decoration: none;
        }
        .login-footer a:hover { text-decoration: underline; }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: #8a4a60;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: #ec008c; }
        
        .admin-shield {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 480px) {
            .admin-login-box { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="admin-login-container">
    <div class="admin-login-box">
        
        <div class="logo">
            <div class="admin-shield">🛡️</div>
            <img src="../images/cadienteamainlogo.png" alt="CadienTea" />
            <h1>Cadien<span>Tea</span></h1>
            <div class="sub">Admin Panel</div>
        </div>
        
        <div style="text-align:center; margin-bottom:1.5rem;">
            <span class="badge">🔐 Admin Access Only</span>
        </div>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <?php
        $errors = $_SESSION['login_errors'] ?? [];
        $old = $_SESSION['login_old'] ?? [];
        unset($_SESSION['login_errors'], $_SESSION['login_old']);
        ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form action="login_function.php" method="POST" novalidate>
            <?php
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
            
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    placeholder="admin@example.com"
                    class="<?= isset($errors['email']) ? 'is-error' : '' ?>"
                    autocomplete="email"
                    required
                />
                <?php if (isset($errors['email'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="<?= isset($errors['password']) ? 'is-error' : '' ?>"
                        autocomplete="current-password"
                        required
                    />
                    <button type="button" class="toggle-pw" aria-label="Toggle password" onclick="togglePassword()">
                        👁
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-login">🔐 Sign In as Admin</button>
        </form>
        
        <div class="login-footer">
            <a href="../login.php">← Customer Login</a>
            <br><br>
            <a href="../index.php">🏠 Back to Store</a>
        </div>
        
        <a href="../index.php" class="back-link">← Back to CadienTea Homepage</a>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const btn = document.querySelector('.toggle-pw');
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }
</script>

</body>
</html>
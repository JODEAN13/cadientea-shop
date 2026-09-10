<?php
require_once 'function.php';

// Already logged in — send to success page
if (isLoggedIn()) {
    redirect('success.php');
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        /* ── LOGIN PAGE STYLES ── */
        .login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .login-panel {
            background: linear-gradient(160deg, #f8b5c2 0%, #ec008c 70%, #a30062 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .login-panel::before {
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            top: -80px; left: -80px;
        }
        .login-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -60px; right: -60px;
        }
        .login-panel img {
            width: 180px;
            margin: 0 auto 1.5rem;
            position: relative; z-index: 1;
        }
        .login-panel h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem; font-weight: 700;
            margin-bottom: 0.75rem;
            position: relative; z-index: 1;
        }
        .login-panel p {
            font-size: 1rem; opacity: 0.9; line-height: 1.6;
            max-width: 280px;
            position: relative; z-index: 1;
        }
        .login-panel .tagline {
            margin-top: 2rem;
            font-size: 0.85rem;
            opacity: 0.75;
            font-style: italic;
            position: relative; z-index: 1;
        }

        .login-form-wrap {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background: #fff8fb;
        }
        .login-box {
            width: 100%;
            max-width: 420px;
        }
        .login-box h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem; font-weight: 700;
            color: #1a0a10;
            margin-bottom: 0.35rem;
        }
        .login-box .sub {
            font-size: 0.9rem;
            color: #8a4a60;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1a0a10;
            margin-bottom: 0.4rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.75rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.975rem;
            color: #1a0a10;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            border-color: #ec008c;
            box-shadow: 0 0 0 3px rgba(236,0,140,0.12);
        }
        .form-group input.is-error { border-color: #ef4444; }
        .field-error {
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 0.3rem;
        }

        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 3rem; }
        .toggle-pw {
            position: absolute; right: 0.9rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; color: #8a4a60;
            font-size: 1rem; padding: 0;
        }
        .toggle-pw:hover { color: #ec008c; }

        .btn-login {
            display: block; width: 100%;
            background: #ec008c; color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-weight: 700; font-size: 1.05rem;
            padding: 0.85rem;
            border: none; border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(236,0,140,0.3);
            transition: transform 0.15s, box-shadow 0.15s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 26px rgba(236,0,140,0.4);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.875rem;
            color: #8a4a60;
        }
        .login-footer a {
            color: #ec008c;
            font-weight: 700;
        }
        .login-footer a:hover { text-decoration: underline; }

        .back-link {
            display: inline-flex; align-items: center; gap: 0.35rem;
            margin-top: 1.25rem;
            font-size: 0.85rem; color: #8a4a60;
            transition: color 0.2s;
        }
        .back-link:hover { color: #ec008c; }

        @media (max-width: 700px) {
            .login-page { grid-template-columns: 1fr; }
            .login-panel { display: none; }
            .login-form-wrap { padding: 2.5rem 1.25rem; }
        }
    </style>
</head>
<body>

<div class="login-page">

    <!-- Left decorative panel -->
    <div class="login-panel">
        <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        <h2>Welcome Back!</h2>
        <p>Log in to track your orders, manage your account, and earn loyalty rewards.</p>
        <div class="tagline">"Sip the glow, love the flow."</div>
    </div>

    <!-- Right form panel -->
    <div class="login-form-wrap">
        <div class="login-box">
            <h1>Sign In</h1>
            <p class="sub">Enter your credentials to access your account.</p>

            <?php if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php
            $errors = $_SESSION['login_errors'] ?? [];
            $old    = $_SESSION['login_old']    ?? [];
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

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        placeholder="you@example.com"
                        class="<?= isset($errors['email']) ? 'is-error' : '' ?>"
                        autocomplete="email"
                        required
                    />
                    <?php if (isset($errors['email'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
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
                        <button type="button" class="toggle-pw" aria-label="Toggle password visibility" onclick="togglePassword()">
                            👁
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-login">Sign In 🧋</button>
            </form>

            <div class="login-footer">
                Don't have an account? <a href="register.php">Create Account</a>
            </div>

            <a href="index.php" class="back-link">← Back to CadienTea Homepage</a>
        </div>
    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const btn   = document.querySelector('.toggle-pw');
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
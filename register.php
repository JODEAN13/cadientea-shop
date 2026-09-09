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
    <title>Register – CadienTea</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        /* ── REGISTER PAGE STYLES ── */
        .register-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Left decorative panel */
        .register-panel {
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
        .register-panel::before {
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            top: -80px; left: -80px;
        }
        .register-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -60px; right: -60px;
        }
        .register-panel img {
            width: 180px;
            margin: 0 auto 1.5rem;
            position: relative; z-index: 1;
        }
        .register-panel h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem; font-weight: 700;
            margin-bottom: 0.75rem;
            position: relative; z-index: 1;
        }
        .register-panel p {
            font-size: 1rem; opacity: 0.9; line-height: 1.6;
            max-width: 280px;
            position: relative; z-index: 1;
        }
        .register-panel .tagline {
            margin-top: 2rem;
            font-size: 0.85rem;
            opacity: 0.75;
            font-style: italic;
            position: relative; z-index: 1;
        }

        /* Right form panel */
        .register-form-wrap {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 2rem;
            background: #fff8fb;
            min-height: 100vh;
            overflow-y: auto;
        }
        .register-box {
            width: 100%;
            max-width: 440px;
        }
        .register-box h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 2rem; font-weight: 700;
            color: #1a0a10;
            margin-bottom: 0.35rem;
        }
        .register-box .sub {
            font-size: 0.9rem;
            color: #8a4a60;
            margin-bottom: 1.5rem;
        }

        /* Alert boxes */
        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

        /* Form fields */
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-family: 'Fredoka', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1a0a10;
            margin-bottom: 0.3rem;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid #f5c6d8;
            border-radius: 0.75rem;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.95rem;
            color: #1a0a10;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ec008c;
            box-shadow: 0 0 0 3px rgba(236,0,140,0.12);
        }
        .form-group input.is-error,
        .form-group textarea.is-error { border-color: #ef4444; }
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
        .field-error {
            font-size: 0.78rem;
            color: #dc2626;
            margin-top: 0.2rem;
        }
        .field-hint {
            font-size: 0.75rem;
            color: #8a4a60;
            margin-top: 0.2rem;
        }

        /* Password wrapper */
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

        /* Password strength indicator */
        .password-strength {
            margin-top: 0.3rem;
            height: 4px;
            border-radius: 4px;
            background: #f5c6d8;
            overflow: hidden;
            transition: width 0.3s;
        }
        .password-strength .bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }
        .password-strength .bar.weak { width: 25%; background: #ef4444; }
        .password-strength .bar.medium { width: 50%; background: #f59e0b; }
        .password-strength .bar.strong { width: 75%; background: #10b981; }
        .password-strength .bar.very-strong { width: 100%; background: #059669; }

        .password-hints {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.3rem;
            font-size: 0.7rem;
            color: #8a4a60;
        }
        .password-hints .hint {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .password-hints .hint.valid { color: #059669; }
        .password-hints .hint.invalid { color: #ef4444; }

        /* Submit button */
        .btn-register {
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
        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 26px rgba(236,0,140,0.4);
        }

        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #8a4a60;
        }
        .register-footer a {
            color: #ec008c;
            font-weight: 700;
        }
        .register-footer a:hover { text-decoration: underline; }

        .back-link {
            display: inline-flex; align-items: center; gap: 0.35rem;
            margin-top: 1rem;
            font-size: 0.85rem; color: #8a4a60;
            transition: color 0.2s;
        }
        .back-link:hover { color: #ec008c; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 700px) {
            .register-page { grid-template-columns: 1fr; }
            .register-panel { display: none; }
            .register-form-wrap { padding: 1.5rem 1rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="register-page">

    <!-- Left decorative panel -->
    <div class="register-panel">
        <img src="images/cadienteamainlogo.png" alt="CadienTea logo" />
        <h2>Join the Community!</h2>
        <p>Create your account and start enjoying hand-crafted bubble teas made with love.</p>
        <div class="tagline">"Sip the glow, love the flow."</div>
    </div>

    <!-- Right form panel -->
    <div class="register-form-wrap">
        <div class="register-box">
            <h1>Create Account</h1>
            <p class="sub">Join the CadienTea community today 🧋</p>

            <?php if ($flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php
            // Show field errors passed back via session
            $errors = $_SESSION['register_errors'] ?? [];
            $old    = $_SESSION['register_old']    ?? [];
            unset($_SESSION['register_errors'], $_SESSION['register_old']);
            ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>

            <form action="register_function.php" method="POST" novalidate>
                <!-- CSRF token -->
                <?php
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

                <!-- First Name & Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                            placeholder="John"
                            class="<?= isset($errors['first_name']) ? 'is-error' : '' ?>"
                            required
                        />
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['first_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                            placeholder="Doe"
                            class="<?= isset($errors['last_name']) ? 'is-error' : '' ?>"
                            required
                        />
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors['last_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

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

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                        placeholder="09123456789"
                        class="<?= isset($errors['phone']) ? 'is-error' : '' ?>"
                    />
                    <?php if (isset($errors['phone'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea
                        id="address"
                        name="address"
                        placeholder="123 Main St, Dumaguete City"
                        class="<?= isset($errors['address']) ? 'is-error' : '' ?>"
                    ><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                    <?php if (isset($errors['address'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['address']) ?></div>
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
                            autocomplete="new-password"
                            required
                            onkeyup="checkPasswordStrength(this.value)"
                        />
                        <button type="button" class="toggle-pw" aria-label="Toggle password visibility" onclick="togglePassword('password')">
                            👁
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="bar" id="strengthBar"></div>
                    </div>
                    <div class="password-hints" id="passwordHints">
                        <span class="hint invalid" id="hintLength">❌ 8+ characters</span>
                        <span class="hint invalid" id="hintUppercase">❌ Uppercase letter</span>
                        <span class="hint invalid" id="hintLowercase">❌ Lowercase letter</span>
                        <span class="hint invalid" id="hintNumber">❌ Number</span>
                        <span class="hint invalid" id="hintSpecial">❌ Special character</span>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="••••••••"
                            class="<?= isset($errors['confirm_password']) ? 'is-error' : '' ?>"
                            autocomplete="new-password"
                            required
                        />
                        <button type="button" class="toggle-pw" aria-label="Toggle password visibility" onclick="togglePassword('confirm_password')">
                            👁
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-register">Create Account 🧋</button>
            </form>

            <div class="register-footer">
                Already have an account? <a href="login.php">Sign In</a>
            </div>

            <a href="index.php" class="back-link">← Back to CadienTea Homepage</a>
        </div>
    </div>

</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const btn = input.parentElement.querySelector('.toggle-pw');
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        const hints = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[\W_]/.test(password)
        };

        // Update hint icons
        document.getElementById('hintLength').textContent = (hints.length ? '✅' : '❌') + ' 8+ characters';
        document.getElementById('hintUppercase').textContent = (hints.uppercase ? '✅' : '❌') + ' Uppercase letter';
        document.getElementById('hintLowercase').textContent = (hints.lowercase ? '✅' : '❌') + ' Lowercase letter';
        document.getElementById('hintNumber').textContent = (hints.number ? '✅' : '❌') + ' Number';
        document.getElementById('hintSpecial').textContent = (hints.special ? '✅' : '❌') + ' Special character';

        // Update hint classes
        Object.keys(hints).forEach(key => {
            const el = document.getElementById('hint' + key.charAt(0).toUpperCase() + key.slice(1));
            if (el) {
                el.className = 'hint ' + (hints[key] ? 'valid' : 'invalid');
            }
        });

        // Update strength bar
        const bar = document.getElementById('strengthBar');
        const score = Object.values(hints).filter(Boolean).length;
        
        bar.className = 'bar';
        if (score === 0) { bar.style.width = '0%'; }
        else if (score <= 2) { bar.classList.add('weak'); }
        else if (score === 3) { bar.classList.add('medium'); }
        else if (score === 4) { bar.classList.add('strong'); }
        else if (score === 5) { bar.classList.add('very-strong'); }
    }
</script>

</body>
</html>
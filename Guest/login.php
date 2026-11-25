<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$redirectUrl = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$isNotice    = isset($_GET['notice']) && $_GET['notice'] === 'login_required';

// If already logged in, redirect to target destination or customer dashboard
if (isset($_SESSION['customer_id'])) {
    $dest = (!empty($redirectUrl) && strpos($redirectUrl, '://') === false && strpos($redirectUrl, '//') !== 0) 
        ? $redirectUrl 
        : "../Customer/index.php";
    header("Location: " . $dest);
    exit();
}
if (isset($_SESSION['Login_id'])) {
    header("Location: ../Admin/index.php");
    exit();
}

$pageTitle = "Login | MelodyMart - Premium Musical Instruments";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to your MelodyMart account to manage orders, wishlist, and instruments.">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Guest Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon"><i class="fa-solid fa-music"></i></span>
            <span class="logo-name">Melody<span>Mart</span></span>
        </a>

        <div class="nav-actions">
            <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-house"></i> Home</a>
            <a href="cusreg.php" class="btn btn-gold"><i class="fa-solid fa-user-plus"></i> Register</a>
        </div>
    </div>
</header>

<!-- MAIN AUTH SECTION -->
<main class="auth-wrapper">
    <div class="auth-container auth-narrow">
        <div class="auth-card">

            <!-- VISUAL SHOWCASE SIDE -->
            <div class="auth-visual">
                <div class="auth-visual-top">
                    <span class="brand-badge"><i class="fa-solid fa-shield-halved"></i> Secure Access</span>
                    <div class="auth-visual-content">
                        <h2>Welcome Back to <span>Sound.</span></h2>
                        <p>Sign in to discover curated instruments, track active orders, and access personalized setup advice from our experts.</p>

                        <ul class="auth-perks">
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>100% Authentic Handcrafted Gear</span>
                            </li>
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Safe Express Shock-Resistant Delivery</span>
                            </li>
                            <li class="auth-perk-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Direct Musician Technical Assistance</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="auth-visual-footer">
                    <span>Crafted for Musicians Worldwide</span>
                    <span>MelodyMart &copy; <?= date('Y') ?></span>
                </div>
            </div>

            <!-- LOGIN FORM SIDE -->
            <div class="auth-form-wrap">
                <div class="auth-header">
                    <span class="eyebrow"><i class="fa-solid fa-lock"></i> SIGN IN</span>
                    <h1>Account Login</h1>
                    <p>Enter your username and password to continue.</p>
                </div>

                <?php if ($isNotice || !empty($redirectUrl)): ?>
                    <div style="background: rgba(215, 173, 105, 0.15); border: 1px solid #d7ad69; border-radius: 8px; padding: 12px 14px; margin-bottom: 22px; display: flex; align-items: center; gap: 12px; font-size: 13px; color: #272a25;">
                        <i class="fa-solid fa-lock" style="font-size: 18px; color: #b88636 !important; flex-shrink: 0;"></i>
                        <span style="line-height: 1.4;"><strong>Sign in required:</strong> Please sign in to your MelodyMart customer account to view instrument specifications and complete your purchase.</span>
                    </div>
                <?php endif; ?>

                <form action="loginaction.php" method="POST" class="auth-form" autocomplete="off">
                    <?php if (!empty($redirectUrl)): ?>
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectUrl) ?>">
                    <?php endif; ?>

                    <!-- Username Field -->
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-box">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-input"
                                placeholder="Enter your username"
                                required
                                autocomplete="off"
                                value=""
                                autofocus>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Enter your password"
                                required
                                autocomplete="new-password"
                                value="">
                            <i class="fa-regular fa-eye toggle-password" id="togglePassword" title="Show/Hide Password"></i>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-gold btn-submit" style="margin-top: 10px;">
                        Sign In <i class="fa-solid fa-arrow-right"></i>
                    </button>

                </form>

                <div class="auth-switch-text">
                    Don't have an account yet?
                    <a href="cusreg.php">Create an Account</a>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    // Ensure inputs are completely empty on load for manual entry
    window.addEventListener('DOMContentLoaded', () => {
        const u = document.getElementById('username');
        const p = document.getElementById('password');
        if (u) u.value = '';
        if (p) p.value = '';
    });

    // Password visibility toggle
    const toggleBtn = document.getElementById('togglePassword');
    const pwdInput = document.getElementById('password');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            toggleBtn.classList.toggle('fa-eye', !isPassword);
            toggleBtn.classList.toggle('fa-eye-slash', isPassword);
        });
    }
</script>

</body>
</html>
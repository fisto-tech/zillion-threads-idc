<?php
// Root Login Gateway for Zillion Threads Catalog Admin
session_start();

// Compute base path relative to current script for portable assets & redirects
$isSubdir = basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'login';
$basePrefix = $isSubdir ? '../' : '';
$adminRedirect = $basePrefix . 'admin/index.php';

// If already logged in, redirect directly to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . $adminRedirect);
    exit();
}

// Credentials
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

$error = '';

// Process Login from root directory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . $adminRedirect);
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zillion Threads Admin Login</title>
    <link rel="icon" href="<?php echo $basePrefix; ?>global%20assets/Images/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #1A7292;
            --brand-gold: #D4A03C;
            --brand-gold-hover: #BF8D2D;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --text-dim: #94A3B8;
            --card-bg: #FFFFFF;
            --input-bg: #FAFCFE;
            --input-border: #E2E8F0;
            --input-focus: #D4A03C;
            --error-bg: #FEF2F2;
            --error-border: #FCA5A5;
            --error-text: #DC2626;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
            background-color: #EFECE6;
            background-image: url('<?php echo $basePrefix; ?>global%20assets/Images/background-images/background-image-1.webp');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Subtle responsive page overlay container */
        .login-page-container {
            width: 100%;
            max-width: 1200px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2rem 5%;
            position: relative;
            z-index: 10;
        }

        /* Left Side: Brand presentation (ZILLION THREADS logo + title) */
        .brand-showcase-panel {
            flex: 1;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-right: 2rem;
            animation: fadeInLeft 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-24px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .brand-emblem-box {
            width: auto;
            height: auto;
            background: transparent;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            margin-bottom: 1.25rem;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .brand-emblem-box:hover {
            transform: scale(1.06);
        }

        .brand-emblem-box img {
            width: 78px;
            height: 78px;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.15));
        }

        .brand-main-title {
            font-family: 'Prompt', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.2;
        }

        .brand-main-title span.threads-gold {
            color: var(--brand-gold);
        }

        .brand-subline {
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 2.2px;
            text-transform: uppercase;
            color: #64748B;
            margin-top: 0.65rem;
        }

        /* Right Side: Clean White Floating Login Card (Enlarged) */
        .login-card-container {
            flex: 1.15;
            max-width: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(24px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .login-card {
            width: 100%;
            background: var(--card-bg);
            border-radius: 26px;
            padding: 3.25rem 2.85rem 3rem;
            box-shadow: 
                0 25px 60px -12px rgba(0, 0, 0, 0.16),
                0 12px 28px -6px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .card-top-tag {
            text-align: center;
            margin-bottom: 0.4rem;
        }

        .admin-portal-text {
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #475569;
            display: inline-block;
        }

        .gold-divider {
            width: 38px;
            height: 3px;
            background: var(--brand-gold);
            border-radius: 4px;
            margin: 8px auto 1.35rem;
        }

        .card-heading-title {
            font-family: 'Prompt', sans-serif;
            font-size: 2.15rem;
            font-weight: 800;
            color: #0F172A;
            text-align: center;
            letter-spacing: -0.5px;
            margin-bottom: 0.45rem;
        }

        .card-subtext {
            text-align: center;
            font-size: 0.92rem;
            color: var(--text-muted);
            margin-bottom: 2.25rem;
            line-height: 1.45;
        }

        .alert-error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.35s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }

        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            display: block;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #94A3B8;
            display: flex;
            align-items: center;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 14px;
            padding: 1.05rem 1.15rem 1.05rem 3.1rem;
            font-size: 1.02rem;
            color: var(--text-dark);
            font-family: inherit;
            outline: none;
            transition: all 0.22s ease;
        }

        .form-input::placeholder {
            color: #94A3B8;
        }

        .form-input:focus {
            border-color: var(--input-focus);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(212, 160, 60, 0.16);
        }

        .form-input:focus ~ .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--brand-gold);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            background: transparent;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--brand-gold);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #DEAB45 0%, #CE9632 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 1.1rem 1.75rem;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 2rem;
            box-shadow: 0 8px 22px rgba(206, 150, 50, 0.4);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #E5B450 0%, #C48E28 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(206, 150, 50, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-link-wrap {
            margin-top: 1.5rem;
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            color: #0F172A;
            background: rgba(255, 255, 255, 0.6);
            transform: translateX(-2px);
        }

        /* Mobile / Tablet Responsiveness */
        @media (max-width: 900px) {
            .login-page-container {
                flex-direction: column;
                justify-content: center;
                gap: 2rem;
                padding: 2.5rem 1.25rem;
                min-height: 100vh;
            }

            .brand-showcase-panel {
                padding-right: 0;
                max-width: 100%;
            }

            .brand-main-title {
                font-size: 1.65rem;
                justify-content: center;
            }

            .brand-emblem-box {
                margin-bottom: 0.85rem;
            }
            .brand-emblem-box img {
                width: 68px;
                height: 68px;
            }

            .login-card-container {
                width: 100%;
                max-width: 460px;
            }

            .login-card {
                padding: 2.25rem 2rem;
                border-radius: 22px;
            }

            .card-heading-title {
                font-size: 1.85rem;
            }
        }

        @media (max-width: 480px) {
            .login-page-container {
                padding: 2rem 1rem;
                gap: 1.5rem;
            }

            .brand-main-title {
                font-size: 1.35rem;
                letter-spacing: 1px;
            }

            .brand-subline {
                font-size: 0.68rem;
                letter-spacing: 1.2px;
            }

            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 18px;
            }

            .card-heading-title {
                font-size: 1.6rem;
            }

            .card-subtext {
                font-size: 0.85rem;
                margin-bottom: 1.5rem;
            }

            .form-input {
                font-size: 0.95rem;
                padding: 0.85rem 1rem 0.85rem 2.75rem;
            }

            .input-icon {
                left: 12px;
            }

            .btn-submit {
                padding: 0.95rem 1.25rem;
                font-size: 0.98rem;
                margin-top: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-page-container">
        <!-- Left Side: Brand presentation -->
        <div class="brand-showcase-panel">
            <div class="brand-emblem-box">
                <img src="<?php echo $basePrefix; ?>global%20assets/Images/logo.svg" onerror="this.onerror=null; this.src='<?php echo $basePrefix; ?>global%20assets/Images/logo.svg';" alt="Zillion Threads Logo">
            </div>
            <div class="brand-main-title">
                <span>ZILLION</span>
                <span class="threads-gold">THREADS</span>
            </div>
            <p class="brand-subline">IDC INTERACTIVE DIGITAL CATALOGUE MANAGEMENT</p>
        </div>

        <!-- Right Side: Clean White Floating Card -->
        <div class="login-card-container">
            <div class="login-card">
                <div class="card-top-tag">
                    <span class="admin-portal-text">ADMIN PORTAL</span>
                    <div class="gold-divider"></div>
                </div>

                <h1 class="card-heading-title">Sign In</h1>
                <p class="card-subtext">Access your admin account to continue.</p>

                <?php if (!empty($error)): ?>
                    <div class="alert-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input type="text" id="username" name="username" class="form-input" placeholder="admin" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="toggle-password" id="togglePassBtn" title="Toggle password visibility">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="login_submit" class="btn-submit">
                        <span>Sign In</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="back-link-wrap">
                <a href="<?php echo $basePrefix; ?>index.html" class="back-link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Product Catalogue
                </a>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassBtn');
        const passInput = document.getElementById('password');
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passInput.setAttribute('type', type);
                this.innerHTML = type === 'password'
                    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
                    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            });
        }
    </script>
</body>
</html>

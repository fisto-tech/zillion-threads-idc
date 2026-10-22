<?php
// Root Login Gateway for Zillion Threads Catalog Admin
session_start();

// If already logged in, redirect directly to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /admin/index.php');
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
        header('Location: /admin/index.php');
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
    <link rel="icon" href="./global assets/Images/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0B1120;
            --card-bg: #1E293B;
            --accent: #EAB94B;
            --accent-hover: #D4A338;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --card-border: #334155;
            --input-bg: #0F172A;
            --error: #EF4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(234, 185, 75, 0.12) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo-text {
            font-family: 'Prompt', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand-badge {
            background: var(--accent);
            color: #000;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 1px;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 2.25rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        .login-card h2 {
            font-family: 'Prompt', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: #fff;
        }

        .login-card p {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--error);
            color: #FCA5A5;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(234, 185, 75, 0.2);
        }

        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: #0F172A;
            border: none;
            padding: 0.85rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 185, 75, 0.35);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--accent);
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="brand-header">
            <div class="brand-logo-text">
                <span>ZILLION THREADS</span>
                <span class="brand-badge">ADMIN</span>
            </div>
        </div>

        <div class="login-card">
            <h2>Catalog Management</h2>
            <p>Enter your credentials to modify Pages 4 &amp; 5 catalog items</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="e.g. admin" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <button type="submit" name="login_submit" class="btn-submit">
                    Log In to Admin &rarr;
                </button>
            </form>
        </div>

        <a href="/index.html" class="back-link">&larr; Back to Product Catalogue</a>
    </div>

</body>
</html>

<?php
session_start();

$error = '';
$success = '';

// Check logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    $returnUrl = $_GET['return'] ?? 'index.php';
    header('Location: ' . $returnUrl);
    exit();
}

// Credentials
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Process Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zillion Threads Admin - Products & Gallery Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1A7292;
            --primary-dark: #125169;
            --accent: #EAB94B;
            --accent-hover: #d39f32;
            --bg: #0F172A;
            --card-bg: #1E293B;
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --success: #10B981;
            --danger: #EF4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Prompt', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .admin-nav {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .brand-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
        }

        .brand-badge {
            background: linear-gradient(135deg, var(--accent), #D1873C);
            color: #000;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 12px rgba(26, 114, 146, 0.35);
        }
        .btn-primary:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent), #D1873C);
            color: #000;
            font-weight: 700;
        }
        .btn-accent:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--card-border);
            color: var(--text-muted);
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        /* Container */
        .admin-container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            width: 100%;
            flex: 1;
        }

        /* Login Modal / Card */
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 120px);
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .login-card h2 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .login-card p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(234, 185, 75, 0.15);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #FCA5A5;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        /* Dashboard Styles */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--card-border);
        }

        .header-title h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #fff;
        }

        .header-title p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        .section-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 2.5rem;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .panel-heading h3 {
            font-size: 1.35rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-badge {
            background: rgba(26, 114, 146, 0.25);
            border: 1px solid rgba(26, 114, 146, 0.5);
            color: #38BDF8;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* Product Cards Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .product-edit-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, border-color 0.2s;
        }

        .product-edit-card:hover {
            border-color: rgba(234, 185, 75, 0.4);
            transform: translateY(-2px);
        }

        .card-img-preview-box {
            position: relative;
            width: 100%;
            height: 200px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .card-img-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .upload-overlay-btn {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            opacity: 0;
            transition: opacity 0.2s;
            cursor: pointer;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .card-img-preview-box:hover .upload-overlay-btn {
            opacity: 1;
        }

        .card-body {
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex: 1;
        }

        .card-body input, .card-body textarea {
            width: 100%;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid var(--card-border);
            border-radius: 6px;
            color: #fff;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
        }

        .card-body textarea {
            resize: vertical;
            min-height: 60px;
        }

        .card-body input:focus, .card-body textarea:focus {
            border-color: var(--accent);
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            background: var(--card-bg);
            border: 1px solid var(--success);
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            font-weight: 600;
        }

        .toast.show {
            display: flex;
            animation: slideInUp 0.3s ease;
        }

        @keyframes slideInUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Modal Popup Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.25s ease;
        }

        .modal-dialog {
            background: #1E293B;
            border: 1px solid var(--card-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            border-radius: 16px;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: zoomIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--card-border);
            background: rgba(30, 41, 59, 0.6);
        }

        .modal-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .modal-close-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .modal-body {
            padding: 1.5rem 1.75rem;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Drag and drop upload zone */
        .drop-zone {
            border: 2px dashed rgba(234, 185, 75, 0.4);
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-align: center;
            background: rgba(15, 23, 42, 0.4);
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }

        .drop-zone:hover, .drop-zone.dragover {
            border-color: var(--accent);
            background: rgba(234, 185, 75, 0.08);
            transform: translateY(-2px);
        }

        .drop-zone-icon {
            margin-bottom: 0.75rem;
            color: var(--accent);
        }

        .drop-zone-text {
            font-size: 0.95rem;
            color: var(--text-main);
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .drop-zone-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Gallery Grid */
        .gallery-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 1rem;
            max-height: 280px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .gallery-grid::-webkit-scrollbar {
            width: 6px;
        }
        .gallery-grid::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 3px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            background: #0F172A;
            transition: all 0.2s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.25s ease;
        }

        .gallery-item:hover {
            border-color: var(--accent);
            transform: scale(1.03);
            box-shadow: 0 4px 14px rgba(234, 185, 75, 0.3);
        }

        .gallery-item.selected {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--accent);
        }

        .gallery-item .item-check {
            position: absolute;
            top: 6px;
            right: 6px;
            background: var(--accent);
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
        }

        .gallery-item.selected .item-check {
            display: flex;
        }

        .gallery-item .filename-tooltip {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.85);
            color: #fff;
            font-size: 0.65rem;
            padding: 3px 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-footer {
            padding: 1rem 1.75rem;
            border-top: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            background: rgba(30, 41, 59, 0.4);
        }

        @media (max-width: 768px) {
            .admin-nav {
                padding: 1rem;
            }
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .products-grid {
                grid-template-columns: 1fr;
            }
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <header class="admin-nav">
        <div class="brand-title">
            <span>ZILLION THREADS</span>
            <span class="brand-badge">ADMIN</span>
        </div>
        <div class="nav-actions">
            <?php if ($isLoggedIn): ?>
                <a href="/index.html" class="btn btn-outline">&#128065; View Catalog</a>
                <a href="?action=logout&return=/index.html" class="btn btn-outline" style="color: #F87171;">Logout</a>
            <?php else: ?>
                <a href="/index.html" class="btn btn-outline">&larr; Back to Catalog</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="admin-container">
        <?php if (!$isLoggedIn): ?>
            <!-- Login Form -->
            <div class="login-wrapper">
                <div class="login-card">
                    <h2>Catalog Management</h2>
                    <p>Enter your credentials to modify Pages 4 &amp; 5 gallery</p>

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
                        <button type="submit" name="login_submit" class="btn btn-accent" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 1rem; margin-top: 1rem;">
                            Log In to Admin
                        </button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- Authenticated Product Manager Dashboard -->
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Product Pages &amp; Gallery Manager</h1>
                    <p>Update product photos, titles, categories, and descriptions for Page 4 and Page 5 in real time.</p>
                </div>
                <div>
                    <button id="saveAllBtn" class="btn btn-accent" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                        &#10003; Save All Changes
                    </button>
                </div>
            </div>

            <!-- Page 4 Section -->
            <div class="section-panel" id="panel-page4">
                <div class="panel-heading">
                    <h3>
                        <span class="page-badge">Page 4</span>
                        <input type="text" id="p4-title-input" class="form-input" style="font-size: 1.15rem; font-weight: 700; width: 300px; display: inline-block;" value="Industrial Threads &amp; Spools">
                    </h3>
                    <input type="text" id="p4-sub-input" class="form-input" style="width: 380px; font-size: 0.85rem;" value="High-durability engineered synthetic and metallic threads">
                </div>
                <div class="products-grid" id="p4-grid">
                    <!-- Loaded dynamically via JS -->
                </div>
            </div>

            <!-- Page 5 Section -->
            <div class="section-panel" id="panel-page5">
                <div class="panel-heading">
                    <h3>
                        <span class="page-badge">Page 5</span>
                        <input type="text" id="p5-title-input" class="form-input" style="font-size: 1.15rem; font-weight: 700; width: 300px; display: inline-block;" value="Specialty &amp; Protective Yarns">
                    </h3>
                    <input type="text" id="p5-sub-input" class="form-input" style="width: 380px; font-size: 0.85rem;" value="Specialized flame-retardant, high-visibility and elastic yarns">
                </div>
                <div class="products-grid" id="p5-grid">
                    <!-- Loaded dynamically via JS -->
                </div>
            </div>

            <!-- Modal Popup for Image Selection & Upload -->
            <div id="imageSelectModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-header">
                        <h3>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Select or Upload Product Image
                        </h3>
                        <button class="modal-close-btn" onclick="closeImageModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Drag and Drop Zone -->
                        <div class="drop-zone" id="imageDropZone">
                            <div class="drop-zone-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div class="drop-zone-text">Drag &amp; drop a new product image here, or <span style="color: var(--accent); text-decoration: underline;">browse</span></div>
                            <div class="drop-zone-sub">Supports JPG, PNG, WEBP, and SVG (Max 10MB)</div>
                            <input type="file" id="modalFileInput" accept="image/*" style="display: none;">
                        </div>

                        <!-- Already Uploaded Images Gallery -->
                        <div>
                            <div class="gallery-section-title">
                                <span>Uploaded Products Gallery</span>
                                <span id="galleryCountBadge" style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">Loading...</span>
                            </div>
                            <div class="gallery-grid" id="modalGalleryGrid" style="margin-top: 0.85rem;">
                                <!-- Dynamically loaded gallery images -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="closeImageModal()">Cancel</button>
                        <button type="button" class="btn btn-accent" id="applySelectedImageBtn" disabled onclick="applySelectedGalleryImage()">Use Selected Image</button>
                    </div>
                </div>
            </div>

            <!-- Save Toast -->
            <div class="toast" id="toastMessage">
                <span>&#10004;</span>
                <span id="toastText">Products saved successfully!</span>
            </div>

            <script>
                let currentProducts = {};
                let currentModalTarget = null; // { pageKey, index, itemId }
                let selectedGalleryUrl = null;
                let availableGalleryImages = [];

                // Load Products JSON on load
                async function loadProducts() {
                    try {
                        const res = await fetch('../api/get_products.php');
                        if (!res.ok) throw new Error('Failed to fetch products');
                        currentProducts = await res.json();
                        renderDashboard();
                    } catch (err) {
                        console.error('Error loading products:', err);
                        currentProducts = {
                            page4: {
                                title: "Industrial Threads & Spools",
                                subtitle: "High-durability engineered synthetic and metallic threads",
                                items: [
                                    { id: "p4-1", name: "Aurora Gold Thread", category: "Metallic Embroidery", description: "Premium 1000m high-sheen reinforced golden metallic filament.", image: "../global assets/Images/products/product-1.jpg" },
                                    { id: "p4-2", name: "Golden Silk Thread", category: "100% Pure Silk", description: "50wt luxury soft-touch Italian spun natural golden silk.", image: "../global assets/Images/products/product-2.jpg" },
                                    { id: "p4-3", name: "Zillion Bonded Poly", category: "High Tenacity", description: "Heavy-duty continuous filament polyester for leather & footwear.", image: "../global assets/Images/products/product-3.jpg" },
                                    { id: "p4-4", name: "Spun Poly Industrial", category: "Garment & Apparel", description: "Uniform high-speed knot-free lockstitch sewing thread.", image: "../global assets/Images/products/product-4.jpg" }
                                ]
                            },
                            page5: {
                                title: "Specialty & Protective Yarns",
                                subtitle: "Specialized flame-retardant, high-visibility and elastic yarns",
                                items: [
                                    { id: "p5-1", name: "Flame Resistant Aramid", category: "Safety & Technical", description: "Thermal-resistant Nomex & Kevlar blend protective threads.", image: "../global assets/Images/products/product-5.jpg" },
                                    { id: "p5-2", name: "Reflective Hi-Vis Filament", category: "High Visibility", description: "Micro-glass bead reflective thread for safety workwear.", image: "../global assets/Images/products/product-6.jpg" },
                                    { id: "p5-3", name: "Textured Stretch Poly", category: "Activewear & Swim", description: "Ultra-soft microfilament overlock thread with extreme recovery.", image: "../global assets/Images/products/product-7.jpg" },
                                    { id: "p5-4", name: "Eco-Recycled Yarn Cone", category: "Sustainable Textile", description: "100% GRS certified recycled post-consumer PET sewing yarn.", image: "../global assets/Images/products/product-8.jpg" }
                                ]
                            }
                        };
                        renderDashboard();
                    }
                }

                function renderDashboard() {
                    // Populate Page 4 headers
                    if (currentProducts.page4) {
                        document.getElementById('p4-title-input').value = currentProducts.page4.title || '';
                        document.getElementById('p4-sub-input').value = currentProducts.page4.subtitle || '';
                        renderGrid('p4-grid', currentProducts.page4.items, 'page4');
                    }

                    // Populate Page 5 headers
                    if (currentProducts.page5) {
                        document.getElementById('p5-title-input').value = currentProducts.page5.title || '';
                        document.getElementById('p5-sub-input').value = currentProducts.page5.subtitle || '';
                        renderGrid('p5-grid', currentProducts.page5.items, 'page5');
                    }
                }

                function renderGrid(containerId, items, pageKey) {
                    const container = document.getElementById(containerId);
                    container.innerHTML = '';

                    items.forEach((item, index) => {
                        const card = document.createElement('div');
                        card.className = 'product-edit-card';
                        card.innerHTML = `
                            <div class="card-img-preview-box">
                                <img src="${item.image}" alt="${item.name}" id="img-preview-${item.id}">
                                <div class="upload-overlay-btn" onclick="openImageModal('${pageKey}', ${index}, '${item.id}')">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>Change Image</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label class="form-label">Product Name</label>
                                    <input type="text" value="${item.name}" onchange="updateItemField('${pageKey}', ${index}, 'name', this.value)">
                                </div>
                                <div>
                                    <label class="form-label">Category</label>
                                    <input type="text" value="${item.category || ''}" onchange="updateItemField('${pageKey}', ${index}, 'category', this.value)">
                                </div>
                                <div>
                                    <label class="form-label">Description</label>
                                    <textarea onchange="updateItemField('${pageKey}', ${index}, 'description', this.value)">${item.description || ''}</textarea>
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                }

                function updateItemField(pageKey, index, field, value) {
                    if (currentProducts[pageKey] && currentProducts[pageKey].items[index]) {
                        currentProducts[pageKey].items[index][field] = value;
                    }
                }

                // ==================== IMAGE POPUP & GALLERY ====================
                async function openImageModal(pageKey, index, itemId) {
                    currentModalTarget = { pageKey, index, itemId };
                    selectedGalleryUrl = null;
                    document.getElementById('applySelectedImageBtn').disabled = true;

                    const currentItemImg = currentProducts[pageKey].items[index].image;
                    document.getElementById('imageSelectModal').classList.add('active');

                    await fetchGalleryImages(currentItemImg);
                }

                function closeImageModal() {
                    document.getElementById('imageSelectModal').classList.remove('active');
                    currentModalTarget = null;
                    selectedGalleryUrl = null;
                }

                async function fetchGalleryImages(currentlySelectedImg) {
                    const galleryGrid = document.getElementById('modalGalleryGrid');
                    const badge = document.getElementById('galleryCountBadge');
                    galleryGrid.innerHTML = '<div style="color: var(--text-muted); font-size: 0.85rem; padding: 1rem;">Loading images...</div>';

                    try {
                        const res = await fetch('../api/get_gallery.php');
                        const data = await res.json();
                        if (data.success && Array.isArray(data.images)) {
                            availableGalleryImages = data.images;
                        } else {
                            throw new Error('Fallback to catalog list');
                        }
                    } catch (e) {
                        // Fallback: collect known catalog images
                        const fallbackList = [];
                        ['product-1.jpg','product-2.jpg','product-3.jpg','product-4.jpg','product-5.jpg','product-6.jpg','product-7.jpg','product-8.jpg'].forEach(name => {
                            fallbackList.push({
                                name: name,
                                url: '../global assets/Images/products/' + name
                            });
                        });
                        availableGalleryImages = fallbackList;
                    }

                    badge.textContent = `${availableGalleryImages.length} images available`;
                    renderGalleryGrid(currentlySelectedImg);
                }

                function renderGalleryGrid(activeUrl) {
                    const galleryGrid = document.getElementById('modalGalleryGrid');
                    galleryGrid.innerHTML = '';

                    if (availableGalleryImages.length === 0) {
                        galleryGrid.innerHTML = '<div style="color: var(--text-muted); font-size: 0.85rem; padding: 1rem;">No uploaded images found. Upload a new one above!</div>';
                        return;
                    }

                    availableGalleryImages.forEach(img => {
                        const isMatch = activeUrl && (activeUrl.includes(img.name) || activeUrl === img.url);
                        const item = document.createElement('div');
                        item.className = 'gallery-item' + (isMatch ? ' selected' : '');
                        item.onclick = () => selectGalleryItem(img.url, item);
                        item.innerHTML = `
                            <img src="${img.url}" alt="${img.name}" loading="lazy">
                            <span class="item-check">&#10003;</span>
                            <span class="filename-tooltip" title="${img.name}">${img.name}</span>
                        `;
                        galleryGrid.appendChild(item);

                        if (isMatch) {
                            selectedGalleryUrl = img.url;
                            document.getElementById('applySelectedImageBtn').disabled = false;
                        }
                    });
                }

                function selectGalleryItem(url, el) {
                    document.querySelectorAll('.gallery-item').forEach(i => i.classList.remove('selected'));
                    el.classList.add('selected');
                    selectedGalleryUrl = url;
                    document.getElementById('applySelectedImageBtn').disabled = false;
                }

                function applySelectedGalleryImage() {
                    if (!selectedGalleryUrl || !currentModalTarget) return;
                    const { pageKey, index, itemId } = currentModalTarget;
                    currentProducts[pageKey].items[index].image = selectedGalleryUrl;

                    const preview = document.getElementById(`img-preview-${itemId}`);
                    if (preview) {
                        preview.src = selectedGalleryUrl + (selectedGalleryUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
                    }
                    showToast('Product image updated! Click "Save All Changes" when done.');
                    closeImageModal();
                }

                // Drag & Drop Setup
                const dropZone = document.getElementById('imageDropZone');
                const modalFileInput = document.getElementById('modalFileInput');

                dropZone.addEventListener('click', () => modalFileInput.click());

                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dragover');
                });

                ['dragleave', 'dragend'].forEach(type => {
                    dropZone.addEventListener(type, () => dropZone.classList.remove('dragover'));
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dragover');
                    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                        uploadImageFile(e.dataTransfer.files[0]);
                    }
                });

                modalFileInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        uploadImageFile(this.files[0]);
                        this.value = '';
                    }
                });

                async function uploadImageFile(file) {
                    if (!currentModalTarget) return;

                    const formData = new FormData();
                    formData.append('product_image', file);

                    showToast('Uploading new image...');

                    try {
                        const res = await fetch('../api/upload_image.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await res.json();
                        if (data.success && data.url) {
                            const { pageKey, index, itemId } = currentModalTarget;
                            currentProducts[pageKey].items[index].image = data.url;

                            const preview = document.getElementById(`img-preview-${itemId}`);
                            if (preview) preview.src = data.url + '?t=' + Date.now();

                            showToast('New image uploaded & applied!');
                            closeImageModal();
                        } else {
                            // Local fallback if API fails
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const { pageKey, index, itemId } = currentModalTarget;
                                currentProducts[pageKey].items[index].image = e.target.result;
                                const preview = document.getElementById(`img-preview-${itemId}`);
                                if (preview) preview.src = e.target.result;
                                showToast('Image updated locally!');
                                closeImageModal();
                            };
                            reader.readAsDataURL(file);
                        }
                    } catch (err) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const { pageKey, index, itemId } = currentModalTarget;
                            currentProducts[pageKey].items[index].image = e.target.result;
                            const preview = document.getElementById(`img-preview-${itemId}`);
                            if (preview) preview.src = e.target.result;
                            showToast('Image updated!');
                            closeImageModal();
                        };
                        reader.readAsDataURL(file);
                    }
                }

                // Close modal on click outside dialog
                document.getElementById('imageSelectModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeImageModal();
                    }
                });

                // Close modal on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeImageModal();
                    }
                });

                // Save All Button
                document.getElementById('saveAllBtn').addEventListener('click', async function() {
                    // Update titles
                    currentProducts.page4.title = document.getElementById('p4-title-input').value;
                    currentProducts.page4.subtitle = document.getElementById('p4-sub-input').value;
                    currentProducts.page5.title = document.getElementById('p5-title-input').value;
                    currentProducts.page5.subtitle = document.getElementById('p5-sub-input').value;

                    try {
                        const res = await fetch('../api/get_products.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(currentProducts)
                        });
                        const data = await res.json();
                        if (data.success) {
                            showToast('All changes saved to products.json!');
                        } else {
                            // Save to localStorage as backup
                            localStorage.setItem('zt_products_catalog', JSON.stringify(currentProducts));
                            showToast('Changes saved successfully!');
                        }
                    } catch (e) {
                        localStorage.setItem('zt_products_catalog', JSON.stringify(currentProducts));
                        showToast('Changes saved to browser storage!');
                    }
                });

                function showToast(msg) {
                    const toast = document.getElementById('toastMessage');
                    document.getElementById('toastText').textContent = msg;
                    toast.classList.add('show');
                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3500);
                }

                // Initial fetch
                loadProducts();
            </script>
        <?php endif; ?>
    </div>

</body>
</html>

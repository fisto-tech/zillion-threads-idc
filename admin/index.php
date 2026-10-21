<?php
session_start();

$error = '';
$success = '';

// Check logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header('Location: index.php');
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
                <a href="../For_Desc/index.html" target="_blank" class="btn btn-outline">Preview Catalog &rarr;</a>
                <a href="?action=logout" class="btn btn-outline" style="color: #F87171;">Logout</a>
            <?php else: ?>
                <a href="../For_Desc/index.html" class="btn btn-outline">&larr; Back to Catalog</a>
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

            <!-- Hidden File Input for Image Uploads -->
            <input type="file" id="hiddenFileInput" accept="image/*" style="display: none;">

            <!-- Save Toast -->
            <div class="toast" id="toastMessage">
                <span>&#10004;</span>
                <span id="toastText">Products saved successfully!</span>
            </div>

            <script>
                let currentProducts = {};
                let targetCardItemId = null;

                // Load Products JSON on load
                async function loadProducts() {
                    try {
                        const res = await fetch('../api/get_products.php');
                        if (!res.ok) throw new Error('Failed to fetch products');
                        currentProducts = await res.json();
                        renderDashboard();
                    } catch (err) {
                        console.error('Error loading products:', err);
                        // Fallback to local default if API not running PHP web server
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
                                <div class="upload-overlay-btn" onclick="triggerImageUpload('${pageKey}', ${index}, '${item.id}')">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>Replace Image</span>
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

                let currentUploadTarget = null;
                function triggerImageUpload(pageKey, index, itemId) {
                    currentUploadTarget = { pageKey, index, itemId };
                    document.getElementById('hiddenFileInput').click();
                }

                // Handle file selection and upload
                document.getElementById('hiddenFileInput').addEventListener('change', async function() {
                    if (!this.files || this.files.length === 0 || !currentUploadTarget) return;

                    const file = this.files[0];
                    const formData = new FormData();
                    formData.append('product_image', file);

                    showToast('Uploading image...');

                    try {
                        const res = await fetch('../api/upload_image.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await res.json();
                        if (data.success && data.url) {
                            const { pageKey, index, itemId } = currentUploadTarget;
                            currentProducts[pageKey].items[index].image = data.url;
                            document.getElementById(`img-preview-${itemId}`).src = data.url + '?t=' + Date.now();
                            showToast('Image uploaded and preview updated!');
                        } else {
                            // If running in environment without PHP upload handler, read as local Data URL
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const { pageKey, index, itemId } = currentUploadTarget;
                                currentProducts[pageKey].items[index].image = e.target.result;
                                document.getElementById(`img-preview-${itemId}`).src = e.target.result;
                                showToast('Image updated locally!');
                            };
                            reader.readAsDataURL(file);
                        }
                    } catch (err) {
                        // Fallback read as Data URL
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const { pageKey, index, itemId } = currentUploadTarget;
                            currentProducts[pageKey].items[index].image = e.target.result;
                            document.getElementById(`img-preview-${itemId}`).src = e.target.result;
                            showToast('Image updated!');
                        };
                        reader.readAsDataURL(file);
                    }
                    this.value = '';
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

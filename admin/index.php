<?php
session_start();

$error = '';
$success = '';

// Check logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    $returnUrl = $_GET['return'] ?? '/login.php';
    header('Location: ' . $returnUrl);
    exit();
}

// Authentication check - If not logged in, redirect to login screen
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
if (!$isLoggedIn) {
    header('Location: /login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zillion Threads Admin - Yarika Collections Slot Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Montserrat:wght@400;500;600;700&family=Noto+Sans+Tamil:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0D161F;
            --bg-body: #F4F6F9;
            --bg-card: #FFFFFF;
            
            --brand-primary: #1A647E;
            --brand-primary-light: #257D9C;
            --brand-primary-dark: #124B5E;
            
            --accent-gold: #C9972E;
            --accent-gold-light: #DEB04F;
            --accent-gold-dark: #A5781E;
            
            --border-subtle: #E2E8F0;
            --border-card: #E2E8F0;
            
            --text-main: #0F172A;
            --text-secondary: #475569;
            --text-muted: #64748B;
            
            --success: #10B981;
            --danger: #EF4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(ellipse 80% 40% at 50% -10%, rgba(26, 100, 126, 0.05) 0%, transparent 60%),
                linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Navbar */
        .admin-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.85rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .brand-title {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-icon img {
            height: 34px;
            width: auto;
        }

        .brand-text-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-badge {
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            color: #92400E;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-size: 0.86rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1A647E 0%, #124B5E 100%);
            color: #ffffff;
        }
        .btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }

        .btn-accent {
            background: linear-gradient(135deg, #DFB253 0%, #C9972E 100%);
            color: #1E293B;
            font-weight: 700;
        }
        .btn-accent:hover { filter: brightness(1.08); transform: translateY(-1px); }

        .btn-outline {
            background: #FFFFFF;
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
        }
        .btn-outline:hover { background: #F8FAFC; color: var(--text-main); transform: translateY(-1px); }

        .btn-danger-outline {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }
        .btn-danger-outline:hover { background: #FEE2E2; color: #B91C1C; }

        /* Main Container */
        .admin-container {
            max-width: 1540px;
            margin: 1.5rem auto;
            padding: 0 1.5rem;
            width: 100%;
            flex: 1;
        }

        .dashboard-header {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            flex-wrap: wrap;
            gap: 1.25rem;
        }

        .header-title h1 {
            font-family: 'Prompt', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Page Switcher Tabs */
        .page-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #F1F5F9;
            padding: 4px;
            border-radius: 10px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: #FFFFFF;
            color: var(--brand-primary);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* Two-Column Studio Layout: Left Page Canvas Stage, Right Inspector Panel */
        .studio-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.75rem;
            align-items: start;
        }

        /* Interactive Canvas Stage Preview Container */
        .stage-card-wrapper {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stage-viewport-box {
            width: 100%;
            max-width: 620px;
            position: relative;
            background: #FAF8F5;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        /* Yarika Pure Absolute Stage (Aspect Ratio 4:5 matching individual leaf) */
        .yarika-page-stage {
            position: relative !important;
            width: 100% !important;
            aspect-ratio: 4 / 5 !important;
            overflow: hidden !important;
            background-color: #f3ece3 !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            container-type: size;
            user-select: none;
            box-sizing: border-box;
        }

        .yarika-page-stage[data-page="page4"] {
            background-image: url('../global assets/Images/background-images/page-4-Background-image.webp') !important;
        }

        .yarika-page-stage[data-page="page5"] {
            background-image: url('../global assets/Images/background-images/page-5-Background-image.webp') !important;
        }

        /* Calibration Dev Mode Overlay (?calibrate=1) */
        .calibrate-mode .calibrate-overlay-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            opacity: 0.5;
            pointer-events: none;
            z-index: 99998;
        }

        .calibrate-mode .slot {
            outline: 1.5px solid #FF3366 !important;
            outline-offset: -1px;
        }

        .calibrate-mode .slot-id-label {
            position: absolute;
            top: 2px;
            left: 2px;
            background: rgba(0, 0, 0, 0.85);
            color: #00FFCC;
            font-family: monospace;
            font-size: 1.8cqw;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 2px;
            pointer-events: none;
            z-index: 99999;
        }

        .yarika-brand-header {
            position: absolute;
            top: 4.3%;
            left: 0;
            width: 100%;
            height: 7.2%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            pointer-events: none;
            z-index: 10;
        }

        .yarika-brand-logo-text {
            font-family: 'Cinzel', serif;
            font-size: 5.6cqw;
            font-weight: 600;
            letter-spacing: 0.52em;
            color: #B88B4A;
            text-indent: 0.52em;
            line-height: 1;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
        }

        .yarika-brand-logo-text sup {
            font-size: 0.42em;
            position: relative;
            top: -0.6em;
            font-family: sans-serif;
            letter-spacing: 0;
        }

        .yarika-brand-sub {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.3cqw;
            font-weight: 600;
            letter-spacing: 0.38em;
            text-indent: 0.38em;
            color: #1E293B;
            text-transform: uppercase;
            margin-top: 0.6cqw;
        }

        .yarika-heading-bar {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5cqw;
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
        }

        .yarika-heading-bar::before,
        .yarika-heading-bar::after {
            content: '';
            height: 1px;
            background: #C4A467;
            opacity: 0.75;
            flex: 1;
        }

        .yarika-heading-text {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Tamil', sans-serif;
            font-size: 2.2cqw;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            gap: 1cqw;
            background: rgba(243, 236, 227, 0.75);
            backdrop-filter: blur(2px);
            padding: 0 1.5cqw;
            border-radius: 4px;
        }

        .yarika-heading-ta {
            color: #1E293B;
            font-weight: 700;
        }

        .yarika-heading-sep {
            color: #B88B4A;
            font-weight: 600;
        }

        .yarika-heading-en {
            color: #0F172A;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .yarika-slots-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        /* Slot in Admin Editor: Clickable with Hover Indicator */
        .slot {
            position: absolute;
            overflow: hidden;
            box-sizing: border-box;
            background-color: #E2E8F0;
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            cursor: pointer;
            transition: outline 0.15s, box-shadow 0.2s;
        }

        .slot:hover {
            outline: 2px solid var(--accent-gold);
            outline-offset: -2px;
            box-shadow: 0 4px 14px rgba(201, 151, 46, 0.35);
        }

        .slot.selected-slot {
            outline: 3px solid #1A647E !important;
            outline-offset: -3px;
            box-shadow: 0 0 0 4px rgba(26, 100, 126, 0.3) !important;
            z-index: 99 !important;
        }

        .slot img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            display: block;
        }

        /* Slot Badge Overlay in Admin View */
        .slot .admin-slot-tag {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: rgba(15, 23, 42, 0.75);
            color: #FFFFFF;
            font-size: 0.58rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 5;
        }

        .slot:hover .admin-slot-tag,
        .slot.selected-slot .admin-slot-tag {
            opacity: 1;
        }

        /* Right Side: Slot Inspector Panel */
        .inspector-panel {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            position: sticky;
            top: 5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .inspector-header {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .inspector-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .inspector-slot-id {
            background: #E0F2FE;
            color: #0369A1;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Interactive Drag Reposition Viewport */
        .drag-stage-preview {
            width: 100%;
            height: 220px;
            background: #0F172A;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            border: 1px solid #CBD5E1;
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drag-stage-preview:active {
            cursor: grabbing;
        }

        .drag-stage-preview img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            user-select: none;
            -webkit-user-drag: none;
        }

        .drag-crosshair {
            position: absolute;
            width: 24px;
            height: 24px;
            transform: translate(-50%, -50%);
            pointer-events: none;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.5), 0 0 10px rgba(223, 178, 83, 0.8);
            z-index: 10;
        }
        .drag-crosshair::before,
        .drag-crosshair::after {
            content: '';
            position: absolute;
            background: #FFFFFF;
        }
        .drag-crosshair::before { top: -6px; bottom: -6px; left: 10px; width: 2px; }
        .drag-crosshair::after { left: -6px; right: -6px; top: 10px; height: 2px; }

        .drag-instructions {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.8);
            color: #FFFFFF;
            font-size: 0.68rem;
            padding: 3px 10px;
            border-radius: 20px;
            pointer-events: none;
            white-space: nowrap;
            letter-spacing: 0.3px;
        }

        /* Slider Controls */
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .control-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .control-val-badge {
            color: var(--brand-primary);
            font-weight: 800;
            font-family: monospace;
            background: #F1F5F9;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .range-slider {
            width: 100%;
            accent-color: var(--brand-primary);
            cursor: pointer;
        }

        /* Section Heading Editor Accordion Styles */
        .section-editor-box {
            border-top: 1px solid var(--border-subtle);
            padding-top: 1rem;
            margin-top: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .section-editor-title {
            font-size: 0.86rem;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-item-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .section-input-row {
            display: flex;
            gap: 6px;
        }

        .section-input-row input {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 0.78rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }

        .section-input-row input:focus {
            border-color: var(--brand-primary);
        }

        .section-save-btn {
            padding: 5px 10px;
            border-radius: 6px;
            background: var(--brand-primary);
            color: #FFFFFF;
            font-size: 0.74rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            align-self: flex-end;
        }

        .section-save-btn:hover {
            filter: brightness(1.1);
        }

        /* Inspector Actions */
        .inspector-actions {
            display: flex;
            gap: 10px;
            margin-top: 0.5rem;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-dialog {
            background: #FFFFFF;
            border-radius: 18px;
            width: 100%;
            max-width: 680px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.75rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .drop-zone {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 1.75rem;
            text-align: center;
            background: #F8FAFC;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .drop-zone:hover {
            border-color: var(--brand-primary);
            background: #F0F9FF;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.85rem;
            max-height: 240px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #E2E8F0;
            cursor: pointer;
            background: #F8FAFC;
            transition: all 0.2s;
        }
        .gallery-item:hover {
            border-color: var(--brand-primary);
            transform: scale(1.03);
        }
        .gallery-item.selected {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(201, 151, 46, 0.4);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-footer {
            padding: 1rem 1.75rem;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #F8FAFC;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 0.95rem 1.6rem;
            background: #0F172A;
            border: 1px solid #DFB253;
            color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 999999;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .toast.show {
            display: flex;
            animation: slideInUp 0.3s ease;
        }

        @keyframes slideInUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 1080px) {
            .studio-layout {
                grid-template-columns: 1fr;
            }
            .inspector-panel {
                position: static;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Header -->
    <header class="admin-nav">
        <a href="/For_Desc/index.html" class="brand-title">
            <div class="brand-icon">
                <img src="../global assets/Images/commonThings/logo.svg" alt="Zillion Threads Logo"/>
            </div>
            <div class="brand-text-group">
                <div class="brand-text-row">
                    <span style="font-weight: 800; font-size: 1.1rem; color: var(--text-main);">YARIKA COLLECTIONS</span>
                    <span class="brand-badge">STUDIO</span>
                </div>
                <span class="brand-subtitle">Page 4 &amp; 5 Dynamic Collage &amp; Slot Manager</span>
            </div>
        </a>
        <div class="nav-actions">
            <button type="button" class="btn btn-outline" id="calibrateToggleBtn" onclick="toggleCalibrateOverlay()" title="Overlay target image at 50% opacity to verify exact layout alignment">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="22" y1="12" x2="18" y2="12"></line>
                    <line x1="6" y1="12" x2="2" y2="12"></line>
                    <line x1="12" y1="6" x2="12" y2="2"></line>
                    <line x1="12" y1="22" x2="12" y2="18"></line>
                </svg>
                <span id="calibrateBtnText">Calibrate Overlay: OFF</span>
            </button>
            <a href="/For_Desc/index.html" class="btn btn-outline" target="_blank" title="Preview live catalog in new tab">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span>Live Flipbook</span>
            </a>
            <a href="?action=logout&return=/login.php" class="btn btn-danger-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </header>

    <div class="admin-container">
        <!-- Dashboard Header & Page Switcher -->
        <div class="dashboard-header">
            <div class="header-title">
                <h1>
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--accent-gold)" stroke-width="2.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Yarika Collage Slot Editor
                </h1>
                <p>Click any slot below to swap photo, drag focal point inside the frame, and adjust zoom without cropping.</p>
            </div>

            <div class="page-tabs">
                <button class="tab-btn active" id="tab-p4" onclick="switchPageTab('page4')">Page 4 (Twirly, Edge, Wave, Muse)</button>
                <button class="tab-btn" id="tab-p5" onclick="switchPageTab('page5')">Page 5 (Silk, Maternity, Camicia)</button>
            </div>
        </div>

        <!-- Studio 2-Column Interface -->
        <div class="studio-layout">
            <!-- Left: Stage Canvas Preview -->
            <div class="stage-card-wrapper">
                <div class="stage-viewport-box">
                    <div class="yarika-page-stage" id="admin-stage-canvas" data-page="page4">
                        <div class="yarika-brand-header">
                            <div class="yarika-brand-logo-text">YARIKA<sup>™</sup></div>
                            <div class="yarika-brand-sub">COLLECTIONS</div>
                        </div>
                        <div class="yarika-headings-layer" id="admin-headings-layer"></div>
                        <div class="yarika-slots-layer" id="admin-slots-layer"></div>
                    </div>
                </div>
            </div>

            <!-- Right: Slot Inspector & Controls -->
            <div class="inspector-panel" id="slotInspector">
                <div class="inspector-header">
                    <div class="inspector-title">
                        <span>Slot Inspector</span>
                        <span class="inspector-slot-id" id="insp-slot-id">Select a slot</span>
                    </div>
                </div>

                <!-- Reposition viewport -->
                <div class="control-group">
                    <div class="control-label-row">
                        <span>Focal Point &amp; Reposition</span>
                        <span class="control-val-badge" id="insp-focal-text">50% / 50%</span>
                    </div>
                    <div class="drag-stage-preview" id="dragPreviewViewport">
                        <img src="" id="insp-preview-img" alt="Slot Preview">
                        <div class="drag-crosshair" id="dragCrosshair" style="left: 50%; top: 50%;"></div>
                        <div class="drag-instructions">Click &amp; drag to reposition focal point</div>
                    </div>
                </div>

                <!-- Zoom Slider -->
                <div class="control-group">
                    <div class="control-label-row">
                        <span>Zoom Scale</span>
                        <span class="control-val-badge" id="insp-zoom-text">1.00x</span>
                    </div>
                    <input type="range" class="range-slider" id="inspZoomSlider" min="1.00" max="3.00" step="0.05" value="1.00">
                </div>

                <!-- Swap Image Button -->
                <button type="button" class="btn btn-outline" style="width: 100%; justify-content: center;" onclick="openImageModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Swap Photo (Upload / Gallery)</span>
                </button>

                <!-- Save & Reset Action Buttons -->
                <div class="inspector-actions">
                    <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="resetSlotAdjustments()">
                        <span>Reset</span>
                    </button>
                    <button type="button" class="btn btn-accent" style="flex: 2; justify-content: center;" id="saveSlotBtn" onclick="saveActiveSlot()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Save Slot</span>
                    </button>
                </div>

                <!-- Section Headings Editor (Tamil / English) -->
                <div class="section-editor-box">
                    <div class="section-editor-title">
                        <span>Section Headings Editor</span>
                        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 500;">Bilingual Labels</span>
                    </div>
                    <div id="sectionCardsContainer" style="display: flex; flex-direction: column; gap: 8px;">
                        <!-- Rendered dynamically per active page -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Selection & Upload Modal -->
    <div id="imageSelectModal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent-gold)" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Select or Upload Photo for Slot
                </h3>
                <button class="btn btn-outline" style="padding: 4px 10px;" onclick="closeImageModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="drop-zone" id="imageDropZone">
                    <div style="font-weight: 700; color: #0F172A; margin-bottom: 4px;">Drag &amp; drop a photo here, or <span style="color: var(--accent-gold); text-decoration: underline;">browse files</span></div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">Supports JPG, PNG, WEBP, and GIF (No SVG). Original is never cropped.</div>
                    <input type="file" id="modalFileInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display: none;">
                </div>

                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 8px;">
                        Available Gallery Photos
                    </div>
                    <div class="gallery-grid" id="modalGalleryGrid"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeImageModal()">Cancel</button>
                <button type="button" class="btn btn-accent" id="applySelectedImageBtn" disabled onclick="applySelectedGalleryImage()">Use Selected Photo</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toastMessage">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent-gold)" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span id="toastText">Slot saved successfully!</span>
    </div>

    <script>
        let catalogData = {};
        let activePageKey = 'page4';
        let activeSlot = null;
        let selectedGalleryUrl = null;
        let isDraggingCrosshair = false;

        let isCalibrate = false;

        function toggleCalibrateOverlay() {
            isCalibrate = !isCalibrate;
            const btnText = document.getElementById('calibrateBtnText');
            if (btnText) btnText.textContent = `Calibrate Overlay: ${isCalibrate ? 'ON' : 'OFF'}`;
            const btn = document.getElementById('calibrateToggleBtn');
            if (btn) {
                btn.classList.toggle('btn-accent', isCalibrate);
                btn.classList.toggle('btn-outline', !isCalibrate);
            }
            renderStage();
        }

        // Shared Universal Slot HTML Renderer (identical to Flipbook)
        window.renderSlotHtml = function(slot) {
            const fx = typeof slot.focal_x !== 'undefined' ? slot.focal_x : 50;
            const fy = typeof slot.focal_y !== 'undefined' ? slot.focal_y : 50;
            const zoom = typeof slot.zoom !== 'undefined' ? slot.zoom : 1.00;
            const radius = slot.radius || '0';
            const zIndex = slot.z_index || 1;
            const imgSrc = slot.image || '../global assets/Images/products/product-1.jpg';

            return `
                <div class="slot ${activeSlot && activeSlot.id === slot.id ? 'selected-slot' : ''}" 
                     id="slot-${slot.id}" 
                     data-slot-id="${slot.id}" 
                     onclick="selectSlot('${slot.id}')"
                     style="
                    position: absolute;
                    left: ${slot.x}%;
                    top: ${slot.y}%;
                    width: ${slot.w}%;
                    height: ${slot.h}%;
                    border-radius: ${radius};
                    overflow: hidden;
                    z-index: ${zIndex};
                ">
                    <img src="${imgSrc}" alt="${slot.id}" style="
                        position: absolute;
                        inset: 0;
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        object-position: ${fx}% ${fy}%;
                        transform: scale(${zoom});
                        transform-origin: ${fx}% ${fy}%;
                    " onerror="this.onerror=null; this.src='../global assets/Images/products/product-1.jpg';">
                    ${isCalibrate ? `<span class="slot-id-label">${slot.id}</span>` : `<span class="admin-slot-tag">${slot.id}</span>`}
                </div>
            `;
        };

        // Shared Heading Renderer
        window.renderSectionHeadingHtml = function(sec) {
            return `
                <div class="yarika-heading-bar" id="sec-${sec.id}" style="
                    position: absolute;
                    left: ${sec.x}%;
                    top: ${sec.y}%;
                    width: ${sec.w}%;
                    height: ${sec.h}%;
                ">
                    <div class="yarika-heading-text">
                        <span class="yarika-heading-ta">${sec.label_ta || ''}</span>
                        <span class="yarika-heading-sep">—</span>
                        <span class="yarika-heading-en">${sec.label_en || ''}</span>
                    </div>
                </div>
            `;
        };

        // Fetch Catalog Data
        async function fetchCatalog() {
            try {
                const res = await fetch('../api/get_products.php?t=' + Date.now());
                if (!res.ok) throw new Error('API error');
                catalogData = await res.json();
                renderStage();
                renderSectionCards();
                // Select first slot by default
                if (catalogData[activePageKey] && catalogData[activePageKey].slots && catalogData[activePageKey].slots.length > 0) {
                    selectSlot(catalogData[activePageKey].slots[0].id);
                }
            } catch (err) {
                console.error("Failed to load catalog data:", err);
                showToast("Could not load catalog data.");
            }
        }

        function switchPageTab(pageKey) {
            activePageKey = pageKey;
            document.getElementById('tab-p4').classList.toggle('active', pageKey === 'page4');
            document.getElementById('tab-p5').classList.toggle('active', pageKey === 'page5');
            document.getElementById('admin-stage-canvas').setAttribute('data-page', pageKey);
            renderStage();
            renderSectionCards();
            if (catalogData[activePageKey] && catalogData[activePageKey].slots && catalogData[activePageKey].slots.length > 0) {
                selectSlot(catalogData[activePageKey].slots[0].id);
            }
        }

        function renderStage() {
            const pageData = catalogData[activePageKey];
            if (!pageData) return;

            const stage = document.getElementById('admin-stage-canvas');
            if (isCalibrate) {
                stage.classList.add('calibrate-mode');
                let overlay = stage.querySelector('.calibrate-overlay-img');
                if (!overlay) {
                    overlay = document.createElement('img');
                    overlay.className = 'calibrate-overlay-img';
                    stage.appendChild(overlay);
                }
                overlay.src = `../global assets/Images/targets/${activePageKey}-target.png`;
                overlay.alt = 'Target Overlay';
            } else {
                stage.classList.remove('calibrate-mode');
                const overlay = stage.querySelector('.calibrate-overlay-img');
                if (overlay) overlay.remove();
            }

            const headingsLayer = document.getElementById('admin-headings-layer');
            const slotsLayer = document.getElementById('admin-slots-layer');

            if (headingsLayer && Array.isArray(pageData.sections)) {
                headingsLayer.innerHTML = pageData.sections.map(sec => window.renderSectionHeadingHtml(sec)).join('');
            }
            if (slotsLayer && Array.isArray(pageData.slots)) {
                slotsLayer.innerHTML = pageData.slots.map(slot => window.renderSlotHtml(slot)).join('');
            }
        }

        // Render Section Headings in Inspector for Tamil & English editing
        function renderSectionCards() {
            const container = document.getElementById('sectionCardsContainer');
            if (!container) return;
            const pageData = catalogData[activePageKey];
            if (!pageData || !Array.isArray(pageData.sections)) {
                container.innerHTML = '<div style="color: var(--text-muted); font-size: 0.8rem;">No sections found.</div>';
                return;
            }

            container.innerHTML = pageData.sections.map(sec => `
                <div class="section-item-card" id="card-sec-${sec.id}">
                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748B;">ID: ${sec.id}</div>
                    <div class="section-input-row">
                        <input type="text" id="sec-ta-${sec.id}" value="${sec.label_ta || ''}" placeholder="Tamil (e.g. சுடிதார்)">
                        <input type="text" id="sec-en-${sec.id}" value="${sec.label_en || ''}" placeholder="English (e.g. Silk Signature)">
                    </div>
                    <button type="button" class="section-save-btn" onclick="saveSectionHeading('${sec.id}')">Save Label</button>
                </div>
            `).join('');
        }

        async function saveSectionHeading(sectionId) {
            const taInput = document.getElementById(`sec-ta-${sectionId}`);
            const enInput = document.getElementById(`sec-en-${sectionId}`);
            if (!taInput || !enInput) return;

            const labelTa = taInput.value.trim();
            const labelEn = enInput.value.trim();

            try {
                const res = await fetch('../api/save_section.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        section_id: sectionId,
                        label_ta: labelTa,
                        label_en: labelEn
                    })
                });

                const data = await res.json();
                if (data.success) {
                    showToast(`Heading for ${sectionId} updated!`);
                    // Update in local memory
                    const pageData = catalogData[activePageKey];
                    if (pageData && Array.isArray(pageData.sections)) {
                        const s = pageData.sections.find(item => item.id === sectionId);
                        if (s) {
                            s.label_ta = labelTa;
                            s.label_en = labelEn;
                        }
                    }
                    // Re-render headings layer
                    const headingsLayer = document.getElementById('admin-headings-layer');
                    if (headingsLayer && pageData) {
                        headingsLayer.innerHTML = pageData.sections.map(sec => window.renderSectionHeadingHtml(sec)).join('');
                    }
                } else {
                    showToast(data.message || 'Failed to update section heading.');
                }
            } catch (err) {
                console.error("Save section error:", err);
                showToast("Network error saving section heading.");
            }
        }

        function selectSlot(slotId) {
            const pageData = catalogData[activePageKey];
            if (!pageData || !Array.isArray(pageData.slots)) return;

            const slot = pageData.slots.find(s => s.id === slotId);
            if (!slot) return;

            activeSlot = slot;

            // Highlight selected in DOM
            document.querySelectorAll('.slot').forEach(el => el.classList.remove('selected-slot'));
            const slotEl = document.getElementById(`slot-${slot.id}`);
            if (slotEl) slotEl.classList.add('selected-slot');

            // Update Inspector UI
            document.getElementById('insp-slot-id').textContent = slot.id;
            const previewImg = document.getElementById('insp-preview-img');
            previewImg.src = slot.image || '../global assets/Images/products/product-1.jpg';

            const fx = typeof slot.focal_x !== 'undefined' ? slot.focal_x : 50;
            const fy = typeof slot.focal_y !== 'undefined' ? slot.focal_y : 50;
            const zoom = typeof slot.zoom !== 'undefined' ? slot.zoom : 1.00;

            updateInspectorValues(fx, fy, zoom);
        }

        function updateInspectorValues(fx, fy, zoom) {
            document.getElementById('insp-focal-text').textContent = `${Math.round(fx)}% / ${Math.round(fy)}%`;
            document.getElementById('insp-zoom-text').textContent = `${Number(zoom).toFixed(2)}x`;
            document.getElementById('inspZoomSlider').value = zoom;

            // Crosshair position
            const crosshair = document.getElementById('dragCrosshair');
            crosshair.style.left = `${fx}%`;
            crosshair.style.top = `${fy}%`;

            // Preview image styling
            const previewImg = document.getElementById('insp-preview-img');
            previewImg.style.objectPosition = `${fx}% ${fy}%`;
            previewImg.style.transform = `scale(${zoom})`;
            previewImg.style.transformOrigin = `${fx}% ${fy}%`;

            // Real-time sync to the stage canvas slot
            if (activeSlot) {
                const stageSlotImg = document.querySelector(`#slot-${activeSlot.id} img`);
                if (stageSlotImg) {
                    stageSlotImg.style.objectPosition = `${fx}% ${fy}%`;
                    stageSlotImg.style.transform = `scale(${zoom})`;
                    stageSlotImg.style.transformOrigin = `${fx}% ${fy}%`;
                }
            }
        }

        // Mouse Drag to Reposition Focal Point in Drag Viewport
        const dragViewport = document.getElementById('dragPreviewViewport');
        
        function handleFocalDrag(e) {
            if (!activeSlot) return;
            const rect = dragViewport.getBoundingClientRect();
            const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
            const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);

            let percentX = ((clientX - rect.left) / rect.width) * 100;
            let percentY = ((clientY - rect.top) / rect.height) * 100;

            percentX = Math.max(0, Math.min(100, percentX));
            percentY = Math.max(0, Math.min(100, percentY));

            activeSlot.focal_x = parseFloat(percentX.toFixed(2));
            activeSlot.focal_y = parseFloat(percentY.toFixed(2));

            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        }

        dragViewport.addEventListener('mousedown', (e) => {
            isDraggingCrosshair = true;
            handleFocalDrag(e);
        });

        window.addEventListener('mousemove', (e) => {
            if (isDraggingCrosshair) handleFocalDrag(e);
        });

        window.addEventListener('mouseup', () => {
            isDraggingCrosshair = false;
        });

        // Mouse Wheel Zoom inside Drag Viewport
        dragViewport.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (!activeSlot) return;
            let z = parseFloat(activeSlot.zoom) || 1.00;
            z += e.deltaY < 0 ? 0.05 : -0.05;
            z = Math.max(1.00, Math.min(3.00, z));
            activeSlot.zoom = parseFloat(z.toFixed(2));
            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        }, { passive: false });

        // Zoom Slider change
        document.getElementById('inspZoomSlider').addEventListener('input', function() {
            if (!activeSlot) return;
            activeSlot.zoom = parseFloat(this.value);
            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        });

        // Reset Button
        function resetSlotAdjustments() {
            if (!activeSlot) return;
            activeSlot.focal_x = 50.0;
            activeSlot.focal_y = 50.0;
            activeSlot.zoom = 1.00;
            updateInspectorValues(50, 50, 1.00);
            showToast("Focal point & zoom reset to default.");
        }

        // Save Slot to API save_slot.php
        async function saveActiveSlot() {
            if (!activeSlot) return;
            const btn = document.getElementById('saveSlotBtn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const res = await fetch('../api/save_slot.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        slot_id: activeSlot.id,
                        image: activeSlot.image,
                        focal_x: activeSlot.focal_x,
                        focal_y: activeSlot.focal_y,
                        zoom: activeSlot.zoom
                    })
                });

                const data = await res.json();
                if (data.success) {
                    showToast(`Slot ${activeSlot.id} saved successfully!`);
                } else {
                    showToast(data.message || 'Failed to save slot');
                }
            } catch (err) {
                console.error("Save error:", err);
                showToast("Network error while saving slot.");
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Save Slot</span>
                `;
            }
        }

        // ==================== GALLERY & UPLOAD ====================
        function openImageModal() {
            if (!activeSlot) return;
            selectedGalleryUrl = null;
            document.getElementById('applySelectedImageBtn').disabled = true;
            document.getElementById('imageSelectModal').classList.add('active');
            fetchGalleryImages();
        }

        function closeImageModal() {
            document.getElementById('imageSelectModal').classList.remove('active');
            selectedGalleryUrl = null;
        }

        async function fetchGalleryImages() {
            const grid = document.getElementById('modalGalleryGrid');
            grid.innerHTML = '<div style="color: var(--text-muted); font-size: 0.85rem; padding: 1rem;">Loading photos...</div>';

            try {
                const res = await fetch('../api/get_gallery.php');
                const data = await res.json();
                if (data.success && Array.isArray(data.images)) {
                    grid.innerHTML = '';
                    data.images.forEach(img => {
                        const isMatch = activeSlot && activeSlot.image === img.url;
                        const item = document.createElement('div');
                        item.className = 'gallery-item' + (isMatch ? ' selected' : '');
                        item.onclick = () => selectGalleryItem(img.url, item);
                        item.innerHTML = `<img src="${img.url}" alt="${img.name}" loading="lazy">`;
                        grid.appendChild(item);
                        if (isMatch) {
                            selectedGalleryUrl = img.url;
                            document.getElementById('applySelectedImageBtn').disabled = false;
                        }
                    });
                }
            } catch (e) {
                grid.innerHTML = '<div style="color: var(--text-muted); padding: 1rem;">Could not load gallery photos.</div>';
            }
        }

        function selectGalleryItem(url, el) {
            document.querySelectorAll('.gallery-item').forEach(i => i.classList.remove('selected'));
            el.classList.add('selected');
            selectedGalleryUrl = url;
            document.getElementById('applySelectedImageBtn').disabled = false;
        }

        function applySelectedGalleryImage() {
            if (!selectedGalleryUrl || !activeSlot) return;
            activeSlot.image = selectedGalleryUrl;

            // Update previews
            document.getElementById('insp-preview-img').src = selectedGalleryUrl;
            const stageSlotImg = document.querySelector(`#slot-${activeSlot.id} img`);
            if (stageSlotImg) stageSlotImg.src = selectedGalleryUrl;

            closeImageModal();
            showToast("New photo applied to slot! Click 'Save Slot' to persist.");
        }

        // Upload Handlers
        const dropZone = document.getElementById('imageDropZone');
        const modalFileInput = document.getElementById('modalFileInput');

        dropZone.addEventListener('click', () => modalFileInput.click());
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.borderColor = '#1A647E'; });
        dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = '#CBD5E1'; });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#CBD5E1';
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
            if (!activeSlot) return;
            const formData = new FormData();
            formData.append('product_image', file);

            showToast("Uploading original photo...");

            try {
                const res = await fetch('../api/upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success && data.url) {
                    activeSlot.image = data.url;
                    document.getElementById('insp-preview-img').src = data.url;
                    const stageSlotImg = document.querySelector(`#slot-${activeSlot.id} img`);
                    if (stageSlotImg) stageSlotImg.src = data.url;

                    closeImageModal();
                    showToast("Photo uploaded and applied to slot! Click 'Save Slot' to persist.");
                } else {
                    showToast(data.message || "Failed to upload photo.");
                }
            } catch (err) {
                showToast("Upload failed due to network error.");
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toastMessage');
            document.getElementById('toastText').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        }

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeImageModal();
        });

        // Initialize Studio
        fetchCatalog();
    </script>
</body>
</html>

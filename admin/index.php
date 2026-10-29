<?php
session_start();

$error = '';
$success = '';

// Check logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    $returnUrl = $_GET['return'] ?? '../login.php';
    header('Location: ' . $returnUrl);
    exit();
}

// Authentication check - If not logged in, redirect to login screen
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
if (!$isLoggedIn) {
    header('Location: ../login.php');
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

        /* Modern Theme Scrollbar Styling (Teal & Gold accents) */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
            border: 2px solid #F1F5F9;
            transition: background 0.2s;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
        }

        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            padding: 0.5rem 2rem;
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
            height: 30px;
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
            gap: 0.65rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.2s ease;
            width: fit-content;
            max-width: 100%;
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
            margin: 0 auto;
            padding: 0 1.5rem 0.75rem 1.5rem;
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        .dashboard-header {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: none;
            border-bottom: 1px solid var(--border-subtle);
            border-radius: 0;
            padding: 0.5rem 0;
            margin-bottom: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            flex-wrap: wrap;
            gap: 0.85rem;
            flex-shrink: 0;
        }

        .header-title h1 {
            font-family: 'Prompt', sans-serif;
            font-size: 1.2rem;
            font-weight: 500;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title p {
            font-size: 0.80rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Page Switcher Tabs */
        .page-tabs {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #E2E8F0;
            padding: 3px;
            border-radius: 9px;
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.55rem 1.15rem;
            border-radius: 7px;
            font-weight: 600;
            font-size: 0.86rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            min-height: auto;
            line-height: 1.25;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .tab-btn.active {
            background: #FFFFFF;
            color: var(--brand-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            font-weight: 700;
            transform: translateY(0);
        }

        /* 3-Column Studio Layout: Column 1 = Stage Canvas Preview (IDC), Column 2 = Section Headings Editor, Column 3 = Photo Adjustment & Action Buttons */
        .studio-layout {
            display: grid;
            grid-template-columns:minmax(420px, 1fr) minmax(320px, 0.75fr) minmax(268px, 399px);
            gap: 1.15rem;
            align-items: stretch;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding-bottom: 0.5rem;
        }

        /* Interactive Canvas Stage Preview Container (Separately Scrollable on Left) */
        .stage-card-wrapper {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1rem 1rem 2rem 1rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            max-height: calc(100vh - 145px);
            min-height: 0;
            overflow-y: scroll !important;
            overflow-x: hidden;
            scrollbar-gutter: stable;
            scrollbar-width: thin;
            scrollbar-color: #94A3B8 #F1F5F9;
        }

        .stage-card-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        .stage-card-wrapper::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 8px;
            margin: 8px 0;
        }
        .stage-card-wrapper::-webkit-scrollbar-thumb {
            background: #94A3B8;
            border-radius: 8px;
            border: 2px solid #F1F5F9;
        }
        .stage-card-wrapper::-webkit-scrollbar-thumb:hover {
            background: #1A647E;
        }

        .stage-viewport-box {
            width: 100%;
            max-width: 680px;
            flex-shrink: 0;
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
            outline: 3px solid #0284C7 !important;
            outline-offset: -2px;
            box-shadow: 0 0 0 3px #FFFFFF, 0 0 0 6px #0284C7, 0 0 25px rgba(2, 132, 199, 0.65) !important;
            z-index: 99 !important;
            cursor: grab !important;
            user-select: none;
            -webkit-user-select: none;
            touch-action: none;
            animation: slotSelectedPulse 1.8s infinite ease-in-out;
        }

        @keyframes slotSelectedPulse {
            0%, 100% {
                box-shadow: 0 0 0 3px #FFFFFF, 0 0 0 6px #0284C7, 0 0 25px rgba(2, 132, 199, 0.65);
            }
            50% {
                box-shadow: 0 0 0 3px #FFFFFF, 0 0 0 7px #DFB253, 0 0 30px rgba(223, 178, 83, 0.85);
            }
        }

        .slot.selected-slot:active {
            cursor: grabbing !important;
        }

        .slot img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Slot Badge Overlay in Admin View (Hidden for clean visual display) */
        .slot .admin-slot-tag {
            display: none !important;
        }

        /* Right Side: Slot Inspector Panel (Fits desktop without scrolling) */
        .inspector-panel {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            height: 100%;
            max-height: calc(100vh - 145px);
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 1081px) {
            .inspector-panel {
                overflow: hidden !important; /* Zero scroll in desktop screen */
            }
        }

        .inspector-panel::-webkit-scrollbar {
            width: 6px;
        }
        .inspector-panel::-webkit-scrollbar-track {
            background: #F8FAFC;
            border-radius: 6px;
        }
        .inspector-panel::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 6px;
        }
        .inspector-panel::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
        }

        .inspector-header {
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .inspector-title {
            font-size: 0.96rem;
            font-weight: 800;
            color: #0F172A;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .inspector-slot-id {
            background: #E0F2FE;
            color: #0369A1;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 6px;
        }

        /* ==================== 3-COLUMN STUDIO LAYOUT SPECIFICS ==================== */
        
        /* COLUMN 2: Section Headings Editor Panel */
        .headings-editor-panel {
            background: #FFFFFF;
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1rem 1.15rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            height: 100%;
            max-height: calc(100vh - 145px);
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .headings-editor-panel::-webkit-scrollbar {
            width: 6px;
        }
        .headings-editor-panel::-webkit-scrollbar-track {
            background: #F8FAFC;
            border-radius: 6px;
        }
        .headings-editor-panel::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 6px;
        }
        .headings-editor-panel::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
        }

        .section-cards-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            overflow-y: visible;
        }

        .section-item-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 6px 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            box-sizing: border-box;
            width: 100%;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            transition: border-color 0.2s;
        }

        .section-item-card:hover {
            border-color: #CBD5E1;
        }

        .section-input-row {
            display: flex;
            gap: 6px;
            width: 100%;
            box-sizing: border-box;
        }

        .section-input-row input {
            flex: 1;
            min-width: 0;
            width: 100%;
            padding: 13px 8px;
            border: 1px solid #CBD5E1;
            border-radius: 5px;
            font-size: 0.75rem;
            font-family: inherit;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
            background: #FFFFFF;
        }

        .section-input-row input:focus {
            border-color: var(--brand-primary);
        }

        .section-save-btn {
            padding: 3px 9px;
            border-radius: 5px;
            background: var(--brand-primary);
            color: #FFFFFF;
            font-size: 0.70rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            align-self: flex-end;
        }

        .section-save-btn:hover {
            filter: brightness(1.1);
        }

        /* COLUMN 3: Photo row (Framing Preview + Dashed Upload Card) */
        .inspector-photo-row {
            display: grid;
            grid-template-columns: minmax(130px, 160px) 1fr;
            gap: 0.75rem;
            align-items: stretch;
            flex: 1;
            min-height: 0;
        }

        .inspector-preview-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 0;
        }

        .inspector-upload-col {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 0;
        }

        /* Dashed Swap / Upload Photo Button in Column 3 */
        .btn-dashed-upload {
            width: 100%;
            padding: 8px 12px;
            min-height: 48px;
            background: #F8FAFC;
            border: 2px dashed #94A3B8;
            border-radius: 9px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            cursor: pointer;
            color: #1E293B;
            font-weight: 700;
            font-size: 0.80rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
            text-align: center;
        }

        .btn-dashed-upload:hover {
            background: #EFF6FF;
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 100, 126, 0.10);
        }

        .btn-dashed-upload svg {
            stroke: currentColor;
            transition: transform 0.2s ease;
        }

        .btn-dashed-upload:hover svg {
            transform: scale(1.08);
        }

        /* 2x2 Action Buttons Grid in Right Column (Undo/Redo on Top, Reset/Save on Bottom) */
        .inspector-action-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: repeat(3, minmax(0, 1fr));
            gap: 50px;
            width: 100%;
            flex: 1;
            align-items: stretch;
            min-height: 95px;
        }

        .inspector-action-grid-2x2 .btn {
            width: 100% !important;
            height: 100% !important;
            min-height: 42px !important;
            max-height: none !important;
            padding: 8px 12px !important;
            font-size: 0.82rem;
            font-weight: 700;
            justify-content: center;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-sizing: border-box;
            border-radius: 8px;
            margin: 0;
        }

        .btn-publish-idc {
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
            color: #FFFFFF;
            border: 1px solid #047857;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease;
        }

        .btn-publish-idc:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }

        /* Interactive Drag Reposition Viewport */
        .drag-stage-preview {
            width: 100%;
            flex: 1;
            min-height: 140px;
            max-height: 180px;
            background: #F8FAFC;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            border: 2px dashed #CBD5E1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            background-image: radial-gradient(#E2E8F0 1.5px, transparent 1.5px);
            background-size: 12px 12px;
            box-sizing: border-box;
        }

        .drag-preview-frame {
            position: relative;
            overflow: hidden;
            background: #0F172A;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(0, 0, 0, 0.08);
            cursor: grab;
            user-select: none;
            -webkit-user-select: none;
            touch-action: none;
            transition: border-radius 0.2s, aspect-ratio 0.2s, box-shadow 0.2s;
            max-width: 100%;
            max-height: 165px;
        }

        .drag-preview-frame:active {
            cursor: grabbing;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.32), 0 0 0 2px var(--brand-primary);
        }

        .drag-preview-frame img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            user-select: none;
            -webkit-user-drag: none;
            pointer-events: none;
        }

        .drag-crosshair {
            position: absolute;
            width: 24px;
            height: 24px;
            transform: translate(-50%, -50%);
            pointer-events: none;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            box-shadow: 0 0 0 1.5px rgba(0, 0, 0, 0.6), 0 0 10px rgba(223, 178, 83, 0.9);
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

        /* Slider Controls */
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .control-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .control-val-badge {
            color: var(--brand-primary);
            font-weight: 800;
            font-family: monospace;
            background: #F1F5F9;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 0.72rem;
        }

        .range-slider {
            width: 100%;
            accent-color: var(--brand-primary);
            cursor: pointer;
            height: 6px;
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
            grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
            gap: 0.95rem;
            max-height: 280px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .gallery-grid::-webkit-scrollbar {
            width: 6px;
        }
        .gallery-grid::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 4px;
        }
        .gallery-grid::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        .gallery-grid::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
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
            transform: scale(1.02);
        }
        .gallery-item.selected {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(201, 151, 46, 0.4);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-item-del-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.75);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 5;
            padding: 0;
        }

        .gallery-item:hover .gallery-item-del-btn {
            opacity: 1;
        }

        .gallery-item-del-btn:hover {
            background: #DC2626;
            border-color: #EF4444;
            transform: scale(1.12);
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

        /* Responsive Design: Tablet and Mobile */
        @media (max-width: 1080px) {
            html, body {
                height: auto;
                max-height: none;
                overflow: auto;
            }
            .admin-container {
                overflow: visible;
                height: auto;
                min-height: auto;
            }
            .studio-layout {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                overflow: visible;
                height: auto;
            }
            .stage-card-wrapper {
                height: auto;
                max-height: 80vh;
            }
            .headings-editor-panel {
                height: auto;
                max-height: none;
                overflow: visible !important;
            }
            .inspector-panel {
                height: auto;
                max-height: none;
                overflow: visible !important;
            }
            .stage-viewport-box {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .admin-nav {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
                gap: 10px;
            }
            .admin-container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            .header-title h1 {
                font-size: 1.2rem;
            }
            .dashboard-header > div:last-child {
                display: flex;
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px !important;
                width: 100%;
            }
            .page-tabs {
                width: 100%;
                display: flex;
            }
            .page-tabs .tab-btn {
                flex: 1;
                padding: 10px 14px;
                min-height: 44px;
                font-size: 0.88rem;
            }
            #saveAllTopBtn {
                width: 100%;
                justify-content: center;
            }
            .stage-card-wrapper {
                padding: 1rem;
            }
            .drag-stage-preview {
                min-height: 260px;
                padding: 12px;
            }
            .drag-preview-frame {
                max-height: 240px;
            }
            .inspector-panel {
                padding: 1.15rem;
            }
            .inspector-actions {
                flex-direction: column;
            }
            .inspector-actions button {
                width: 100%;
                justify-content: center;
            }
            .section-input-row {
                flex-direction: column;
            }
            .modal-dialog {
                width: 95%;
                margin: 10px auto;
            }
            .modal-header, .modal-body, .modal-footer {
                padding: 1rem 1.15rem;
            }
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
                gap: 0.65rem;
            }
            .toast {
                left: 1rem;
                right: 1rem;
                bottom: 1rem;
                text-align: center;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .nav-actions {
                width: 100%;
                justify-content: space-between;
            }
            .nav-actions .btn {
                flex: 1;
                justify-content: center;
                padding: 0.5rem 0.75rem;
                font-size: 0.78rem;
            }
            .header-title h1 {
                font-size: 1.1rem;
            }
            .header-title p {
                font-size: 0.8rem;
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
             
                </div>
            </div>
        </a>

        <div class="nav-actions">
            <!-- Live Flipbook (Public live version) -->
            <a href="../For_Desc/index.html" class="btn btn-outline" target="_blank" title="View live published catalog in new tab">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polygon points="10 8 16 12 10 16 10 8"></polygon>
                </svg>
                <span>Live Flipbook</span>
            </a>

            <!-- Publish to IDC Button -->
            <button type="button" class="btn btn-publish-idc" id="publishToIdcTopBtn" onclick="publishToIDC()" title="Publish all saved draft images & labels to the live IDC catalog flipbook">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Publish to IDC</span>
            </button>

            <a href="?action=logout&return=../login.php" class="btn btn-danger-outline">
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--accent-gold)" stroke-width="2.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Catalog Layout Editor
                </h1>
                <p>Click any slot below or on the right to adjust framing, reposition focal point, and zoom.</p>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div class="page-tabs">
                    <button class="tab-btn active" id="tab-p4" onclick="switchPageTab('page4')">Page 4</button>
                    <button class="tab-btn" id="tab-p5" onclick="switchPageTab('page5')">Page 5</button>
                </div>

                <button type="button" class="btn btn-primary" id="saveAllTopBtn" onclick="saveAllChanges()" style="padding: 9px 18px; font-weight: 700; box-shadow: 0 4px 14px rgba(26, 100, 126, 0.25);" title="Save all adjustments across all images on both Page 4 and Page 5">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>

        <!-- Studio 3-Column Interface: IDC Flipbook | Section Headings Editor | Photo Adjustment & Actions -->
        <div class="studio-layout">
            <!-- COLUMN 1: Stage Canvas Preview (IDC Live Book) -->
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

            <!-- COLUMN 2: Section Headings Editor Panel -->
            <div class="headings-editor-panel">
                <div class="inspector-header">
                    <div class="inspector-title">
                        <span>Section Headings</span>
                    </div>
                </div>
                <div id="sectionCardsContainer" class="section-cards-list">
                    <!-- Populated dynamically with all Grid sections -->
                </div>
            </div>

            <!-- COLUMN 3: Photo Adjustment, Image Change/Upload & Action Buttons -->
            <div class="inspector-panel" id="slotInspector">
                <div class="inspector-header">
                    <div class="inspector-title">
                        <span>Photo Adjustment</span>
                        <span class="inspector-slot-id" id="insp-slot-id" style="display: none;"></span>
                    </div>
                </div>

                <!-- 1. Framing and Alignment Preview -->
                <div class="control-group" style="display: flex; flex-direction: column; gap: 4px; min-height: 0;">
                    <div class="control-label-row">
                        <span>Framing &amp; Alignment</span>
                        <span class="control-val-badge" id="insp-focal-text">50% / 50%</span>
                    </div>
                    <div class="drag-stage-preview">
                        <div class="drag-preview-frame" id="dragPreviewViewport">
                            <img src="" id="insp-preview-img" alt="Slot Preview" draggable="false">
                            <div class="drag-crosshair" id="dragCrosshair" style="left: 50%; top: 50%;"></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Upload / Replace Photo Option -->
                <div class="control-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <button type="button" class="btn-dashed-upload" onclick="openImageModal()" title="Upload new photo or pick from gallery">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Swap / Replace Photo</span>
                        <span style="font-size: 0.68rem; color: #64748B; font-weight: 500;">Upload or choose from gallery</span>
                    </button>
                </div>

                <!-- 3. Zoom Scale Slider -->
                <div class="control-group" style="margin-top: 2px;">
                    <div class="control-label-row">
                        <span>Zoom Scale</span>
                        <span class="control-val-badge" id="insp-zoom-text">1.00x</span>
                    </div>
                    <input type="range" class="range-slider" id="inspZoomSlider" min="1.00" max="3.00" step="0.05" value="1.00">
                </div>

                <!-- 4. Undo / Redo & 5. Reset / Save Buttons -->
                <div class="inspector-action-grid-2x2">
                    <button type="button" class="btn btn-outline" id="undoBtn" onclick="undoSlotChange()" title="Undo adjustment (Ctrl+Z)" disabled>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 14 4 9 9 4"></polyline>
                            <path d="M20 20v-7a4 4 0 0 0-4-4H4"></path>
                        </svg>
                        <span>Undo</span>
                    </button>
                    <button type="button" class="btn btn-outline" id="redoBtn" onclick="redoSlotChange()" title="Redo adjustment (Ctrl+Y)" disabled>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 14 20 9 15 4"></polyline>
                            <path d="M4 20v-7a4 4 0 0 1 4-4h12"></path>
                        </svg>
                        <span>Redo</span>
                    </button>
                    <button type="button" class="btn btn-outline" onclick="resetSlotAdjustments()" title="Reset focal position & zoom">
                        <span>Reset</span>
                    </button>
                    <button type="button" class="btn btn-accent" id="saveSlotBtn" onclick="saveActiveSlot()" title="Save currently selected image">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Save</span>
                    </button>
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
                    <img src="${imgSrc}" alt="${slot.id}" draggable="false" style="
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
                restoreDraftFromLocalStorage('page4');
                restoreDraftFromLocalStorage('page5');
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
            restoreDraftFromLocalStorage();
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

        // Render Section Headings in Column 2 for Tamil & English editing
        function renderSectionCards() {
            const container = document.getElementById('sectionCardsContainer');
            const pageData = catalogData[activePageKey];
            if (!container) return;

            function generateCardHtml(sec, idx) {
                return `
                    <div class="section-item-card" id="card-sec-${sec.id}">
                        <div style="font-size: 0.74rem; font-weight: 700; color: #1A647E; display: flex; justify-content: space-between;">
                            <span>Grid ${idx + 1}</span>
                        </div>
                        <div class="section-input-row">
                            <input type="text" id="sec-ta-${sec.id}" value="${sec.label_ta || ''}" placeholder="Tamil (e.g. சுடிதார்)">
                            <input type="text" id="sec-en-${sec.id}" value="${sec.label_en || ''}" placeholder="English (e.g. Silk Signature)">
                        </div>
                        <button type="button" class="section-save-btn" onclick="saveSectionHeading('${sec.id}')">Save Label</button>
                    </div>
                `;
            }

            if (!pageData || !Array.isArray(pageData.sections) || pageData.sections.length === 0) {
                container.innerHTML = '<div style="color: var(--text-muted); font-size: 0.8rem;">No sections found.</div>';
                return;
            }

            container.innerHTML = pageData.sections.map((sec, idx) => generateCardHtml(sec, idx)).join('');
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
                    showToast('Label updated successfully!');
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

        // Friendly Grid Name helper (e.g. "Grid 1 - Photo A")
        function getFriendlySlotLabel(slot) {
            if (!slot) return 'Select a photo';
            const pageData = catalogData[activePageKey];
            if (pageData && Array.isArray(pageData.slots)) {
                const idx = pageData.slots.findIndex(s => s.id === slot.id);
                if (idx !== -1) {
                    return `Grid ${idx + 1}`;
                }
            }
            return 'Selected Photo';
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

            // Update Inspector UI with friendly label (no raw IDs)
            document.getElementById('insp-slot-id').textContent = getFriendlySlotLabel(slot);
            const previewImg = document.getElementById('insp-preview-img');
            previewImg.src = slot.image || '../global assets/Images/products/product-1.jpg';

            // Match exact slot shape & aspect ratio in inspector preview
            const dragFrame = document.getElementById('dragPreviewViewport');
            if (dragFrame) {
                const w = parseFloat(slot.w) || 1;
                const h = parseFloat(slot.h) || 1;
                // Since stage has aspect-ratio 4/5, absolute slot width/height in px is:
                // aspect ratio = (slot.w * 4) / (slot.h * 5) = (slot.w / slot.h) * 0.8
                const aspect = (w / h) * 0.8;
                let frameHeight = 160;
                let frameWidth = frameHeight * aspect;
                if (frameWidth > 220) {
                    frameWidth = 220;
                    frameHeight = frameWidth / aspect;
                }
                dragFrame.style.width = `${Math.round(frameWidth)}px`;
                dragFrame.style.height = `${Math.round(frameHeight)}px`;
                dragFrame.style.borderRadius = slot.radius || '4px';
            }

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
            if (crosshair) {
                crosshair.style.left = `${fx}%`;
                crosshair.style.top = `${fy}%`;
            }

            // Preview image styling
            const previewImg = document.getElementById('insp-preview-img');
            if (previewImg) {
                previewImg.style.objectPosition = `${fx}% ${fy}%`;
                previewImg.style.transform = `scale(${zoom})`;
                previewImg.style.transformOrigin = `${fx}% ${fy}%`;
            }

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

        // Mouse & Touch Drag to Reposition Focal Point in Right Inspector Viewport (Relative Delta Drag)
        const dragViewport = document.getElementById('dragPreviewViewport');
        let dragStartX = 0;
        let dragStartY = 0;
        let focalStartX = 50;
        let focalStartY = 50;
        
        function handleFocalDrag(e) {
            if (!activeSlot || !dragViewport) return;
            const touch = e.touches ? e.touches[0] : null;
            const clientX = touch ? touch.clientX : e.clientX;
            const clientY = touch ? touch.clientY : e.clientY;
            if (clientX === undefined || clientY === undefined) return;

            const rect = dragViewport.getBoundingClientRect();
            if (rect.width <= 0 || rect.height <= 0) return;

            // Compute delta from drag start point
            const deltaPercentX = ((clientX - dragStartX) / rect.width) * 100;
            const deltaPercentY = ((clientY - dragStartY) / rect.height) * 100;

            let newFx = focalStartX + deltaPercentX;
            let newFy = focalStartY + deltaPercentY;

            newFx = Math.max(0, Math.min(100, newFx));
            newFy = Math.max(0, Math.min(100, newFy));

            activeSlot.focal_x = parseFloat(newFx.toFixed(2));
            activeSlot.focal_y = parseFloat(newFy.toFixed(2));

            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        }

        function startViewportDrag(e) {
            if (!activeSlot || !dragViewport) return;
            // Prevent browser native image ghost-drag or text selection
            if (e.cancelable && e.type === 'touchstart') e.preventDefault();
            if (e.type === 'mousedown') e.preventDefault();

            preDragSnapshot = getSlotSnapshot(activeSlot);
            isDraggingCrosshair = true;

            const touch = e.touches ? e.touches[0] : null;
            dragStartX = touch ? touch.clientX : e.clientX;
            dragStartY = touch ? touch.clientY : e.clientY;
            focalStartX = typeof activeSlot.focal_x !== 'undefined' ? parseFloat(activeSlot.focal_x) : 50;
            focalStartY = typeof activeSlot.focal_y !== 'undefined' ? parseFloat(activeSlot.focal_y) : 50;
        }

        if (dragViewport) {
            dragViewport.addEventListener('mousedown', startViewportDrag);
            dragViewport.addEventListener('touchstart', startViewportDrag, { passive: false });

            // Mouse Wheel Zoom inside Drag Viewport
            dragViewport.addEventListener('wheel', (e) => {
                e.preventDefault();
                if (!activeSlot) return;
                pushUndoSnapshot(getSlotSnapshot(activeSlot));
                let z = parseFloat(activeSlot.zoom) || 1.00;
                z += e.deltaY < 0 ? 0.05 : -0.05;
                z = Math.max(1.00, Math.min(3.00, z));
                activeSlot.zoom = parseFloat(z.toFixed(2));
                updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
                persistDraftToLocalStorage();
            }, { passive: false });
        }

        function onGlobalDragMove(e) {
            if (isDraggingCrosshair) {
                if (e.cancelable && e.type === 'touchmove') e.preventDefault();
                handleFocalDrag(e);
            }
            if (isDraggingStageSlot) {
                if (e.cancelable && e.type === 'touchmove') e.preventDefault();
                handleStageSlotDrag(e);
            }
        }

        function onGlobalDragEnd() {
            if (isDraggingCrosshair || isDraggingStageSlot) {
                // If position actually changed during drag, push preDragSnapshot to undo stack
                if (preDragSnapshot && activeSlot) {
                    const changed = (preDragSnapshot.focal_x !== activeSlot.focal_x || preDragSnapshot.focal_y !== activeSlot.focal_y);
                    if (changed) {
                        pushUndoSnapshot(preDragSnapshot);
                    }
                }
                preDragSnapshot = null;
                persistDraftToLocalStorage();
            }
            isDraggingCrosshair = false;
            isDraggingStageSlot = false;
            stageDragSlotElement = null;
        }

        window.addEventListener('mousemove', onGlobalDragMove);
        window.addEventListener('mouseup', onGlobalDragEnd);
        window.addEventListener('touchmove', onGlobalDragMove, { passive: false });
        window.addEventListener('touchend', onGlobalDragEnd);
        window.addEventListener('touchcancel', onGlobalDragEnd);

        // DIRECT STAGE CANVAS DRAGGING (Relative Delta Drag on Slot)
        let isDraggingStageSlot = false;
        let stageDragSlotElement = null;
        let stageDragStartX = 0;
        let stageDragStartY = 0;
        let stageFocalStartX = 50;
        let stageFocalStartY = 50;

        function handleStageSlotDrag(e) {
            if (!activeSlot || !stageDragSlotElement) return;
            const touch = e.touches ? e.touches[0] : null;
            const clientX = touch ? touch.clientX : e.clientX;
            const clientY = touch ? touch.clientY : e.clientY;
            if (clientX === undefined || clientY === undefined) return;

            const rect = stageDragSlotElement.getBoundingClientRect();
            if (rect.width <= 0 || rect.height <= 0) return;

            // Compute delta from initial drag start point
            const deltaPercentX = ((clientX - stageDragStartX) / rect.width) * 100;
            const deltaPercentY = ((clientY - stageDragStartY) / rect.height) * 100;

            let newFx = stageFocalStartX + deltaPercentX;
            let newFy = stageFocalStartY + deltaPercentY;

            newFx = Math.max(0, Math.min(100, newFx));
            newFy = Math.max(0, Math.min(100, newFy));

            activeSlot.focal_x = parseFloat(newFx.toFixed(2));
            activeSlot.focal_y = parseFloat(newFy.toFixed(2));

            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        }

        function startStageSlotDrag(e) {
            const slotEl = e.target.closest('.slot');
            if (!slotEl) return;
            // Prevent browser image dragging & text highlighting
            if (e.cancelable && e.type === 'touchstart') e.preventDefault();
            if (e.type === 'mousedown') e.preventDefault();

            const slotId = slotEl.getAttribute('data-slot-id');
            if (!activeSlot || activeSlot.id !== slotId) {
                selectSlot(slotId);
            }
            preDragSnapshot = getSlotSnapshot(activeSlot);
            isDraggingStageSlot = true;
            stageDragSlotElement = slotEl;

            const touch = e.touches ? e.touches[0] : null;
            stageDragStartX = touch ? touch.clientX : e.clientX;
            stageDragStartY = touch ? touch.clientY : e.clientY;
            stageFocalStartX = typeof activeSlot.focal_x !== 'undefined' ? parseFloat(activeSlot.focal_x) : 50;
            stageFocalStartY = typeof activeSlot.focal_y !== 'undefined' ? parseFloat(activeSlot.focal_y) : 50;
        }

        // Attach event delegation for direct dragging and wheel zoom on Stage Slots
        const adminSlotsLayer = document.getElementById('admin-slots-layer');
        if (adminSlotsLayer) {
            adminSlotsLayer.addEventListener('mousedown', startStageSlotDrag);
            adminSlotsLayer.addEventListener('touchstart', startStageSlotDrag, { passive: false });

            adminSlotsLayer.addEventListener('wheel', (e) => {
                const slotEl = e.target.closest('.slot');
                if (!slotEl) return;
                const slotId = slotEl.getAttribute('data-slot-id');
                if (!activeSlot || activeSlot.id !== slotId) {
                    selectSlot(slotId);
                }
                e.preventDefault();
                pushUndoSnapshot(getSlotSnapshot(activeSlot));
                let z = parseFloat(activeSlot.zoom) || 1.00;
                z += e.deltaY < 0 ? 0.05 : -0.05;
                z = Math.max(1.00, Math.min(3.00, z));
                activeSlot.zoom = parseFloat(z.toFixed(2));
                updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
                persistDraftToLocalStorage();
            }, { passive: false });
        }

        // Zoom Slider change
        let sliderStartSnapshot = null;
        const zoomSliderEl = document.getElementById('inspZoomSlider');
        zoomSliderEl.addEventListener('mousedown', () => {
            if (activeSlot) sliderStartSnapshot = getSlotSnapshot(activeSlot);
        });
        zoomSliderEl.addEventListener('input', function() {
            if (!activeSlot) return;
            activeSlot.zoom = parseFloat(this.value);
            updateInspectorValues(activeSlot.focal_x, activeSlot.focal_y, activeSlot.zoom);
        });
        zoomSliderEl.addEventListener('change', function() {
            if (sliderStartSnapshot) {
                pushUndoSnapshot(sliderStartSnapshot);
                sliderStartSnapshot = null;
            }
            persistDraftToLocalStorage();
        });

        // ==================== UNDO / REDO & LOCALSTORAGE PERSISTENCE ====================
        const LOCAL_STORAGE_KEY_PREFIX = 'yarika_slot_state_';
        const undoStack = [];
        const redoStack = [];
        const MAX_HISTORY_STEPS = 50;

        // Current snapshot factory for active slot
        function getSlotSnapshot(slot) {
            if (!slot) return null;
            return {
                slotId: slot.id,
                pageKey: activePageKey,
                focal_x: typeof slot.focal_x !== 'undefined' ? parseFloat(slot.focal_x) : 50,
                focal_y: typeof slot.focal_y !== 'undefined' ? parseFloat(slot.focal_y) : 50,
                zoom: typeof slot.zoom !== 'undefined' ? parseFloat(slot.zoom) : 1.00,
                image: slot.image || ''
            };
        }

        // Save current modifications for current page (or all pages) in LocalStorage
        function persistDraftToLocalStorage(pageKey = activePageKey) {
            try {
                if (!catalogData || !catalogData[pageKey] || !Array.isArray(catalogData[pageKey].slots)) return;
                const pageDraft = {};
                catalogData[pageKey].slots.forEach(s => {
                    pageDraft[s.id] = {
                        focal_x: s.focal_x,
                        focal_y: s.focal_y,
                        zoom: s.zoom,
                        image: s.image
                    };
                });
                localStorage.setItem(LOCAL_STORAGE_KEY_PREFIX + pageKey, JSON.stringify(pageDraft));
            } catch (e) {
                console.warn("Could not save to LocalStorage:", e);
            }
        }

        // Restore drafts from LocalStorage
        function restoreDraftFromLocalStorage(pageKey = activePageKey) {
            try {
                const stored = localStorage.getItem(LOCAL_STORAGE_KEY_PREFIX + pageKey);
                if (!stored) return;
                const pageDraft = JSON.parse(stored);
                if (!pageDraft || !catalogData[pageKey] || !Array.isArray(catalogData[pageKey].slots)) return;

                catalogData[pageKey].slots.forEach(s => {
                    if (pageDraft[s.id]) {
                        if (typeof pageDraft[s.id].focal_x !== 'undefined') s.focal_x = pageDraft[s.id].focal_x;
                        if (typeof pageDraft[s.id].focal_y !== 'undefined') s.focal_y = pageDraft[s.id].focal_y;
                        if (typeof pageDraft[s.id].zoom !== 'undefined') s.zoom = pageDraft[s.id].zoom;
                        if (pageDraft[s.id].image) s.image = pageDraft[s.id].image;
                    }
                });
            } catch (e) {
                console.warn("Could not restore from LocalStorage:", e);
            }
        }

        function updateUndoRedoButtons() {
            const uBtn = document.getElementById('undoBtn');
            const rBtn = document.getElementById('redoBtn');
            if (uBtn) uBtn.disabled = undoStack.length === 0;
            if (rBtn) rBtn.disabled = redoStack.length === 0;
        }

        function pushUndoSnapshot(snapshot) {
            if (!snapshot) return;
            // Prevent pushing duplicate identical consecutive state
            const last = undoStack[undoStack.length - 1];
            if (last &&
                last.slotId === snapshot.slotId &&
                last.pageKey === snapshot.pageKey &&
                last.focal_x === snapshot.focal_x &&
                last.focal_y === snapshot.focal_y &&
                last.zoom === snapshot.zoom &&
                last.image === snapshot.image
            ) {
                return;
            }
            undoStack.push(snapshot);
            if (undoStack.length > MAX_HISTORY_STEPS) undoStack.shift();
            // Clear redo stack on new action
            redoStack.length = 0;
            updateUndoRedoButtons();
            persistDraftToLocalStorage();
        }

        function applySlotSnapshot(snapshot) {
            if (!snapshot) return;
            // If snapshot belongs to another page, switch tab first
            if (snapshot.pageKey !== activePageKey) {
                switchPageTab(snapshot.pageKey);
            }
            const pageData = catalogData[snapshot.pageKey];
            if (!pageData || !Array.isArray(pageData.slots)) return;
            const targetSlot = pageData.slots.find(s => s.id === snapshot.slotId);
            if (!targetSlot) return;

            // Make sure slot is active
            if (!activeSlot || activeSlot.id !== targetSlot.id) {
                selectSlot(targetSlot.id);
            }

            targetSlot.focal_x = snapshot.focal_x;
            targetSlot.focal_y = snapshot.focal_y;
            targetSlot.zoom = snapshot.zoom;
            targetSlot.image = snapshot.image;

            // Update previews
            const previewImg = document.getElementById('insp-preview-img');
            if (previewImg && snapshot.image) previewImg.src = snapshot.image;
            const stageSlotImg = document.querySelector(`#slot-${targetSlot.id} img`);
            if (stageSlotImg && snapshot.image) stageSlotImg.src = snapshot.image;

            updateInspectorValues(targetSlot.focal_x, targetSlot.focal_y, targetSlot.zoom);
            persistDraftToLocalStorage();
        }

        function undoSlotChange() {
            if (undoStack.length === 0 || !activeSlot) return;
            const currentState = getSlotSnapshot(activeSlot);
            const prevState = undoStack.pop();
            redoStack.push(currentState);
            applySlotSnapshot(prevState);
            updateUndoRedoButtons();
            showToast("Undo applied");
        }

        function redoSlotChange() {
            if (redoStack.length === 0 || !activeSlot) return;
            const currentState = getSlotSnapshot(activeSlot);
            const nextState = redoStack.pop();
            undoStack.push(currentState);
            applySlotSnapshot(nextState);
            updateUndoRedoButtons();
            showToast("Redo applied");
        }

        // Track drag start snapshots
        let preDragSnapshot = null;

        // Reset Button
        function resetSlotAdjustments() {
            if (!activeSlot) return;
            pushUndoSnapshot(getSlotSnapshot(activeSlot));
            activeSlot.focal_x = 50.0;
            activeSlot.focal_y = 50.0;
            activeSlot.zoom = 1.00;
            updateInspectorValues(50, 50, 1.00);
            persistDraftToLocalStorage();
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
                    try {
                        localStorage.removeItem(LOCAL_STORAGE_KEY_PREFIX + activePageKey);
                    } catch (e) {}
                    showToast('Image updated successfully!');
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
                    <span>Save</span>
                `;
            }
        }

        // Save All Changes across all slots on both pages to API
        async function saveAllChanges() {
            // Gather all slots from both page4 and page5
            const pageKeys = ['page4', 'page5'];
            let allSlotsToSave = [];

            pageKeys.forEach(pk => {
                if (catalogData && catalogData[pk] && Array.isArray(catalogData[pk].slots)) {
                    allSlotsToSave = allSlotsToSave.concat(catalogData[pk].slots);
                }
            });

            if (allSlotsToSave.length === 0) {
                showToast("No slots found to save.");
                return;
            }

            const topBtn = document.getElementById('saveAllTopBtn');

            if (topBtn) {
                topBtn.disabled = true;
                topBtn.innerHTML = `<span>Saving all...</span>`;
            }

            try {
                // Map all slots with current modified values
                const payloadSlots = allSlotsToSave.map(s => ({
                    slot_id: s.id,
                    image: s.image,
                    focal_x: typeof s.focal_x !== 'undefined' ? s.focal_x : 50.0,
                    focal_y: typeof s.focal_y !== 'undefined' ? s.focal_y : 50.0,
                    zoom: typeof s.zoom !== 'undefined' ? s.zoom : 1.00
                }));

                const res = await fetch('../api/save_slot.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slots: payloadSlots })
                });

                const data = await res.json();
                if (data.success) {
                    try {
                        localStorage.removeItem(LOCAL_STORAGE_KEY_PREFIX + 'page4');
                        localStorage.removeItem(LOCAL_STORAGE_KEY_PREFIX + 'page5');
                    } catch (e) {}
                    showToast('All changes saved successfully!');
                } else {
                    showToast(data.message || 'Failed to save all changes');
                }
            } catch (err) {
                console.error("Batch save error:", err);
                showToast("Network error while saving all changes.");
            } finally {
                if (topBtn) {
                    topBtn.disabled = false;
                    topBtn.innerHTML = `
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <span>Save All Changes</span>
                    `;
                }
            }
        }

        // Publish to IDC Live Flipbook (copies draft slots & sections to live public state)
        async function publishToIDC() {
            const confirmed = confirm("Are you sure you want to publish all saved changes to the live IDC catalog flipbook? This will make all current drafts visible on the public flipbook.");
            if (!confirmed) return;

            // First ensure any pending local adjustments are saved
            try {
                showToast("Publishing all changes to IDC live...");
                const res = await fetch('../api/publish_to_idc.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'publish' })
                });

                const data = await res.json();
                if (data.success) {
                    showToast("🎉 Published to IDC! Live flipbook is now updated.");
                } else {
                    showToast(data.message || "Failed to publish to IDC.");
                }
            } catch (err) {
                console.error("Publish to IDC error:", err);
                showToast("Network error publishing to IDC.");
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
                    if (data.images.length === 0) {
                        grid.innerHTML = '<div style="color: var(--text-muted); padding: 1rem; grid-column: 1 / -1;">No photos in gallery. Upload one above!</div>';
                        return;
                    }
                    data.images.forEach(img => {
                        const activeImgName = activeSlot && activeSlot.image ? activeSlot.image.split('/').pop().split('?')[0] : '';
                        const galleryImgName = img.name || (img.url ? img.url.split('/').pop().split('?')[0] : '');
                        const isMatch = activeSlot && (activeSlot.image === img.url || (activeImgName && activeImgName === galleryImgName));
                        const item = document.createElement('div');
                        item.className = 'gallery-item' + (isMatch ? ' selected' : '');
                        item.onclick = (e) => {
                            if (e.target.closest('.gallery-item-del-btn')) return;
                            selectGalleryItem(img.url, item);
                        };
                        item.innerHTML = `
                            <img src="${img.url}" alt="${img.name}" loading="lazy">
                            <button type="button" class="gallery-item-del-btn" title="Remove photo from gallery" onclick="removeGalleryImage(event, '${img.name}', '${img.url}')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        `;
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

        async function removeGalleryImage(e, filename, url) {
            e.stopPropagation();
            if (!confirm(`Are you sure you want to remove "${filename}" from the gallery?`)) {
                return;
            }

            try {
                const res = await fetch('../api/delete_gallery_image.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename: filename })
                });

                const data = await res.json();
                if (data.success) {
                    showToast("Photo removed from gallery.");
                    if (selectedGalleryUrl === url) {
                        selectedGalleryUrl = null;
                        document.getElementById('applySelectedImageBtn').disabled = true;
                    }
                    fetchGalleryImages();
                } else {
                    showToast(data.message || "Failed to remove photo.");
                }
            } catch (err) {
                showToast("Network error removing photo.");
            }
        }

        function selectGalleryItem(url, el) {
            document.querySelectorAll('.gallery-item').forEach(i => i.classList.remove('selected'));
            el.classList.add('selected');
            selectedGalleryUrl = url;
            document.getElementById('applySelectedImageBtn').disabled = false;
        }

        function setSlotImage(newImageUrl, msg = "Image updated successfully!") {
            if (!activeSlot || !newImageUrl) return;
            pushUndoSnapshot(getSlotSnapshot(activeSlot));
            activeSlot.image = newImageUrl;

            // 1. Update Inspector preview image
            const inspImg = document.getElementById('insp-preview-img');
            if (inspImg) inspImg.src = newImageUrl;

            // 2. Update Stage slot element directly
            const slotEl = document.getElementById(`slot-${activeSlot.id}`);
            if (slotEl) {
                const stageSlotImg = slotEl.querySelector('img');
                if (stageSlotImg) {
                    stageSlotImg.src = newImageUrl;
                }
            }

            // 3. Keep focal point and zoom styling in sync
            const fx = typeof activeSlot.focal_x !== 'undefined' ? activeSlot.focal_x : 50;
            const fy = typeof activeSlot.focal_y !== 'undefined' ? activeSlot.focal_y : 50;
            const zoom = typeof activeSlot.zoom !== 'undefined' ? activeSlot.zoom : 1.00;
            updateInspectorValues(fx, fy, zoom);

            // 4. Save to LocalStorage draft
            persistDraftToLocalStorage();

            // 5. Automatically persist slot to database so it stays saved
            saveActiveSlotSilent();

            showToast(msg);
        }

        async function saveActiveSlotSilent() {
            if (!activeSlot) return;
            try {
                await fetch('../api/save_slot.php', {
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
            } catch (err) {
                console.warn("Silent slot save background sync:", err);
            }
        }

        function applySelectedGalleryImage() {
            if (!selectedGalleryUrl || !activeSlot) return;
            const chosenUrl = selectedGalleryUrl;
            closeImageModal();
            setSlotImage(chosenUrl, "Image updated successfully!");
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
                    closeImageModal();
                    setSlotImage(data.url, "Image updated successfully!");
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

        // Keyboard Shortcuts (Ctrl+Z for Undo, Ctrl+Y or Ctrl+Shift+Z for Redo, Esc for modal)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeImageModal();

            // Ignore when typing inside an input
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) {
                    redoSlotChange();
                } else {
                    undoSlotChange();
                }
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redoSlotChange();
            }
        });

        // Initialize Studio
        fetchCatalog();
    </script>
</body>
</html>

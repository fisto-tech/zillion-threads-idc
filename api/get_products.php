<?php
// CORS Header check - allow same origin or localhost dev environments safely
$allowedOrigins = ['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost:3000', 'http://localhost:5500', 'http://127.0.0.1:5500'];
$httpOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($httpOrigin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $httpOrigin");
    header("Access-Control-Allow-Credentials: true");
} else {
    // If same host without explicit Origin header, do not wildcard credentials
    header('Access-Control-Allow-Origin: *');
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../admin/db.php';
$pdo = getDbConnection();
$dataFile = __DIR__ . '/products.json';

// ==================== GET REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($pdo) {
        try {
            // Fetch pages
            $pageStmt = $pdo->query("SELECT id, title, subtitle FROM catalog_pages ORDER BY id ASC");
            $pages = $pageStmt->fetchAll();

            // Fetch sections
            $sectionsStmt = $pdo->query("SELECT id, page_key, label_ta, label_en, x, y, w, h FROM catalog_sections ORDER BY y ASC, x ASC");
            $allSections = $sectionsStmt->fetchAll();

            // Fetch slots
            $slotsStmt = $pdo->query("SELECT id, page_key, section_id, x, y, w, h, radius, z_index, image, focal_x, focal_y, zoom FROM catalog_slots ORDER BY z_index ASC, id ASC");
            $allSlots = $slotsStmt->fetchAll();

            // Fetch legacy items for fallback or other pages
            $itemStmt = $pdo->query("SELECT id, page_key, sort_order, name, category, image FROM catalog_items ORDER BY page_key ASC, sort_order ASC");
            $items = $itemStmt->fetchAll();

            $result = [];
            foreach ($pages as $p) {
                $pageKey = $p['id'];
                $result[$pageKey] = [
                    'title' => $p['title'],
                    'subtitle' => $p['subtitle'] ?? '',
                    'sections' => [],
                    'slots' => [],
                    'items' => []
                ];
            }

            foreach ($allSections as $sec) {
                $pageKey = $sec['page_key'];
                if (isset($result[$pageKey])) {
                    $result[$pageKey]['sections'][] = [
                        'id' => $sec['id'],
                        'label_ta' => $sec['label_ta'],
                        'label_en' => $sec['label_en'],
                        'x' => (float)$sec['x'],
                        'y' => (float)$sec['y'],
                        'w' => (float)$sec['w'],
                        'h' => (float)$sec['h']
                    ];
                }
            }

            foreach ($allSlots as $slot) {
                $pageKey = $slot['page_key'];
                if (isset($result[$pageKey])) {
                    $result[$pageKey]['slots'][] = [
                        'id' => $slot['id'],
                        'section_id' => $slot['section_id'],
                        'x' => (float)$slot['x'],
                        'y' => (float)$slot['y'],
                        'w' => (float)$slot['w'],
                        'h' => (float)$slot['h'],
                        'radius' => $slot['radius'] ?? '0',
                        'z_index' => (int)$slot['z_index'],
                        'image' => $slot['image'] ?? '',
                        'focal_x' => (float)$slot['focal_x'],
                        'focal_y' => (float)$slot['focal_y'],
                        'zoom' => (float)$slot['zoom']
                    ];
                }
            }

            foreach ($items as $item) {
                $pageKey = $item['page_key'];
                if (isset($result[$pageKey])) {
                    $result[$pageKey]['items'][] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'category' => $item['category'] ?? '',
                        'image' => $item['image']
                    ];
                }
            }

            echo json_encode($result, JSON_UNESCAPED_SLASHES);
            exit();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Database query failed: " . $e->getMessage()]);
            exit();
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database connection unavailable"]);
        exit();
    }
}

// ==================== POST / PUT REQUEST (Admin Update) ====================
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!$data || !is_array($data) || (!isset($data['page4']) && !isset($data['page5']))) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid product structure payload"]);
        exit();
    }

    $dbSuccess = false;

    if ($pdo) {
        try {
            $pdo->beginTransaction();

            $pageStmt = $pdo->prepare("
                INSERT INTO catalog_pages (id, title, subtitle)
                VALUES (:id, :title, :subtitle)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    subtitle = VALUES(subtitle)
            ");

            $itemStmt = $pdo->prepare("
                INSERT INTO catalog_items (id, page_key, sort_order, name, category, image)
                VALUES (:id, :page_key, :sort_order, :name, :category, :image)
                ON DUPLICATE KEY UPDATE
                    page_key = VALUES(page_key),
                    sort_order = VALUES(sort_order),
                    name = VALUES(name),
                    category = VALUES(category),
                    image = VALUES(image)
            ");

            foreach ($data as $pageKey => $pageData) {
                if (!is_array($pageData)) continue;
                $pageId = (string)$pageKey;
                $title = $pageData['title'] ?? '';
                $subtitle = $pageData['subtitle'] ?? '';

                $pageStmt->execute([
                    ':id' => $pageId,
                    ':title' => $title,
                    ':subtitle' => $subtitle
                ]);

                if (isset($pageData['items']) && is_array($pageData['items'])) {
                    foreach ($pageData['items'] as $index => $item) {
                        $itemId = $item['id'] ?? ($pageId . '-' . ($index + 1));
                        $itemStmt->execute([
                            ':id' => $itemId,
                            ':page_key' => $pageId,
                            ':sort_order' => $index,
                            ':name' => $item['name'] ?? '',
                            ':category' => $item['category'] ?? '',
                            ':image' => $item['image'] ?? ''
                        ]);
                    }
                }
            }

            $pdo->commit();
            $dbSuccess = true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Database error saving products: " . $e->getMessage());
        }
    }

    $jsonSaved = @file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    if ($dbSuccess || $jsonSaved !== false) {
        echo json_encode([
            "success" => true,
            "message" => "Products saved successfully to " . ($dbSuccess ? "MySQL database" : "local storage"),
            "storage" => $dbSuccess ? "mysql" : "file"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Could not save products"]);
    }
    exit();
}

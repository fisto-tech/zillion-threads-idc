<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../admin/db.php';
$pdo = getDbConnection();
$dataFile = __DIR__ . '/products.json';

// Helper function to read from JSON fallback
function readJsonFallback($dataFile) {
    if (file_exists($dataFile)) {
        return json_decode(file_get_contents($dataFile), true) ?: [];
    }
    return [];
}

// ==================== GET REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($pdo) {
        try {
            // Fetch pages
            $pageStmt = $pdo->query("SELECT id, title, subtitle FROM catalog_pages ORDER BY id ASC");
            $pages = $pageStmt->fetchAll();

            // Fetch products
            $itemStmt = $pdo->query("SELECT id, page_key, sort_order, name, category, description, image FROM catalog_items ORDER BY page_key ASC, sort_order ASC");
            $items = $itemStmt->fetchAll();

            $result = [];
            foreach ($pages as $p) {
                $pageKey = $p['id'];
                $result[$pageKey] = [
                    'title' => $p['title'],
                    'subtitle' => $p['subtitle'] ?? '',
                    'items' => []
                ];
            }

            foreach ($items as $item) {
                $pageKey = $item['page_key'];
                if (isset($result[$pageKey])) {
                    $result[$pageKey]['items'][] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'category' => $item['category'] ?? '',
                        'description' => $item['description'] ?? '',
                        'image' => $item['image']
                    ];
                }
            }

            if (!empty($result)) {
                echo json_encode($result, JSON_UNESCAPED_SLASHES);
                exit();
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No catalog data found in database"]);
                exit();
            }
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
                INSERT INTO catalog_items (id, page_key, sort_order, name, category, description, image)
                VALUES (:id, :page_key, :sort_order, :name, :category, :description, :image)
                ON DUPLICATE KEY UPDATE
                    page_key = VALUES(page_key),
                    sort_order = VALUES(sort_order),
                    name = VALUES(name),
                    category = VALUES(category),
                    description = VALUES(description),
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
                            ':description' => $item['description'] ?? '',
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

    // Always keep products.json synchronized as safety backup and offline redundancy
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

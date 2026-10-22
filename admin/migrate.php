<?php
/**
 * Safe Migration Script: products.json -> MySQL (product_catalog)
 *
 * Can be run from CLI: php admin/migrate.php
 * Or accessed via browser while logged in as admin.
 */

if (php_sapi_name() !== 'cli') {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        die("Unauthorized. Please log in as admin to run this migration.");
    }
}

require_once __DIR__ . '/db.php';

$pdo = getDbConnection();
if (!$pdo) {
    die("Database connection failed. Please verify credentials in admin/config.php or environment variables.\n");
}

// 1. Create tables if they do not exist
$schemaSql = file_get_contents(__DIR__ . '/database.sql');
try {
    $pdo->exec($schemaSql);
    echo "[OK] Database tables verified/created successfully.\n";
} catch (PDOException $e) {
    die("[ERROR] Failed to execute database.sql: " . $e->getMessage() . "\n");
}

// 2. Locate products.json
$jsonPath = __DIR__ . '/../api/products.json';
if (!file_exists($jsonPath)) {
    $jsonPath = __DIR__ . '/products.json';
}

if (!file_exists($jsonPath)) {
    die("[ERROR] products.json not found.\n");
}

// 3. Create a backup of the original JSON file if not already backed up
$backupPath = $jsonPath . '.backup.' . date('Ymd_His');
if (!copy($jsonPath, $backupPath)) {
    die("[ERROR] Failed to create backup of products.json.\n");
}
echo "[OK] Backup created at: {$backupPath}\n";

// 4. Validate and decode JSON
$raw = file_get_contents($jsonPath);
$data = json_decode($raw, true);

if (!$data || !is_array($data)) {
    die("[ERROR] Invalid or corrupt JSON structure in products.json.\n");
}

// 5. Migrate inside a transaction
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

    $pageCount = 0;
    $itemCount = 0;

    foreach ($data as $pageKey => $pageData) {
        $pageId = (string)$pageKey;
        $title = $pageData['title'] ?? '';
        $subtitle = $pageData['subtitle'] ?? '';

        $pageStmt->execute([
            ':id' => $pageId,
            ':title' => $title,
            ':subtitle' => $subtitle
        ]);
        $pageCount++;

        if (isset($pageData['items']) && is_array($pageData['items'])) {
            foreach ($pageData['items'] as $index => $item) {
                $itemId = $item['id'] ?? ($pageId . '-' . ($index + 1));
                $name = $item['name'] ?? '';
                $category = $item['category'] ?? '';
                $description = $item['description'] ?? '';
                $image = $item['image'] ?? '';

                $itemStmt->execute([
                    ':id' => $itemId,
                    ':page_key' => $pageId,
                    ':sort_order' => $index,
                    ':name' => $name,
                    ':category' => $category,
                    ':description' => $description,
                    ':image' => $image
                ]);
                $itemCount++;
            }
        }
    }

    $pdo->commit();
    echo "[SUCCESS] Migration complete! {$pageCount} catalog pages and {$itemCount} products migrated/synchronized.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "[ERROR] Migration failed and was rolled back: " . $e->getMessage() . "\n";
}

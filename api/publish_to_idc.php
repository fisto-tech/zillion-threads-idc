<?php
// CORS Header check - allow authorized origin or localhost dev environments safely
$allowedOrigins = ['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost:3000', 'http://localhost:5500', 'http://127.0.0.1:5500'];
$httpOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($httpOrigin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $httpOrigin");
    header("Access-Control-Allow-Credentials: true");
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized. Please log in first."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

require_once __DIR__ . '/../admin/db.php';
$pdo = getDbConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection unavailable."]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Ensure published tables exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS catalog_slots_published LIKE catalog_slots;");
    $pdo->exec("CREATE TABLE IF NOT EXISTS catalog_sections_published LIKE catalog_sections;");

    // Copy all working slots to published
    $pdo->exec("DELETE FROM catalog_slots_published;");
    $pdo->exec("INSERT INTO catalog_slots_published SELECT * FROM catalog_slots;");

    // Copy all working sections to published
    $pdo->exec("DELETE FROM catalog_sections_published;");
    $pdo->exec("INSERT INTO catalog_sections_published SELECT * FROM catalog_sections;");

    $pdo->commit();

    $publishedSlotsCount = (int)$pdo->query("SELECT COUNT(*) FROM catalog_slots_published")->fetchColumn();

    echo json_encode([
        "success" => true,
        "message" => "All changes published to IDC live flipbook successfully!",
        "count" => $publishedSlotsCount,
        "published_at" => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Publishing failed: " . $e->getMessage()]);
}

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

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || !isset($data['section_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing section_id."]);
    exit();
}

$sectionId = trim((string)$data['section_id']);
$labelTa = isset($data['label_ta']) ? trim((string)$data['label_ta']) : '';
$labelEn = isset($data['label_en']) ? trim((string)$data['label_en']) : '';

try {
    $stmt = $pdo->prepare("
        UPDATE catalog_sections
        SET label_ta = :label_ta, label_en = :label_en
        WHERE id = :id
    ");
    $stmt->execute([
        ':label_ta' => $labelTa,
        ':label_en' => $labelEn,
        ':id' => $sectionId
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Section heading updated successfully",
        "section" => [
            "id" => $sectionId,
            "label_ta" => $labelTa,
            "label_en" => $labelEn
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database update failed: " . $e->getMessage()]);
}

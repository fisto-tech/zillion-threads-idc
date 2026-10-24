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

// Support batch save if 'slots' array is supplied
if (isset($data['slots']) && is_array($data['slots'])) {
    $slotsList = $data['slots'];
    if (empty($slotsList)) {
        echo json_encode(["success" => true, "message" => "No slots to update.", "count" => 0]);
        exit();
    }

    try {
        $pdo->beginTransaction();
        $updateStmtWithImg = $pdo->prepare("
            UPDATE catalog_slots
            SET image = :image, focal_x = :focal_x, focal_y = :focal_y, zoom = :zoom
            WHERE id = :id
        ");
        $updateStmtNoImg = $pdo->prepare("
            UPDATE catalog_slots
            SET focal_x = :focal_x, focal_y = :focal_y, zoom = :zoom
            WHERE id = :id
        ");

        $updatedCount = 0;
        foreach ($slotsList as $s) {
            if (!isset($s['id']) && !isset($s['slot_id'])) continue;
            $sId = trim((string)($s['id'] ?? $s['slot_id']));
            $sImg = isset($s['image']) ? trim((string)$s['image']) : null;
            $sFx = isset($s['focal_x']) ? (float)$s['focal_x'] : 50.0;
            $sFy = isset($s['focal_y']) ? (float)$s['focal_y'] : 50.0;
            $sZoom = isset($s['zoom']) ? (float)$s['zoom'] : 1.0;

            $sFx = max(0.0, min(100.0, $sFx));
            $sFy = max(0.0, min(100.0, $sFy));
            $sZoom = max(1.00, min(3.00, $sZoom));

            if ($sImg !== null && $sImg !== '') {
                $updateStmtWithImg->execute([
                    ':image' => $sImg,
                    ':focal_x' => $sFx,
                    ':focal_y' => $sFy,
                    ':zoom' => $sZoom,
                    ':id' => $sId
                ]);
            } else {
                $updateStmtNoImg->execute([
                    ':focal_x' => $sFx,
                    ':focal_y' => $sFy,
                    ':zoom' => $sZoom,
                    ':id' => $sId
                ]);
            }
            $updatedCount++;
        }

        $pdo->commit();
        echo json_encode([
            "success" => true,
            "message" => "All {$updatedCount} slot changes saved successfully.",
            "count" => $updatedCount
        ]);
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database batch update failed: " . $e->getMessage()]);
        exit();
    }
}

if (!$data || !isset($data['slot_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing slot_id or slots array."]);
    exit();
}

$slotId = trim((string)$data['slot_id']);
$image = isset($data['image']) ? trim((string)$data['image']) : null;
$focalX = isset($data['focal_x']) ? (float)$data['focal_x'] : 50.0;
$focalY = isset($data['focal_y']) ? (float)$data['focal_y'] : 50.0;
$zoom = isset($data['zoom']) ? (float)$data['zoom'] : 1.0;

// Clamp focal point to 0 - 100
$focalX = max(0.0, min(100.0, $focalX));
$focalY = max(0.0, min(100.0, $focalY));

// Clamp zoom to 1.00 - 3.00
$zoom = max(1.00, min(3.00, $zoom));

try {
    // If image is provided, update image, focal_x, focal_y, zoom
    if ($image !== null && $image !== '') {
        $stmt = $pdo->prepare("
            UPDATE catalog_slots
            SET image = :image, focal_x = :focal_x, focal_y = :focal_y, zoom = :zoom
            WHERE id = :id
        ");
        $stmt->execute([
            ':image' => $image,
            ':focal_x' => $focalX,
            ':focal_y' => $focalY,
            ':zoom' => $zoom,
            ':id' => $slotId
        ]);
    } else {
        // Only update adjustments
        $stmt = $pdo->prepare("
            UPDATE catalog_slots
            SET focal_x = :focal_x, focal_y = :focal_y, zoom = :zoom
            WHERE id = :id
        ");
        $stmt->execute([
            ':focal_x' => $focalX,
            ':focal_y' => $focalY,
            ':zoom' => $zoom,
            ':id' => $slotId
        ]);
    }

    if ($stmt->rowCount() > 0 || true) {
        // Fetch updated slot
        $fetchStmt = $pdo->prepare("SELECT id, page_key, section_id, image, focal_x, focal_y, zoom FROM catalog_slots WHERE id = :id");
        $fetchStmt->execute([':id' => $slotId]);
        $updatedSlot = $fetchStmt->fetch();

        echo json_encode([
            "success" => true,
            "message" => "Slot updated successfully",
            "slot" => $updatedSlot
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Slot not found or unchanged"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database update failed: " . $e->getMessage()]);
}

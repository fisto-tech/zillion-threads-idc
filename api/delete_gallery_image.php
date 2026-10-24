<?php
session_start();

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

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || !isset($data['filename'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing filename."]);
    exit();
}

// Sanitize filename to prevent directory traversal
$filename = basename(trim((string)$data['filename']));
if (empty($filename) || $filename === '.' || $filename === '..') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid filename."]);
    exit();
}

// Prevent deletion of critical defaults if desired (e.g. product-1.jpg)
$protectedFiles = ['product-1.jpg'];
if (in_array($filename, $protectedFiles)) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "This system image is protected and cannot be deleted."]);
    exit();
}

$dir = realpath(__DIR__ . '/../global assets/Images/products/');
if (!$dir) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Gallery directory not found."]);
    exit();
}

$targetPath = $dir . DIRECTORY_SEPARATOR . $filename;

// Ensure path is strictly within the products directory
if (strpos(realpath($targetPath) ?: $targetPath, $dir) !== 0) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized file path."]);
    exit();
}

if (!file_exists($targetPath)) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "File not found."]);
    exit();
}

if (@unlink($targetPath)) {
    echo json_encode(["success" => true, "message" => "Photo removed successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete file from disk."]);
}

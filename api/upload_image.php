<?php
session_start();

// CORS Header check - allow authorized origins
$allowedOrigins = ['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost:3000', 'http://localhost:5500', 'http://127.0.0.1:5500'];
$httpOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($httpOrigin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $httpOrigin");
    header("Access-Control-Allow-Credentials: true");
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No image uploaded or upload error occurred."]);
    exit();
}

$file = $_FILES['product_image'];

// Validate MIME type strictly from file content - remove SVG to prevent XSS / malicious scripts
$allowedMimeMap = [
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif'
];

$fileInfo = new finfo(FILEINFO_MIME_TYPE);
$detectedMime = $fileInfo->file($file['tmp_name']);

if (!isset($allowedMimeMap[$detectedMime])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid image type ($detectedMime). Only JPG, PNG, WEBP, and GIF are allowed."]);
    exit();
}

$safeExt = $allowedMimeMap[$detectedMime];

// Destination directory
$targetDir = __DIR__ . '/../global assets/Images/products/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Generate unique clean name using cryptographically secure random bytes
$cleanName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
$destination = $targetDir . $cleanName;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    // Relative web path from For_Desc/ or root
    $webPath = '../global assets/Images/products/' . $cleanName;
    echo json_encode([
        "success" => true,
        "message" => "Image uploaded successfully",
        "url" => $webPath,
        "filename" => $cleanName
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to save uploaded file."]);
}

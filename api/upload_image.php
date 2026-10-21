<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

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
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Only JPG, PNG, WEBP, and SVG images are supported."]);
    exit();
}

// Destination directory
$targetDir = __DIR__ . '/../global assets/Images/products/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Generate unique clean name
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if (!$ext) $ext = 'jpg';
$cleanName = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . strtolower($ext);
$destination = $targetDir . $cleanName;

if (move_uploaded_file($file['tmp_name'], $destination)) {
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

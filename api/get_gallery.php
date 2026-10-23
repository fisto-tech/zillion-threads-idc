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

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit();
}

$dir = __DIR__ . '/../global assets/Images/products/';
$files = [];

if (is_dir($dir)) {
    $scanned = scandir($dir);
    $validExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    foreach ($scanned as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . $file;
        if (is_file($path)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $validExts)) {
                $files[] = [
                    'name' => $file,
                    'url' => '../global assets/Images/products/' . $file,
                    'modified' => filemtime($path),
                    'size' => filesize($path)
                ];
            }
        }
    }

    // Sort newest modified first
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

echo json_encode(["success" => true, "images" => $files]);

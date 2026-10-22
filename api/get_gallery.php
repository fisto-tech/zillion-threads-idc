<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit();
}

$dir = __DIR__ . '/../global assets/Images/products/';
$files = [];

if (is_dir($dir)) {
    $scanned = scandir($dir);
    $validExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
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

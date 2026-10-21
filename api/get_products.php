<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$dataFile = __DIR__ . '/products.json';

// Return current products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($dataFile)) {
        echo file_get_contents($dataFile);
    } else {
        echo json_encode(["error" => "Data file not found"]);
    }
    exit();
}

// Update products (requires authentication check or secret)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if ($data && isset($data['page4']) && isset($data['page5'])) {
        $saved = file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($saved !== false) {
            echo json_encode(["success" => true, "message" => "Products saved successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Could not save to file"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid product structure payload"]);
    }
    exit();
}

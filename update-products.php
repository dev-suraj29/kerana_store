<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// =====================================
// SECURITY PASSWORD AUTHORIZATION KEY
// =====================================
$admin_password = "amit@1993";

// =====================================
// TARGET DATABASE CONFIGURATION
// =====================================
$jsonFile = __DIR__ . '/products.json';

// Ensure writable file permissions locally or on cloud storage platforms
if (file_exists($jsonFile)) {
    @chmod($jsonFile, 0664); 
}

// =====================================
// READ INCOMING TRANSMISSION DATA
// =====================================
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Malformed or empty JSON data package received."
    ]);
    exit;
}

// =====================================
// VERIFY ROUTE SECURITY KEY
// =====================================
if (!isset($data['password']) || $data['password'] !== $admin_password) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access: Key token mismatch validation error."
    ]);
    exit;
}

// =====================================
// CHECK DATA INTEGRITY NODE
// =====================================
if (!isset($data['products']) || !isset($data['categories'])) {
    echo json_encode([
        "success" => false,
        "message" => "Incomplete arrays: Products or categories block matrix missing."
    ]);
    exit;
}

// =====================================
// PREPARE & WRITE NEW SYSTEM MATRIX
// =====================================
$finalData = [
    "categories" => $data['categories'],
    "products" => $data['products']
];

$result = file_put_contents(
    $jsonFile,
    json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// =====================================
// RETURN STATUS TELEMETRY LOGS
// =====================================
if ($result !== false) {
    echo json_encode([
        "success" => true,
        "message" => "Kirana database updated and synchronized successfully!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to write data onto server filesystem disk storage module."
    ]);
}

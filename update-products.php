<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// =====================================
// SECURITY PASSWORD
// =====================================
$admin_password = "amit@1993";

// =====================================
// DATABASE FILE PATH (Root Directory File)
// =====================================
$jsonFile = dirname(__FILE__) . '/products.json';

// Forcefully attempt to unlock the root file permissions
if (file_exists($jsonFile)) {
    @chmod($jsonFile, 0664); 
}

// =====================================
// READ INPUT
// =====================================
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Malformed or empty JSON input package received."
    ]);
    exit;
}

// =====================================
// CHECK PASSWORD
// =====================================
if (!isset($data['password']) || $data['password'] !== $admin_password) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access: Key token mismatch."
    ]);
    exit;
}

// =====================================
// VALIDATE PRODUCTS DATA
// =====================================
if (!isset($data['products'])) {
    echo json_encode([
        "success" => false,
        "message" => "No product array node mapping found."
    ]);
    exit;
}

// =====================================
// PREPARE & WRITE FILE MATRIX
// =====================================
$finalData = [
    "categories" => $data['categories'] ?? [],
    "products" => $data['products']
];

$result = file_put_contents(
    $jsonFile,
    json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// =====================================
// RESPONSE TARGET PROTOCOLS
// =====================================
if ($result !== false) {
    echo json_encode([
        "success" => true,
        "message" => "Database updated and synchronized successfully!"
    ]);
} else {
    $error = error_get_last();
    echo json_encode([
        "success" => false,
        "message" => "Failed to write structural changes. Server says: " . ($error['message'] ?? 'Permissions Denied')
    ]);
}
?>
<?php
header("Content-Type: application/json");
session_start();
require_once '../../config.php';
require_once '../../helper.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['full_name']) && !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nothing to update."]);
    exit;
}

$userId = $_SESSION['user_id'];
$updates = [];
$params = [];

if (isset($data['full_name'])) {
    $updates[] = "full_name = ?";
    $params[] = sanitize($data['full_name']);
}

if (isset($data['email'])) {
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid email address."]);
        exit;
    }
    $updates[] = "email = ?";
    $params[] = $email;
}

$params[] = $userId;
$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute($params);
    echo json_encode(["success" => "Profile updated successfully."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Profile update failed."]);
}
?>

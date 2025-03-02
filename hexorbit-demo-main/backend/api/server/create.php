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
if (!isset($data['server_name'])) {
    http_response_code(400);
    echo json_encode(["error" => "Server name is required."]);
    exit;
}

$serverName = sanitize($data['server_name']);
$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO servers (server_name, created_by) VALUES (?, ?)");
    $stmt->execute([$serverName, $userId]);
    echo json_encode(["success" => "Server created successfully.", "server_id" => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create server."]);
}
?>

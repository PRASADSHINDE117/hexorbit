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
if (!isset($data['server_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Server ID is required to join a server."]);
    exit;
}

$serverId = intval($data['server_id']);
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM user_servers WHERE user_id = ? AND server_id = ?");
$stmt->execute([$userId, $serverId]);
if ($stmt->fetch()) {
    echo json_encode(["success" => "Already a member of this server."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO user_servers (user_id, server_id) VALUES (?, ?)");
    $stmt->execute([$userId, $serverId]);
    echo json_encode(["success" => "Joined the server successfully."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to join the server."]);
}
?>

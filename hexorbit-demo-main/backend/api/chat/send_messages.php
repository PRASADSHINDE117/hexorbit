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
if (!isset($data['channel_id'], $data['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Channel ID and message are required."]);
    exit;
}

$channelId = intval($data['channel_id']);
$message = sanitize($data['message']);
$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO messages (channel_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$channelId, $userId, $message]);
    echo json_encode(["success" => "Message sent.", "message_id" => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to send message."]);
}
?>

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

if (!isset($_GET['channel_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Channel ID is required."]);
    exit;
}

$channelId = intval($_GET['channel_id']);

$stmt = $pdo->prepare("SELECT m.id, m.message, m.sent_at, u.full_name 
    FROM messages m 
    JOIN users u ON m.user_id = u.id 
    WHERE m.channel_id = ? 
    ORDER BY m.sent_at ASC");
$stmt->execute([$channelId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["messages" => $messages]);
?>

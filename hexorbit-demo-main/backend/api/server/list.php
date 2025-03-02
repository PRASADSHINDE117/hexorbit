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

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT s.id, s.server_name, s.created_by, s.created_at FROM servers s 
    JOIN user_servers us ON s.id = us.server_id WHERE us.user_id = ?");
$stmt->execute([$userId]);
$servers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["servers" => $servers]);
?>

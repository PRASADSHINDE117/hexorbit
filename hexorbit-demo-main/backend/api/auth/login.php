<?php
header("Content-Type: application/json");
session_start();
require_once '../../config.php';
require_once '../../helper.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing credentials."]);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = $data['password'];

if (!$email) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email address."]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    echo json_encode([
        "success" => "Login successful.",
        "user" => [
            "id" => $user['id'],
            "full_name" => $user['full_name'],
            "email" => $email
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password."]);
}
?>

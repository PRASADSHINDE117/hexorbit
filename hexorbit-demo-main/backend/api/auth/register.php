<?php
header("Content-Type: application/json");
require_once '../../config.php';
require_once '../../helper.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['full_name'], $data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields."]);
    exit;
}

$full_name = sanitize($data['full_name']);
$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$password = $data['password'];

if (!$email) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email address."]);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Password must be at least 8 characters long."]);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$full_name, $email, $password_hash]);
    echo json_encode(["success" => "User registered successfully."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Registration failed. Possibly duplicate email."]);
}
?>

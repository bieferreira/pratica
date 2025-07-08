<?php
require 'db.php';
require 'jwt.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && $_GET['action'] === 'register') {
    $input = json_decode(file_get_contents("php://input"), true);
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$input['name'], $input['email'], password_hash($input['password'], PASSWORD_DEFAULT)]);
    echo json_encode(['message' => 'User registered']);
}

if ($method === 'POST' && $_GET['action'] === 'login') {
    $input = json_decode(file_get_contents("php://input"), true);
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($input['password'], $user['password'])) {
        $token = create_jwt(['id' => $user['id'], 'name' => $user['name']]);
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

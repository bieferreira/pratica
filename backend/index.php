<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json; charset=utf-8');

$request = $_GET['r'] ?? '';

if (empty($request)) {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
    exit;
}

switch ($request) {
    case 'login':
    case 'register':
        require 'auth.php';
        break;
    case 'tasks':
        require 'task.php';
        break;
}

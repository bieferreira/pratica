<?php
require 'db.php';
require 'jwt.php';

// ======== CORS E HEADERS ========
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// Responde requisição preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ======== OBTÉM HEADER DE AUTORIZAÇÃO (Robusto) ========
$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

if (!$authorization && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authorization = $headers['Authorization'] ?? null;
}

if (empty($authorization)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token não enviado']);
    exit;
}

// ======== VALIDA TOKEN JWT ========
$token = str_replace('Bearer ', '', $authorization);
$user = validate_jwt($token);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

// ======== PROCESSA MÉTODO ========
$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': 
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
        $stmt->execute([$user['id']]);
       
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });

        echo json_encode($data);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['title'], $data['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Título e descrição obrigatórios']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $data['title'], $data['description']]);
        echo json_encode(['message' => 'Tarefa criada']);
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da tarefa não informado']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, status=? WHERE id=? AND user_id=?");
        $stmt->execute([$data['title'], $data['description'], $data['status'], $_GET['id'], $user['id']]);
        echo json_encode(['message' => 'Tarefa atualizada']);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da tarefa não informado']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
        $stmt->execute([$_GET['id'], $user['id']]);
        echo json_encode(['message' => 'Tarefa excluída']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}

<?php
require 'db.php';
require 'jwt.php';

$headers = getallheaders();

echo 'A';
// echo '<pre>Headers recebidos:';
$authorization = null;
foreach ($headers as $key => $value) {
    // echo 'Header: ' . $key . ' => ' . $value ;
    if (strtolower($key) === 'authorization') {
        // echo '<br>(encontrado)';
        $authorization = $value;
        break;
    }
}
// echo '</pre>';
// exit;

if (empty($authorization)) {
    http_response_code(401); // 401 é o correto para "não autorizado"
    echo json_encode(['error' => 'Token não enviado']);
    exit;
}

$token = str_replace('Bearer ', '', $authorization);
$user = validate_jwt($token);


if (!$user) {
    http_response_code(401); exit('Token inválido');
    echo '<pre>Token recebido: ' . $authorization.'</pre>';
    exit;
}

$pdo = getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // echo $user['id'] . ' - ' . $user['name'];
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $data['title'], $data['description']]);
    echo json_encode(['message' => 'Tarefa criada']);
}

if ($method === 'PUT' && isset($_GET['id'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, status=? WHERE id=? AND user_id=?");
    $stmt->execute([$data['title'], $data['description'], $data['status'], $_GET['id'], $user['id']]);
    echo json_encode(['message' => 'Tarefa atualizada']);
}

if ($method === 'DELETE' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
    $stmt->execute([$_GET['id'], $user['id']]);
    echo json_encode(['message' => 'Tarefa excluída']);
}

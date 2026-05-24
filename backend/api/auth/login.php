<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $nombre_de_usuario = isset($data['nombre_de_usuario']) ? trim($data['nombre_de_usuario']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (empty($nombre_de_usuario) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'El nombre de usuario y la contraseña son obligatorios'
        ]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, nombre_de_usuario, password, foto FROM usuarios WHERE nombre_de_usuario = ?');
    $stmt->execute([$nombre_de_usuario]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($password, $usuario['password'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Credenciales inválidas'
        ]);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expiracion = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $stmt = $pdo->prepare('UPDATE usuarios SET token = ?, token_expiracion = ? WHERE id = ?');
    $stmt->execute([$token, $expiracion, $usuario['id']]);

    echo json_encode([
        'success' => true,
        'token' => $token,
        'usuario' => [
            'id' => (int) $usuario['id'],
            'nombre_de_usuario' => $usuario['nombre_de_usuario'],
            'foto' => $usuario['foto']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
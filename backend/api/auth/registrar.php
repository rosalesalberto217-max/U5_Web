<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $nombre_de_usuario = isset($data['nombre_de_usuario']) ? trim($data['nombre_de_usuario']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    if (empty($nombre_de_usuario) || strlen($nombre_de_usuario) < 3) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'El nombre de usuario debe tener al menos 3 caracteres'
        ]);
        exit;
    }

    if (empty($password) || strlen($password) < 6) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE nombre_de_usuario = ?');
    $stmt->execute([$nombre_de_usuario]);

    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'El nombre de usuario ya está registrado'
        ]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre_de_usuario, password) VALUES (?, ?)');
    $stmt->execute([$nombre_de_usuario, $hashedPassword]);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
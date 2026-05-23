<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de contacto es obligatorio'
        ]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM contactos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario['id']]);
    $contacto = $stmt->fetch();

    if (!$contacto) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Contacto no encontrado'
        ]);
        exit;
    }

    $contacto['id'] = (int) $contacto['id'];
    $contacto['usuario_id'] = (int) $contacto['usuario_id'];

    echo json_encode([
        'success' => true,
        'data' => $contacto
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
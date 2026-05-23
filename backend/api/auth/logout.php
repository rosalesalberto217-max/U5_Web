<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    $stmt = $pdo->prepare('UPDATE usuarios SET token = NULL, token_expiracion = NULL WHERE id = ?');
    $stmt->execute([$usuario['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}

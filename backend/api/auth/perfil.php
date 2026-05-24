<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    $stmt = $pdo->prepare('SELECT id, nombre_de_usuario, foto, fecha_registro FROM usuarios WHERE id = ?');
    $stmt->execute([$usuario['id']]);
    $perfil = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'data' => [
            'id' => (int) $perfil['id'],
            'nombre_de_usuario' => $perfil['nombre_de_usuario'],
            'foto' => $perfil['foto'],
            'fecha_registro' => $perfil['fecha_registro']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}

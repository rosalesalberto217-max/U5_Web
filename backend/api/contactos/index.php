<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);
    $usuario_id = $usuario['id'];

    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    if (!empty($buscar)) {
        $searchTerm = '%' . $buscar . '%';
        $stmt = $pdo->prepare(
            'SELECT * FROM contactos WHERE usuario_id = ? AND (nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ? OR email LIKE ?) ORDER BY nombre ASC'
        );
        $stmt->execute([$usuario_id, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM contactos WHERE usuario_id = ? ORDER BY nombre ASC');
        $stmt->execute([$usuario_id]);
    }

    $contactos = $stmt->fetchAll();

    // Cast id and usuario_id to int for consistency
    foreach ($contactos as &$contacto) {
        $contacto['id'] = (int) $contacto['id'];
        $contacto['usuario_id'] = (int) $contacto['usuario_id'];
    }

    echo json_encode([
        'success' => true,
        'data' => $contactos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
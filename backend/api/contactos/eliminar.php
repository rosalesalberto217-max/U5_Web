<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    $data = json_decode(file_get_contents('php://input'), true);

    $id = isset($data['id']) ? (int) $data['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de contacto es obligatorio'
        ]);
        exit;
    }

    // Verify contact belongs to user
    $stmt = $pdo->prepare('SELECT id, foto FROM contactos WHERE id = ? AND usuario_id = ?');
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

    // Delete photo file if exists
    if (!empty($contacto['foto'])) {
        $photoPath = __DIR__ . '/../' . $contacto['foto'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM contactos WHERE id = ? AND usuario_id = ?');
    $stmt->execute([$id, $usuario['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Contacto eliminado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
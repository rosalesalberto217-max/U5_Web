<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    // Read from multipart/form-data or JSON
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }

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

    $updates = [];
    $params = [];

    // Update fields if provided
    $fields = ['nombre', 'apellido', 'telefono', 'email', 'direccion', 'notas'];
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = trim($data[$field]);
        }
    }

    // Handle photo upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $fileType = mime_content_type($_FILES['foto']['tmp_name']);
        $fileSize = $_FILES['foto']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Tipo de archivo no permitido. Use: JPG, PNG, GIF o WEBP'
            ]);
            exit;
        }

        if ($fileSize > $maxSize) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'El archivo no debe superar los 5MB'
            ]);
            exit;
        }

        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('contact_', true) . '.' . strtolower($extension);
        $uploadDir = __DIR__ . '/../uploads/contactos/';
        $uploadPath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            // Delete old photo if exists
            if (!empty($contacto['foto'])) {
                $oldPhotoPath = __DIR__ . '/../' . $contacto['foto'];
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $updates[] = 'foto = ?';
            $params[] = 'uploads/contactos/' . $fileName;
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir la foto'
            ]);
            exit;
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No se proporcionaron datos para actualizar'
        ]);
        exit;
    }

    $params[] = $id;
    $params[] = $usuario['id'];
    $sql = 'UPDATE contactos SET ' . implode(', ', $updates) . ' WHERE id = ? AND usuario_id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'message' => 'Contacto actualizado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
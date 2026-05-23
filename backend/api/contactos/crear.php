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

    $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
    $telefono = isset($data['telefono']) ? trim($data['telefono']) : '';

    if (empty($nombre) || empty($telefono)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'El nombre y teléfono son obligatorios'
        ]);
        exit;
    }

    $apellido = isset($data['apellido']) ? trim($data['apellido']) : null;
    $email = isset($data['email']) ? trim($data['email']) : null;
    $direccion = isset($data['direccion']) ? trim($data['direccion']) : null;
    $notas = isset($data['notas']) ? trim($data['notas']) : null;
    $fotoPath = null;

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
            $fotoPath = 'uploads/contactos/' . $fileName;
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir la foto'
            ]);
            exit;
        }
    }

    $stmt = $pdo->prepare(
        'INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion, notas, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $usuario['id'],
        $nombre,
        $apellido,
        $telefono,
        $email,
        $direccion,
        $notas,
        $fotoPath
    ]);

    $newId = (int) $pdo->lastInsertId();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Contacto creado exitosamente',
        'data' => [
            'id' => $newId
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}
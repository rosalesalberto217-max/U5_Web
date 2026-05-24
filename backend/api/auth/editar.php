<?php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $usuario = validarToken($pdo);

    // Determine input source: JSON or multipart/form-data
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }

    $nombre_de_usuario = isset($data['nombre_de_usuario']) ? trim($data['nombre_de_usuario']) : null;
    $password = isset($data['password']) ? $data['password'] : null;

    $updates = [];
    $params = [];

    // Validate and set nombre_de_usuario
    if ($nombre_de_usuario !== null && $nombre_de_usuario !== '') {
        if (strlen($nombre_de_usuario) < 3) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de usuario debe tener al menos 3 caracteres'
            ]);
            exit;
        }

        // Check if username is taken by another user
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE nombre_de_usuario = ? AND id != ?');
        $stmt->execute([$nombre_de_usuario, $usuario['id']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'El nombre de usuario ya está en uso'
            ]);
            exit;
        }

        $updates[] = 'nombre_de_usuario = ?';
        $params[] = $nombre_de_usuario;
    }

    // Validate and set password
    if ($password !== null && $password !== '') {
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            exit;
        }

        $updates[] = 'password = ?';
        $params[] = password_hash($password, PASSWORD_DEFAULT);
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
        $fileName = uniqid('user_', true) . '.' . strtolower($extension);
        $uploadDir = __DIR__ . '/../uploads/usuarios/';
        $uploadPath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            // Delete old photo if exists
            if (!empty($usuario['foto'])) {
                $oldPhotoPath = __DIR__ . '/../' . $usuario['foto'];
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $updates[] = 'foto = ?';
            $params[] = 'uploads/usuarios/' . $fileName;
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

    $params[] = $usuario['id'];
    $sql = 'UPDATE usuarios SET ' . implode(', ', $updates) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Fetch updated user data
    $stmt = $pdo->prepare('SELECT id, nombre_de_usuario, foto FROM usuarios WHERE id = ?');
    $stmt->execute([$usuario['id']]);
    $updatedUser = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Perfil actualizado',
        'data' => [
            'id' => (int) $updatedUser['id'],
            'nombre_de_usuario' => $updatedUser['nombre_de_usuario'],
            'foto' => $updatedUser['foto']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
}

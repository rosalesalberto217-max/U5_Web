<?php

function validarToken($pdo) {
    $authHeader = null;

    // Try multiple sources for the Authorization header
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token inválido o expirado'
        ]);
        exit;
    }

    $token = $matches[1];

    try {
        $stmt = $pdo->prepare('SELECT id, nombre_de_usuario, foto FROM usuarios WHERE token = ? AND token_expiracion > NOW()');
        $stmt->execute([$token]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ]);
            exit;
        }

        return $usuario;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al validar el token'
        ]);
        exit;
    }
}
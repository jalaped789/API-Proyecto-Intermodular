<?php
// utilidades/jwt_validator.php

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

/**
 * Verifica el JWT en la cabecera Authorization y comprueba si tiene los roles requeridos.
 * * @param array $required_roles Array de roles que pueden acceder (ej: ['admin', 'user']).
 * @return array|null El array de datos del usuario ('id', 'email', 'rol') o null si falla.
 */
function validar_jwt_y_rol(array $required_roles = []): ?array
{
    // 1. Obtener la cabecera Authorization
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($auth_header) || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        http_response_code(401); 
        echo json_encode(["message" => "Acceso denegado. Se requiere Token Bearer."]);
        return null;
    }

    $token = $matches[1];

    try {
        // 2. Verificar el token (firma y caducidad)
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));
        $user_data = (array) $decoded->data; // Datos del usuario: id, email, rol

        // 3. Control de acceso: Verificar el rol
        if (!empty($required_roles) && !in_array($user_data['rol'], $required_roles)) {
            http_response_code(403); // 403 Forbidden
            echo json_encode(["message" => "Acceso denegado. Rol insuficiente. Requerido: " . implode(', ', $required_roles)]);
            return null;
        }

        // Token válido y Rol permitido
        return $user_data;

    } catch (ExpiredException $e) {
        http_response_code(401);
        echo json_encode(["message" => "Token expirado."]);
        return null;
    } catch (\Exception $e) {
        // Error de firma, token mal formado, etc.
        http_response_code(401);
        echo json_encode(["message" => "Token inválido."]);
        return null;
    }
}
?>
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

function verificarJWT(string $token): ?array {
    try {
        return (array) JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Verifica si el usuario tiene al menos uno de los roles permitidos
 */
function usuarioTieneRol(array $usuario, array $rolesPermitidos): bool {
    return isset($usuario['rol']) && in_array($usuario['rol'], $rolesPermitidos);
}

/**
 * Obtiene el usuario del JWT de la cabecera Authorization
 */
function obtenerUsuario(): ?array {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? null;
    if (!$token) return null;

    $token = str_replace("Bearer ", "", $token);
    return verificarJWT($token);
}

<?php
// Cargar Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Configuración y DB
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json; charset=utf-8');

// Leer JSON de entrada
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan email y password"]);
    exit;
}

$email = $input['email'];
$password = $input['password'];

try {
    $db = Database::getConnection();

    // Buscar usuario
    $stmt = $db->prepare("SELECT id, email, password_hash, rol FROM usuario WHERE email = :email LIMIT 1");
    $stmt->execute(["email" => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(401);
        echo json_encode(["error" => "Usuario incorrecto"]);
        exit;
    }

    // Verificar contraseña
    if (!password_verify($password, $usuario["password_hash"])) {
        http_response_code(401);
        echo json_encode(["error" => "Contraseña incorrecta"]);
        exit;
    }

    // Generar JWT
    $payload = [
        "id"    => $usuario["id"],
        "email" => $usuario["email"],
        "rol"   => $usuario["rol"], // admin o user
        "iat"   => time(),           // fecha de emisión
        "exp"   => time() + 3600     // caduca en 1h
    ];

    $token = JWT::encode($payload, JWT_SECRET, 'HS256');

    // Respuesta
    echo json_encode(["token" => $token], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor", "detalle" => $e->getMessage()]);
}

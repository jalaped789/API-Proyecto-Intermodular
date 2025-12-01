<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../controlador/imagen_controlador.php';
require_once __DIR__ . '/../modelo/imagen_modelo.php';


use Controlador\ImagenController;
use Modelo\Imagen;

// Inicializar conexión
$db = Database::getConnection();
$controller = new ImagenController($db);

// Obtener parámetros de la URL (enviados por .htaccess)
$uuid = $_GET['uuid'] ?? null;
$usuario = $_GET['usuario'] ?? null;

// pagination params (page is 1-based)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

// sanitize
if ($page < 1) $page = 1;
if ($limit < 1) $limit = 10;
// hard cap to avoid huge queries
$maxLimit = 100;
if ($limit > $maxLimit) $limit = $maxLimit;

// Detectar el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($uuid && $usuario) {
        $response = $controller->obtenerConUsuario($uuid);
    } elseif ($uuid) {
        $response = $controller->obtener($uuid);
    } elseif ($usuario) {
        $response = $controller->listarConUsuario($page, $limit);
    } else {
        $response = $controller->listar($page, $limit);
    }
}

elseif ($method === 'POST') {
    $response = $controller->crear();
}

elseif ($method === 'PUT' && $uuid) {
    $response = $controller->actualizar($uuid);
}

elseif ($method === 'DELETE' && $uuid) {
    $response = $controller->eliminar($uuid);
}

else {
    http_response_code(404);
    $response = ["error" => "Ruta no encontrada"];
}

// Enviar respuesta JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

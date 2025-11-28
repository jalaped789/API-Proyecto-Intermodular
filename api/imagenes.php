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

// Detectar el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($uuid && $usuario) {
        $response = $controller->obtenerConUsuario($uuid);
    } elseif ($uuid) {
        $response = $controller->obtener($uuid);
    } elseif ($usuario) {
        $response = $controller->listarConUsuario();
    } else {
        $response = $controller->listar();
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

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../controlador/imagen_controlador.php';
require_once __DIR__ . '/../modelo/imagen_modelo.php';
require_once __DIR__ . '/../config/auth.php';

use Controlador\ImagenController;

// Inicializar conexión
$db = Database::getConnection();
$controller = new ImagenController($db);

// Obtener parámetros de la URL
$uuid = $_GET['uuid'] ?? null;
$usuarioParam = $_GET['usuario'] ?? null;

// Paginación
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'id';
$order = strtoupper($_GET['order'] ?? 'ASC');

// Detectar método HTTP
$method = $_SERVER['REQUEST_METHOD'];

//Exportar
$export = $_GET['export'] ?? null;

if ($method === 'GET') {
    // GET es público
    if ($export) {
        $controller->exportar(search:$search, sort:$sort, order:$order);
        exit;
    }

    if ($uuid && $usuarioParam) {
        $response = $controller->obtenerConUsuario($uuid);
    } elseif ($uuid) {
        $response = $controller->obtener($uuid);
    } elseif ($usuarioParam) {
        $response = $controller->listarConUsuario($page, $limit);
    } else {
        $response = $controller->listar($page, $limit, $search, $sort, $order);
    }
}

elseif ($method === 'POST') {
    // POST requiere al menos rol "usuario"
    $usuario = obtenerUsuario();
    if (!$usuario) {
        http_response_code(401);
        $response = ["error" => "Token requerido"];
    } elseif (!usuarioTieneRol($usuario, ['usuario', 'admin'])) {
        http_response_code(403);
        $response = ["error" => "No tienes permiso para crear imágenes"];
    } else {
        $response = $controller->crear();
    }
}

elseif ($method === 'PUT' && $uuid) {
    // PUT solo admin
    $usuario = obtenerUsuario();
    if (!$usuario) {
        http_response_code(401);
        $response = ["error" => "Token requerido"];
    } elseif (!usuarioTieneRol($usuario, ['admin'])) {
        http_response_code(403);
        $response = ["error" => "Solo un admin puede modificar imágenes"];
    } else {
        $response = $controller->actualizar($uuid);
    }
}

elseif ($method === 'DELETE' && $uuid) {
    // DELETE solo admin
    $usuario = obtenerUsuario();
    if (!$usuario) {
        http_response_code(401);
        $response = ["error" => "Token requerido"];
    } elseif (!usuarioTieneRol($usuario, ['admin'])) {
        http_response_code(403);
        $response = ["error" => "Solo un admin puede eliminar imágenes"];
    } else {
        $response = $controller->eliminar($uuid);
    }
}

else {
    http_response_code(404);
    $response = ["error" => "Ruta no encontrada"];
}

// Enviar respuesta JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
<?php
namespace Controlador;

use Modelo\Usuario;
use PDO;

class UsuarioController
{
    private Usuario $modelo;

    public function __construct(\PDO $db)
    {
        $this->modelo = new Usuario($db);
    }

    // ========================================
    // Métodos públicos ahora devuelven arrays
    // ========================================

    public function listar(int $page = 1, int $limit = 10, string $search = '', string $sort = 'id', string $order = 'ASC'): array
    {
        // Ajustar límites
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $maxLimit = 100;
        if ($limit > $maxLimit) $limit = $maxLimit;

        $offset = ($page - 1) * $limit;

        $data = $this->modelo->getUsuarios($limit, $offset, $search, $sort, $order);
        $total = $this->modelo->countUsuarios();

        return [
            'data' => $data,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => (int) ceil($total / $limit)
            ]
        ];
    }

    public function obtener($id): array
    {
        $usuario = $this->modelo->getUsuario($id);
        if (!$usuario) {
            return ["error" => "Usuario no encontrado"];
        }
        return $usuario;
    }

    public function crear(): array
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["username"], $input["password"])) {
            return ["error" => "Faltan campos obligatorios"];
        }

        $email = $input["email"] ?? null;
        $nombre   = $input["nombre"] ?? null;
        $apellidos   = $input["apellidos"] ?? null;
        $foto_perfil   = $input["foto_perfil"] ?? null;
        $descripcion   = $input["descripcion"] ?? null;
        $rol   = $input["rol"] ?? null;

        $ok = $this->modelo->insertar(
            $input["username"],
            $email,
            $input["password"],
            $nombre,
            $apellidos,
            $foto_perfil,
            $descripcion,
            $rol
        );

        if ($ok) {
            return ["mensaje" => "Usuario creado correctamente"];
        } else {
            return ["error" => "No se pudo crear el usuario"];
        }
    }

    public function actualizar($id): array
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["username"], $input["password"])) {
            return ["error" => "Faltan campos obligatorios"];
        }

        $email = $input["email"] ?? null;
        $nombre   = $input["nombre"] ?? null;
        $apellidos   = $input["apellidos"] ?? null;
        $foto_perfil   = $input["foto_perfil"] ?? null;
        $descripcion   = $input["descripcion"] ?? null;
        $rol   = $input["rol"] ?? null;

        $ok = $this->modelo->modificar(
            $id,
            $input["username"],
            $email,
            $input["password"],
            $nombre,
            $apellidos,
            $foto_perfil,
            $descripcion,
            $rol
        );

        if ($ok) {
            return ["mensaje" => "Usuario modificado correctamente"];
        } else {
            return ["error" => "Error al modificar usuario"];
        }
    }

    public function eliminar($id): array
    {
        $ok = $this->modelo->eliminar($id);

        if ($ok) {
            return ["mensaje" => "Usuario eliminado correctamente"];
        } else {
            return ["error" => "No se pudo eliminar el usuario"];
        }
    }

    public function exportar(string $search = '', string $sort = 'id', string $order = 'ASC'): array
    {
        // Obtener datos con filtros
        $data = $this->modelo->getUsuarios(null, null, $search, $sort, $order);

        if (empty($data)) {
            return ["error" => "No hay imágenes para exportar"];
        }

        $output = fopen('php://output', 'w');
        // Cabeceras
        fputcsv($output, array_keys($data[0]));

        // Datos
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        return [
            'success' => "Se han exportado " . count($data) . " usuarios"
        ];
    }
}

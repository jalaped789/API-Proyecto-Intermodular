<?php
namespace Controlador;

use Modelo\Imagen;
use PDO;

class ImagenController
{
    private Imagen $modelo;

    public function __construct(\PDO $db)
    {
        $this->modelo = new Imagen($db);
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

        $data = $this->modelo->getImagenes($limit, $offset, $search, $sort, $order);
        $total = $this->modelo->countImagenes();

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

    public function listarConUsuario(int $page = 1, int $limit = 10): array
    {
        // Ajustar límites
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $maxLimit = 100;
        if ($limit > $maxLimit) $limit = $maxLimit;

        $offset = ($page - 1) * $limit;

        $data = $this->modelo->getImagenesConUsuario($limit, $offset);
        $total = $this->modelo->countImagenes();

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

    public function obtener($uuid): array
    {
        $imagen = $this->modelo->getImagen($uuid);
        if (!$imagen) {
            return ["error" => "Imagen no encontrada"];
        }
        return $imagen;
    }

    public function obtenerConUsuario($uuid): array
    {
        $imagen = $this->modelo->getImagenConUsuario($uuid);
        if (!$imagen) {
            return ["error" => "Imagen no encontrada"];
        }
        return $imagen;
    }

    public function crear(): array
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["url_imagen"], $input["texto"])) {
            return ["error" => "Faltan campos obligatorios"];
        }

        $usuario_id = $input["usuario_id"] ?? null;
        $grupo_id   = $input["grupo_id"] ?? null;

        $ok = $this->modelo->insertar(
            $usuario_id,
            $grupo_id,
            $input["url_imagen"],
            $input["texto"]
        );

        if ($ok) {
            return ["mensaje" => "Imagen creada correctamente"];
        } else {
            return ["error" => "No se pudo crear la imagen"];
        }
    }

    public function actualizar($uuid): array
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["url_imagen"], $input["texto"])) {
            return ["error" => "Faltan campos obligatorios"];
        }

        $ok = $this->modelo->modificar(
            $uuid,
            $input["url_imagen"],
            $input["texto"]
        );

        if ($ok) {
            return ["mensaje" => "Imagen modificada correctamente"];
        } else {
            return ["error" => "Error al modificar imagen"];
        }
    }

    public function eliminar($uuid): array
    {
        $ok = $this->modelo->eliminar($uuid);

        if ($ok) {
            return ["mensaje" => "Imagen eliminada correctamente"];
        } else {
            return ["error" => "No se pudo eliminar la imagen"];
        }
    }

    public function exportar(string $search = '', string $sort = 'id', string $order = 'ASC'): array
    {
        // Obtener datos con filtros
        $data = $this->modelo->getImagenes(null, null, $search, $sort, $order);

        if (empty($data)) {
            return ["error" => "No hay imágenes para exportar"];
        }

        // Ruta al directorio padre del proyecto
        $nombreArchivo = 'imagenes_exportadas_' . date('Y-m-d_H-i') . '.csv';
        $rutaArchivo = __DIR__ . '../../storage/exports/' . $nombreArchivo;

        // Abrir archivo para escritura
        $output = fopen($rutaArchivo, 'w');

        // Cabeceras
        fputcsv($output, array_keys($data[0]));

        // Datos
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        return [
            'success' => "Se han exportado " . count($data) . " imágenes",
            'ruta' => realpath($rutaArchivo)
        ];
    }
}

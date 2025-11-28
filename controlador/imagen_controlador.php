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

    public function listar(): array
    {
        return $this->modelo->getImagenes();
    }

    public function listarConUsuario(): array
    {
        return $this->modelo->getImagenesConUsuario();
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
}

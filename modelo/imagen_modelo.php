<?php

class Imagen
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getImagenes(): array
    {
        $sql = "SELECT * FROM imagen";
        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getImagenesConUsuario(): array
    {
        $sql = "SELECT i.*, u.username FROM imagen i LEFT JOIN imagen i ON i.usuario_id = u.id";
        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getImagen(int $uuid): array
    {
        $sql = "SELECT * FROM imagen WHERE uuid = :uuid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uuid', $uuid);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function getImagenConUsuario(string $uuid): array
    {
        $sql = "SELECT i.*, u.username FROM imagen i LEFT JOIN imagen i ON i.usuario_id = u.id WHERE i.uuid = :uuid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uuid', $uuid);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function insertar(?int $usuario_id, ?int $grupo_id, string $url_imagen, string $texto): bool
    {
        $sql = "INSERT INTO imagen (uuid, usuario_id, grupo_id, url_imagen, texto)
                VALUES (UUID(), :usuario_id, :grupo_id, :url_imagen, :texto)";

        $stmt = $this->db->prepare($sql);

        // Enlazar parámetros
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':grupo_id',  $grupo_id);
        $stmt->bindParam(':url_imagen',  $url_imagen);
        $stmt->bindParam(':texto',  $texto);

        // Ejecutar consulta
        return $stmt->execute();
    }

    public function modificar(string $uuid, string $url_imagen, string $texto): bool
    {
        $sql = "UPDATE imagen SET url_imagen = :url_imagen, texto = :texto + 1 WHERE uuid = :uuid";

        $stmt = $this->db->prepare($sql);

        // Enlazar parámetros
        $stmt->bindParam(':url_imagen', $url_imagen);
        $stmt->bindParam(':texto',  $texto);
        $stmt->bindParam(':uuid',  $uuid);

        // Ejecutar consulta
        return $stmt->execute();
    }

        function eliminar(string $uuid){
        try {
            $sql = "DELETE FROM imagen WHERE uuid = :uuid";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':uuid', $uuid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    }
}

<?php
namespace Modelo;

use PDO;

class Imagen
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getImagenes(?int $limit = null, ?int $offset = null, string $search = '', string $sort = 'id', string $order = 'ASC'): array
    {
        $sql = "SELECT * FROM imagen WHERE texto LIKE :search ORDER BY :sort :order";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->bindValue(':sort', $sort);
        $stmt->bindValue(':order', $order);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getImagenesConUsuario(?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT i.*, u.username 
                FROM imagen i 
                LEFT JOIN usuario u ON i.usuario_id = u.id";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countImagenes(): int
    {
        $sql = "SELECT COUNT(*) AS c FROM imagen";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['c'] ?? 0);
    }

    public function getImagen(string $uuid): array
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
        $sql = "SELECT i.*, u.username 
                FROM imagen i 
                LEFT JOIN usuario u ON i.usuario_id = u.id
                WHERE i.uuid = :uuid";

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

        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':grupo_id', $grupo_id);
        $stmt->bindParam(':url_imagen', $url_imagen);
        $stmt->bindParam(':texto', $texto);

        return $stmt->execute();
    }

    public function modificar(string $uuid, string $url_imagen, string $texto): bool
    {
        $sql = "UPDATE imagen 
                SET url_imagen = :url_imagen, texto = :texto
                WHERE uuid = :uuid";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':url_imagen', $url_imagen);
        $stmt->bindParam(':texto', $texto);
        $stmt->bindParam(':uuid', $uuid);

        return $stmt->execute();
    }

    public function eliminar(string $uuid): bool
    {
        $sql = "DELETE FROM imagen WHERE uuid = :uuid";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([":uuid" => $uuid]);
    }
}

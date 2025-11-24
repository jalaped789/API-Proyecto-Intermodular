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

    
    public function getImagenConUsuario(int $uuid): array
    {
        $sql = "SELECT i.*, u.username FROM imagen i LEFT JOIN imagen i ON i.usuario_id = u.id WHERE i.uuid = :uuid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uuid', $uuid);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        return $stmt->fetchAll();
    }
}

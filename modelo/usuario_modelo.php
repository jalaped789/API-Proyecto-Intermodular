<?php
namespace Modelo;

use PDO;

class Usuario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Busca un usuario por email y verifica la contraseña.
     * @param string $email
     * @param string $password
     * @return array|null Datos del usuario (id, email, password_hash, rol) o null
     */
    public function verificarCredenciales(string $email, string $password): ?array
    {
        $sql = "SELECT id, email, password_hash, rol FROM usuario WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario_db && password_verify($password, $usuario_db['password_hash'])) {
            // Credenciales válidas
            unset($usuario_db['password_hash']); // Nunca devolver el hash
            return $usuario_db;
        }

        return null; // Credenciales inválidas o usuario no encontrado
    }

     public function getUsuarios(?int $limit = null, ?int $offset = null, string $search = '', string $sort = 'id', string $order = 'ASC'): array
    {
        $sql = "SELECT * FROM usuario WHERE username LIKE :search ORDER BY :sort :order";

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

    public function countUsuarios(): int
    {
        $sql = "SELECT COUNT(*) AS c FROM usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['c'] ?? 0);
    }

    public function getUsuario(string $id): array
    {
        $sql = "SELECT * FROM usuario WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function insertar(string $username, ?string $email, string $password, ?string $nombre, ?string $apellidos, ?string $foto_perfil, ?string $descripcion, ?string $rol): bool
    {
        $sql = "INSERT INTO usuario (username, email, password_hash, nombre, apellidos, foto_perfil, descripcion, rol)
                VALUES (:username, :email, :password_hash, :nombre, :apellidos, :foto_perfil, :descripcion, :rol)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':foto_perfil', $foto_perfil);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':rol', $rol);

        return $stmt->execute();
    }

    public function modificar(int $id, string $username, ?string $email, string $password, ?string $nombre, ?string $apellidos, ?string $foto_perfil, ?string $descripcion, ?string $rol): bool
    {
        $sql = "UPDATE usuario 
                SET username = :username, 
                    email = :email, 
                    password_hash = :password_hash, 
                    nombre = :nombre, 
                    apellidos = :apellidos, 
                    foto_perfil = :foto_perfil, 
                    descripcion = :descripcion, 
                    rol = :rol
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':foto_perfil', $foto_perfil);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':rol', $rol);

        return $stmt->execute();
    }

    public function eliminar(string $id): bool
    {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([":id" => $id]);
    }
}
?>
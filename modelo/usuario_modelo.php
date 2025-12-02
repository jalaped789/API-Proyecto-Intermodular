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
}
?>
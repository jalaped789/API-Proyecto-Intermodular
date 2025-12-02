<?php
namespace Controlador;

use Modelo\Usuario;
use PDO;

// Asume que tienes instalado composer y autoload está disponible
require_once __DIR__ . '/../vendor/autoload.php'; 
use Firebase\JWT\JWT;

class AuthController
{
    private Usuario $usuario_modelo;

    public function __construct(PDO $db)
    {
        $this->usuario_modelo = new Usuario($db);
    }

    /**
     * Procesa la solicitud de login, verifica credenciales y genera un JWT.
     * @param string $email
     * @param string $password
     * @return void Envía la respuesta JSON y el código HTTP.
     */
    public function login(string $email, string $password): void
    {
        // 1. Comprueba la tabla usuarios
        $usuario = $this->usuario_modelo->verificarCredenciales($email, $password);

        if ($usuario) {
            // 2. Genera el JWT
            $time = time();

            // Payload: id, email, rol, iat, exp (según el requisito)
            $payload = [
                "iat" => $time, // fecha de emisión
                "exp" => $time + JWT_EXPIRATION_TIME, // fecha de caducidad (1 hora)
                "data" => [ 
                    "id" => $usuario['id'],
                    "email" => $usuario['email'],
                    "rol" => $usuario['rol']
                ]
            ];

            $token = JWT::encode($payload, JWT_SECRET_KEY, JWT_ALGORITHM);

            // 3. Devuelve el token en un JSON
            http_response_code(200);
            echo json_encode(["token" => $token]);
            return;

        } else {
            // Credenciales inválidas
            http_response_code(401); // 401 Unauthorized
            echo json_encode(["message" => "Credenciales inválidas."]);
            return;
        }
    }
}
?>
<?php
class Database
{
    public static function getConnection(): PDO
    {
        return new PDO(
            DB_DSN,
            DB_USER, 
            DB_PASS,
            [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
        );
    }
}
?>
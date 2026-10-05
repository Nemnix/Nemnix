<?php

class Conexion {
    private static function instancia(): ConectorPDO
    {
        return ConectorPDO::obtenerInstancia($_ENV['DB_HOST'], (int) $_ENV['DB_PUERTO'], $_ENV['DB_USUARIO'], $_ENV['DB_CLAVE'], $_ENV['DB_NOMBRE']);
    }

    public static function conectar(): PDO
    {
        try {
            return self::instancia()->establecerConexion();
        } catch (PDOException $error) {
            error_log("Error de conexión: " . $error->getMessage());
            RespuestaJson::error("Error de conexión con la base de datos", 500);
        }
    }

    public static function desconectar(): void
    {
        self::instancia()->desconectar();
    }
}
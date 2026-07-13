<?php

/**
 * Clase que simula una recuperación de credenciales correspondientes a la base de datos.
 */
class ConsultaUsuario {
    /**
     * Simula la recuperación de un usuario desde una base de datos.
     *
     * Más adelante, el contenido de esta función será reemplazado
     * por una consulta mediante PDO.
     */
    public function buscarUsuario(string $cedula): ?Usuario {
        if ($cedula == "11111111"){
        $datos = [
            "cedula" => "11111111",
            "claveHash" => password_hash("clave1234567", PASSWORD_DEFAULT),
            "activo" => true,
            "administrador" => true,
            "tecnicos" => false,
            "solicitante" => false
            ];

            return new Usuario(
            $datos["cedula"],
            $datos["claveHash"],
            $datos["activo"],
            $datos["administrador"],
            $datos["tecnico"],
            $datos["solicitante"]
            );
        
        }elseif ($cedula == "22222222"){
        $datos = [
            "cedula" => "22222222",
            "claveHash" => password_hash("hola123456789", PASSWORD_DEFAULT),
            "activo" => true,
            "administrador" => false,
            "tecnico" => true,
            "solicitante" => false
        ];
            return new Usuario (
            $datos["cedula"],
            $datos["claveHash"],
            $datos["activo"],
            $datos["administrador"],
            $datos["tecnico"],
            $datos["solicitante"]
        );
        
        }elseif ($cedula == "33333333"){
        $datos = [
            "cedula" => "33333333",
            "claveHash" => password_hash("chau123456789", PASSWORD_DEFAULT),
            "activo" => true,
            "administrador" => false,
            "tecnico" => false,
            "solicitante" => true
        ];
        return new Usuario (
            $datos["cedula"],
            $datos["claveHash"],
            $datos["activo"],
            $datos["administrador"],
            $datos["tecnico"],
            $datos["solicitante"]
        );

        }else {
            return null;
        }

        
    }
}

?>
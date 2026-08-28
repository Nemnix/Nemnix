<?php

/**
 * Clase encargada de realizar operaciones de alta
 * relacionadas con los usuarios del sistema.
 */
class AltaDatosUsuario {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Registra un nuevo usuario con su rol.
     *
     * @param string $cedula Cédula del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $apellido Apellido del usuario.
     * @param string $claveHash Hash de la contraseña.
     * @param string $rol Rol del usuario en el sistema (Administrador, Tecnico o Solicitante).
     *
     * @return bool TRUE si el registro se completa correctamente, FALSE en caso contrario.
     */
    public function registrarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = "INSERT INTO USUARIO (cedula, nombre, apellido, claveHash) VALUES (:cedula, :nombre, :apellido, :claveHash)";

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);

            $consultaUsuario->execute(["cedula" => $cedula, "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash]);

            switch ($rol) {
                case "Administrador":
                    $sqlRol = "INSERT INTO ADMINISTRADOR (cedula) VALUES (:cedula)";
                    break;
                case "Tecnico":
                    $sqlRol = "INSERT INTO TECNICO (cedula) VALUES (:cedula)";
                    break;
                case "Solicitante":
                    $sqlRol = "INSERT INTO SOLICITANTE (cedula) VALUES (:cedula)";
                    break;
                default:
                    $this->conexion->rollBack();
                    return false;
            }

            $consultaRol = $this->conexion->prepare($sqlRol);

            $consultaRol->execute(["cedula" => $cedula]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log("Error en AltaDatosUsuario: " . $error->getMessage());

            return false;
        }
    }
}

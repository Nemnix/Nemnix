<?php


class AccesoDatosUsuario {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

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

public function buscarUsuario(string $cedula): ?Usuario {
        $sql = "
         SELECT
        u.cedula,
        u.nombre,
        u.apellido,
        u.claveHash,
        u.activo,

        CASE
            WHEN a.cedula IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS administrador,

        CASE
            WHEN t.cedula IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS tecnico,

        CASE
            WHEN s.cedula IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS solicitante

    FROM usuario AS u

    LEFT JOIN administrador AS a
        ON a.cedula = u.cedula

    LEFT JOIN tecnico AS t
        ON t.cedula = u.cedula

    LEFT JOIN solicitante AS s
        ON s.cedula = u.cedula

    WHERE u.cedula = :cedula
";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute(["cedula" => $cedula]);

        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($datos === false) {
            return null;
        }

        return new Usuario(
            $datos["cedula"],
            $datos["nombre"],
            $datos["apellido"],
            $datos["claveHash"],
            (bool) $datos["activo"],
            (bool) $datos["administrador"],
            (bool) $datos["tecnico"],
            (bool) $datos ["solicitante"]
        );
    }

    /**
     * Lista todos los usuarios del sistema junto con su cargo.
     * @return array Los usuarios con cedula, nombre, apellido y cargo.
     */
    public function listarUsuarios(): array
    {
        $sql = "
    SELECT
        u.cedula,
        u.nombre,
        u.apellido,

        CASE
            WHEN a.cedula IS NOT NULL THEN 'Administrador'
            WHEN t.cedula IS NOT NULL THEN 'Tecnico'
            WHEN s.cedula IS NOT NULL THEN 'Solicitante'
            ELSE 'Sin rol'
        END AS cargo

    FROM usuario AS u

    LEFT JOIN administrador AS a
        ON a.cedula = u.cedula

    LEFT JOIN tecnico AS t
        ON t.cedula = u.cedula

    LEFT JOIN solicitante AS s
        ON s.cedula = u.cedula

    ORDER BY u.nombre, u.apellido
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    
}

public function modificarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, apellido = :apellido, claveHash = :claveHash WHERE cedula = :cedula";
            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute(["nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash, "cedula" => $cedula]);

            switch ($rol) {
                case "Administrador":
                    $tablaRol = "ADMINISTRADOR";
                    break;
                case "Tecnico":
                    $tablaRol = "TECNICO";
                    break;
                case "Solicitante":
                    $tablaRol = "SOLICITANTE";
                    break;
                default:
                    $this->conexion->rollBack();
                    return false;
            }

            // Quita al usuario de los otros roles, ya que este panel solo permite un rol activo a la vez.
            foreach (["ADMINISTRADOR", "TECNICO", "SOLICITANTE"] as $tabla) {
                if ($tabla === $tablaRol) {
                    continue;
                }

                $consultaQuitarRol = $this->conexion->prepare("DELETE FROM $tabla WHERE cedula = :cedula");
                $consultaQuitarRol->execute(["cedula" => $cedula]);
            }

            $consultaBuscarRol = $this->conexion->prepare("SELECT cedula FROM $tablaRol WHERE cedula = :cedula");
            $consultaBuscarRol->execute(["cedula" => $cedula]);

            if ($consultaBuscarRol->fetch(PDO::FETCH_ASSOC) === false) {
                $consultaAsignarRol = $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)");
                $consultaAsignarRol->execute(["cedula" => $cedula]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log("Error en ModificarDatosUsuario: " . $error->getMessage());

            return false;
        }
    }
     public function eliminarUsuario(string $cedula): bool {

        try {
            $this->conexion->beginTransaction();

            $sqlAdministrador = "DELETE FROM ADMINISTRADOR WHERE cedula = :cedula";
            $consultaAdministrador = $this->conexion->prepare($sqlAdministrador);
            $consultaAdministrador->execute(["cedula" => $cedula]);

            $sqlTecnico = "DELETE FROM TECNICO WHERE cedula = :cedula";
            $consultaTecnico = $this->conexion->prepare($sqlTecnico);
            $consultaTecnico->execute(["cedula" => $cedula]);

            $sqlSolicitante = "DELETE FROM SOLICITANTE WHERE cedula = :cedula";
            $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
            $consultaSolicitante->execute(["cedula" => $cedula]);

            $sqlUsuario = "DELETE FROM USUARIO WHERE cedula = :cedula";
            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute(["cedula" => $cedula]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            error_log("Error en BajaDatosUsuario: " . $error->getMessage());

            return false;
        }
    }
}
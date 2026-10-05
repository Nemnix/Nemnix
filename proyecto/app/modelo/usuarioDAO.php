<?php


class AccesoDatosUsuario {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

      public function registrarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol, ?string $gmail): bool {

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
                return false;
        }

        try {
            $this->conexion->beginTransaction();

            // Se busca si la cédula ya existe (activa o dada de baja).
            $consultaExiste = $this->conexion->prepare("SELECT activo FROM USUARIO WHERE cedula = :cedula");
            $consultaExiste->execute(["cedula" => $cedula]);
            $activo = $consultaExiste->fetchColumn();

            if ($activo === false) {
                // No existe: se crea el usuario.
                $sqlUsuario = "INSERT INTO USUARIO (cedula, nombre, apellido, claveHash, activo) VALUES (:cedula, :nombre, :apellido, :claveHash, 1)";
            } elseif ((int) $activo === 0) {
                // Existe dado de baja: se reactiva con los datos nuevos.
                $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, apellido = :apellido, claveHash = :claveHash, activo = 1 WHERE cedula = :cedula";
            } else {
                // Existe y está activo: es un duplicado.
                $this->conexion->rollBack();
                return false;
            }

            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute(["cedula" => $cedula, "nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash]);

            // Quita los roles que el usuario tenía antes de la baja.
            foreach (["ADMINISTRADOR", "TECNICO", "SOLICITANTE"] as $tabla) {
                if ($tabla === $tablaRol) {
                    continue;
                }

                $consultaQuitarRol = $this->conexion->prepare("DELETE FROM $tabla WHERE cedula = :cedula");
                $consultaQuitarRol->execute(["cedula" => $cedula]);
            }

            // Asigna el rol nuevo si todavía no lo tiene.
            $consultaBuscarRol = $this->conexion->prepare("SELECT cedula FROM $tablaRol WHERE cedula = :cedula");
            $consultaBuscarRol->execute(["cedula" => $cedula]);

            if ($consultaBuscarRol->fetch(PDO::FETCH_ASSOC) === false) {
                if ($tablaRol === "SOLICITANTE") {
                    // El solicitante guarda además su gmail.
                    $consultaAsignarRol = $this->conexion->prepare("INSERT INTO SOLICITANTE (cedula, gmail) VALUES (:cedula, :gmail)");
                    $consultaAsignarRol->execute(["cedula" => $cedula, "gmail" => $gmail]);
                } else {
                    $consultaAsignarRol = $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)");
                    $consultaAsignarRol->execute(["cedula" => $cedula]);
                }
            } elseif ($tablaRol === "SOLICITANTE") {
                // Ya era solicitante: se actualiza su gmail.
                $consultaGmail = $this->conexion->prepare("UPDATE SOLICITANTE SET gmail = :gmail WHERE cedula = :cedula");
                $consultaGmail->execute(["gmail" => $gmail, "cedula" => $cedula]);
            }

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
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

    FROM USUARIO AS u

    LEFT JOIN ADMINISTRADOR AS a
        ON a.cedula = u.cedula

    LEFT JOIN TECNICO AS t
        ON t.cedula = u.cedula

    LEFT JOIN SOLICITANTE AS s
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
     * Lista los usuarios activos del sistema junto con su cargo.
     * @return array Los usuarios con cedula, nombre, apellido y cargo.
     * @throws PDOException Si la consulta no puede ejecutarse.
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
        END AS cargo,

        s.gmail

    FROM USUARIO AS u

    LEFT JOIN ADMINISTRADOR AS a
        ON a.cedula = u.cedula

    LEFT JOIN TECNICO AS t
        ON t.cedula = u.cedula

    LEFT JOIN SOLICITANTE AS s
        ON s.cedula = u.cedula

    WHERE u.activo = 1

    ORDER BY u.nombre, u.apellido
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    
}

public function modificarUsuario(string $cedula, string $nombre, string $apellido, ?string $claveHash, string $rol, ?string $gmail): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se modifican usuarios existentes y activos.
            $consultaExiste = $this->conexion->prepare("SELECT activo FROM USUARIO WHERE cedula = :cedula");
            $consultaExiste->execute(["cedula" => $cedula]);

            if ((int) $consultaExiste->fetchColumn() !== 1) {
                $this->conexion->rollBack();
                return false;
            }


            // Solo actualiza la clave si se ingresó una nueva.
            if ($claveHash === null) {
                $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, apellido = :apellido WHERE cedula = :cedula";
                $parametros = ["nombre" => $nombre, "apellido" => $apellido, "cedula" => $cedula];
            } else {
                $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, apellido = :apellido, claveHash = :claveHash WHERE cedula = :cedula";
                $parametros = ["nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash, "cedula" => $cedula];
            }
            $consultaUsuario = $this->conexion->prepare($sqlUsuario);
            $consultaUsuario->execute($parametros);

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
                if ($tablaRol === "SOLICITANTE") {
                    // El solicitante guarda además su gmail.
                    $consultaAsignarRol = $this->conexion->prepare("INSERT INTO SOLICITANTE (cedula, gmail) VALUES (:cedula, :gmail)");
                    $consultaAsignarRol->execute(["cedula" => $cedula, "gmail" => $gmail]);
                } else {
                    $consultaAsignarRol = $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)");
                    $consultaAsignarRol->execute(["cedula" => $cedula]);
                }
            } elseif ($tablaRol === "SOLICITANTE") {
                // Ya era solicitante: se actualiza su gmail.
                $consultaGmail = $this->conexion->prepare("UPDATE SOLICITANTE SET gmail = :gmail WHERE cedula = :cedula");
                $consultaGmail->execute(["gmail" => $gmail, "cedula" => $cedula]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }
     public function eliminarUsuario(string $cedula): bool {

        // Baja lógica: el usuario queda inactivo y conserva sus roles e historial.
        // Devuelve false si la cédula no existe o el usuario ya estaba dado de baja.
        $sqlUsuario = "UPDATE USUARIO SET activo = 0 WHERE cedula = :cedula AND activo = 1";
        $consultaUsuario = $this->conexion->prepare($sqlUsuario);
        $consultaUsuario->execute(["cedula" => $cedula]);

        return $consultaUsuario->rowCount() > 0;
    }
}
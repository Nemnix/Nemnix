<?php


class UsuarioDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

      public function registrarUsuario(Usuario $usuario): bool {
        // Se toman los datos del objeto.
        $cedula = $usuario->getCedula();
        $nombre = $usuario->getNombre();
        $apellido = $usuario->getApellido();
        $claveHash = $usuario->getClaveHash();

        // El usuario debe tener al menos un rol.
        if (!$usuario->esAdministrador() && !$usuario->esTecnico() && !$usuario->esSolicitante()) {
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

            // Deja al usuario solo con los roles elegidos (quita los que tenía antes de la baja).
            $this->sincronizarRoles($usuario);

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
        END AS solicitante,

        s.gmail

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
            (bool) $datos ["solicitante"],
            $datos["gmail"]
        );
    }

    /**
     * Lista los usuarios activos del sistema junto con sus roles.
     * @return array Los usuarios con cedula, nombre, apellido, un indicador por rol y gmail.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarUsuarios(): array
    {
        $sql = "
    SELECT
        u.cedula,
        u.nombre,
        u.apellido,

        a.cedula IS NOT NULL AS administrador,
        t.cedula IS NOT NULL AS tecnico,
        s.cedula IS NOT NULL AS solicitante,

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

public function modificarUsuario(Usuario $usuario): bool {
        // Se toman los datos del objeto.
        $cedula = $usuario->getCedula();
        $nombre = $usuario->getNombre();
        $apellido = $usuario->getApellido();
        $claveHash = $usuario->getClaveHash();

        // El usuario debe tener al menos un rol.
        if (!$usuario->esAdministrador() && !$usuario->esTecnico() && !$usuario->esSolicitante()) {
            return false;
        }

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

            // Deja al usuario solo con los roles elegidos.
            $this->sincronizarRoles($usuario);

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

    /**
     * Agrega al usuario los roles que tiene marcados y le quita los demás.
     * Debe llamarse dentro de una transacción abierta.
     * @param Usuario $usuario Usuario con los roles y el gmail a guardar.
     * @throws PDOException Si alguna consulta no puede ejecutarse.
     */
    private function sincronizarRoles(Usuario $usuario): void {
        $cedula = $usuario->getCedula();
        $roles = [
            "ADMINISTRADOR" => $usuario->esAdministrador(),
            "TECNICO" => $usuario->esTecnico(),
            "SOLICITANTE" => $usuario->esSolicitante()
        ];

        foreach ($roles as $tabla => $tieneRol) {
            $consultaBuscarRol = $this->conexion->prepare("SELECT cedula FROM $tabla WHERE cedula = :cedula");
            $consultaBuscarRol->execute(["cedula" => $cedula]);
            $yaTieneRol = $consultaBuscarRol->fetch(PDO::FETCH_ASSOC) !== false;

            if (!$tieneRol) {
                if ($yaTieneRol) {
                    $consultaQuitarRol = $this->conexion->prepare("DELETE FROM $tabla WHERE cedula = :cedula");
                    $consultaQuitarRol->execute(["cedula" => $cedula]);
                }
            } elseif ($tabla === "SOLICITANTE") {
                // El solicitante guarda además su gmail.
                $sqlSolicitante = $yaTieneRol
                    ? "UPDATE SOLICITANTE SET gmail = :gmail WHERE cedula = :cedula"
                    : "INSERT INTO SOLICITANTE (cedula, gmail) VALUES (:cedula, :gmail)";
                $consultaSolicitante = $this->conexion->prepare($sqlSolicitante);
                $consultaSolicitante->execute(["cedula" => $cedula, "gmail" => $usuario->getGmail()]);
            } elseif (!$yaTieneRol) {
                $consultaAsignarRol = $this->conexion->prepare("INSERT INTO $tabla (cedula) VALUES (:cedula)");
                $consultaAsignarRol->execute(["cedula" => $cedula]);
            }
        }
    }
}

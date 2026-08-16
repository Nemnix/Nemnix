<?php

/**
 * Clase que simula una recuperación de credenciales correspondientes a la base de datos.
 */
class AccesoDatosUsuario {
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct (PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Busca un usuario por su cédula y determina el rol.
     * @param string $cedula La cedula del usuario sin puntos ni guiones.
     * @return Usuario|null Los datos del usuario, retorna su objeto si existe, null en caso contrario.
     */
    public function buscarUsuario(string $cedula): ?Usuario
    {
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


   
}


?>
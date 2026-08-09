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


    public function listarDiagnosticos (): array {
        $sql = "
        SELECT
        d.ID_D,
        d.ID_T,
        d.ID_R,
        d.fechaD,
        d.estadoD,
        d.descripcionD,

        CASE
            WHEN r.ID_R IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS registro_diario,

        CASE
            WHEN t.ID_T IS NOT NULL THEN TRUE
            ELSE FALSE
        END AS ticket

        FROM DIAGNOSTICO AS d


        LEFT JOIN TICKET AS t
            ON t.ID_R = d.ID_R
            AND t.ID_T = d.ID_T

        LEFT JOIN REGISTRO_DIARIO AS r
            ON r.ID_R = d.ID_R";

        $consulta = $this->conexion->query($sql);

        $diagnosticos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        

        return $diagnosticos;
    }


    /*public function verDiagnostico (int $idR, int $idT): ?array {
        $sql = "
        SELECT
    d.ID_D,
    d.ID_T,
    d.ID_R,
    d.fechaD,
    d.estadoD,
    d.descripcionD,

    CASE
        WHEN r.ID_R IS NOT NULL THEN TRUE
        ELSE FALSE
        END AS registro_diario,

    CASE
        WHEN t.ID_T IS NOT NULL THEN TRUE
        ELSE FALSE
        END AS ticket

    FROM DIAGNOSTICO AS d


    LEFT JOIN TICKET AS t
        ON t.ID_R = d.ID_R
        AND t.ID_T = d.ID_T

    LEFT JOIN REGISTRO_DIARIO AS r
        ON r.ID_R = d.ID_R

    WHERE d.ID_R = :idR AND d.ID_T = :idT";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute(['idR' => $idR, 'idT' => $idT]);

        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($datos === false) {
            return null;
        }
        return $datos;

    }*/
}


?>
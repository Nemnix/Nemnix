<?php
 class AccesoSolicitudes{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

 public function listarSolicitudes (): array {
        $sql = "
        SELECT
        l.nombreLab,
        l.numeroLab,
        s.fecha,
        s.descripcionSolicitud,
        s.gmail

        FROM SOLICITA AS s
        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = s.ID_L";

        $consulta = $this->conexion->query($sql);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        

        return $solicitudes;
    }
 }
?>
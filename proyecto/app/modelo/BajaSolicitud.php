<?php

class BajaSolicitud{
private PDO $conexion;

/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
public function __construct (PDO $conexion) {
$this->conexion = $conexion;
    }

/**
     * Elimina una solicitud de la tabla SOLICITA a partir del identificador
     * de laboratorio y la cedula del solicitante asociado.
     * @param int $idL El identificador del laboratorio.
     * @param string $cedula La cedula del solicitante.
     * @return bool true si se elimino el registro, false en caso contrario.
     */
public function eliminarSolicitud (int $idL, string $cedula): bool {
$sql = "DELETE FROM SOLICITA WHERE ID_L = :idL AND cedula = :cedula";

$consulta = $this->conexion->prepare($sql);

$consulta->execute([
    'idL' => $idL,
    'cedula' => $cedula
]);

return $consulta->rowCount() > 0;
    }
 }

?>
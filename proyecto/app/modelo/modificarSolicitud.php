<?php

class ModificarSolicitud{
private PDO $conexion;

/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
public function __construct (PDO $conexion) {
$this->conexion = $conexion;
    }

/**
     * Actualiza los datos de una solicitud de la tabla SOLICITA, identificada
     * por el laboratorio y la cedula del solicitante actualmente asociados.
     * @param int $idLActual El identificador de laboratorio actual de la solicitud.
     * @param string $cedula La cedula del solicitante asociado.
     * @param int $idLNuevo El identificador del laboratorio a asociar.
     * @param string $descripcionSolicitud La descripcion de la solicitud.
     * @param string $fecha La fecha de la solicitud.
     * @param string $gmail El gmail de contacto de la solicitud.
     * @return bool true si se modifico el registro, false en caso contrario.
     */
public function actualizarSolicitud (int $idLActual, string $cedula, int $idLNuevo, string $descripcionSolicitud, string $fecha, string $gmail): bool {
$sql = "UPDATE SOLICITA
        SET
            ID_L = :idLNuevo,
            descripcionSolicitud = :descripcionSolicitud,
            Fecha = :fecha,
            Gmail = :gmail
        WHERE ID_L = :idLActual AND cedula = :cedula";

$consulta = $this->conexion->prepare($sql);

$consulta->execute([
    'idLNuevo' => $idLNuevo,
    'descripcionSolicitud' => $descripcionSolicitud,
    'fecha' => $fecha,
    'gmail' => $gmail,
    'idLActual' => $idLActual,
    'cedula' => $cedula
]);

return $consulta->rowCount() > 0;
    }
 }

?>
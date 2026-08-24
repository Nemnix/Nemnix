<?php

class ModificarDiagnostico{
private PDO $conexion;

/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
public function __construct (PDO $conexion) {
$this->conexion = $conexion;
    }

/**
     * Actualiza el estado, la fecha y la descripcion de un diagnostico
     * de la tabla DIAGNOSTICO, a partir de su identificador.
     * @param int $idD El identificador del diagnostico.
     * @param string $estadoD El estado del diagnostico.
     * @param string $fechaD La fecha del diagnostico.
     * @param string $descripcionD La descripcion del diagnostico.
     * @return bool true si se modifico el registro, false en caso contrario.
     */
public function actualizarDiagnostico (int $idD, string $estadoD, string $fechaD, string $descripcionD): bool {
$sql = "UPDATE DIAGNOSTICO
        SET
            estadoD = :estadoD,
            fechaD = :fechaD,
            descripcionD = :descripcionD
        WHERE ID_D = :idD";

$consulta = $this->conexion->prepare($sql);

$consulta->execute([
    'estadoD' => $estadoD,
    'fechaD' => $fechaD,
    'descripcionD' => $descripcionD,
    'idD' => $idD
]);

return $consulta->rowCount() > 0;
    }
 }

?>
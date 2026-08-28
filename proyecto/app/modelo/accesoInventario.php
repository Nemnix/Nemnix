<?php

/**
 * Proporciona las operaciones de consulta de la tabla LABORATORIO.
 */
class AccesoInventario{
    private PDO $conexion;

    /**
         * Constructor parametrizado que recibe una conexión a la base de datos.
         * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
         */
    public function __construct (PDO $conexion) {
    $this->conexion = $conexion;
        }

    /**
         * Lista todos los registros de la tabla LABORATORIO.
         * @return array Los registros de laboratorio, con todos sus atributos.
         */
    public function listarInventarios (): array {
    $sql = " SELECT
                l.ID_L,
                l.nombreLaboratorio,
                l.numLaboratorio,
                e.cantMonitor,
                e.cantPC,
                e.cantTeclado,
                e.cantMouse
            FROM LABORATORIO AS l
            INNER JOIN EQUIPO AS e
                ON e.ID_L = l.ID_L";

    $consulta = $this->conexion->query($sql);

    $inventarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    return $inventarios;
        }

    public function modificarInventario (int $id, string $nombreLaboratorio, string $numLaboratorio, int $cantMonitores, int $cantTeclados, int $cantMouses, int $cantComputadoras): bool {
    try {
                $this->conexion->beginTransaction();

                $consultaEquipo = $this->conexion->prepare(
                    "UPDATE EQUIPO SET cantMonitor = :cantMonitores, cantPC = :cantComputadoras, cantTeclado = :cantTeclados, cantMouse = :cantMouses WHERE ID_L = :id"
                );
                $consultaEquipo->execute([
                    "cantMonitores" => $cantMonitores,
                    "cantComputadoras" => $cantComputadoras,
                    "cantTeclados" => $cantTeclados,
                    "cantMouses" => $cantMouses,
                    "id" => $id
                ]);

                $consultaLaboratorio = $this->conexion->prepare(
                    "UPDATE LABORATORIO SET nombreLaboratorio = :nombreLaboratorio, numLaboratorio = :numLaboratorio WHERE ID_L = :id"
                );
                $consultaLaboratorio->execute([
                    "nombreLaboratorio" => $nombreLaboratorio,
                    "numLaboratorio" => $numLaboratorio,
                    "id" => $id
                ]);



                $this->conexion->commit();
                return true;
            } catch (PDOException $error) {
                if ($this->conexion->inTransaction()) {
                    $this->conexion->rollBack();
                }
                error_log("Error al modificar el inventario: " . $error->getMessage());
                return false;
            }
    }
}

?>
<?php


/**
 * Ejecuta operaciones de baja para las solicitudes.
 */
class BajaSolicitudUS
{
/**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
private PDO $conexion;


/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
public function __construct(PDO $conexion)
    {
$this->conexion = $conexion;
    }


/**
     * Elimina una solicitud de la tabla SOLICITA.
     *
     * La solicitud se identifica por el laboratorio y la cédula del
     * solicitante asociados.
     *
     * @param string $idLaboratorio Identificador del laboratorio.
     * @param string $cedula Cédula del solicitante.
     * @return bool TRUE si la operación se completa correctamente; FALSE si falla.
     */
public function eliminarSolicitud(string $idLaboratorio, string $cedula): bool
    {

try {
$this->conexion->beginTransaction();

$consultaSolicitud = $this->conexion->prepare(
"DELETE FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula"
            );
$consultaSolicitud->execute([
"idLaboratorio" => $idLaboratorio,
"cedula" => $cedula
            ]);

if ($consultaSolicitud->rowCount() === 0) {
$this->conexion->rollBack();
return false;
            }

$this->conexion->commit();
return true;

        } catch (PDOException $error) {
if ($this->conexion->inTransaction()) {
$this->conexion->rollBack();
            }

return false;
        }
    }
}
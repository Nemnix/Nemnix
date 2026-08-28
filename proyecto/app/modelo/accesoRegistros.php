<?php

/**
 * Proporciona las operaciones de consulta y modificación de préstamos.
 */
class AccesoRegistros
{
    /**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;

    /**
     * Construye el acceso a los datos usando una conexión existente.
     *
     * @param PDO $conexion Conexión activa con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los préstamos junto con los datos del alumno y del equipo.
     *
     * @return array<int, array<string, mixed>> Lista de préstamos encontrados.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarRegistros(): array
    {
        $sql = "
    SELECT
        r.ID_R,
        r.fecha,
        l.nombreLaboratorio,
        l.numLaboratorio,
        r.horaEntrada,
        r.horaFin,
        r.grupo,
        r.asignatura,
        r.turno
    FROM REGISTRO_DIARIO AS r
    INNER JOIN LABORATORIO AS l
        ON l.ID_L = r.ID_L";


        $consulta = $this->conexion->query($sql);

        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);


        return $registros;
    }

    /**
     * Modifica los datos del registro diario y del laboratorio asociado.
     *
     * Todas las actualizaciones se ejecutan dentro de una única transacción.
     * Si una operación falla, se revierten los cambios realizados.
     *
     * @param string $idRegistro Identificador del registro diario.
     * @param string $nombreLaboratorio Tipo de aula actualizado (Laboratorio/Taller).
     * @param string $numLaboratorio Número de aula actualizado.
     * @param string $grupo Grupo actualizado.
     * @param string $horaEntrada Hora de entrada actualizada.
     * @param string $horaFin Hora de salida actualizada.
     * @param string $asignatura Asignatura actualizada.
     * @param string $fecha Fecha actualizada en formato válido para SQL.
     * @param string $turno Turno actualizado.
     * @return bool TRUE si la modificación se confirma; FALSE si ocurre un error.
     */
    public function modificarRegistro(string $idRegistro, string $nombreLaboratorio, string $numLaboratorio, string $grupo, string $horaEntrada, string $horaFin, string $asignatura, string $fecha, string $turno): bool
    {
        try {
            $this->conexion->beginTransaction();

            $consultaIdLaboratorio = $this->conexion->prepare(
                "SELECT ID_L FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
            );
            $consultaIdLaboratorio->execute(["idRegistro" => $idRegistro]);
            $idLaboratorio = $consultaIdLaboratorio->fetchColumn();

            if ($idLaboratorio === false) {
                $this->conexion->rollBack();
                return false;
            }

            $consultaLaboratorio = $this->conexion->prepare(
                "UPDATE LABORATORIO SET nombreLaboratorio = :nombreLaboratorio, numLaboratorio = :numLaboratorio WHERE ID_L = :idLaboratorio"
            );
            $consultaLaboratorio->execute([
                "nombreLaboratorio" => $nombreLaboratorio,
                "numLaboratorio" => $numLaboratorio,
                "idLaboratorio" => $idLaboratorio
            ]);

            $consultaRegistro = $this->conexion->prepare(
                "UPDATE REGISTRO_DIARIO SET grupo = :grupo, horaEntrada = :horaEntrada, horaFin = :horaFin, asignatura = :asignatura, fecha = :fecha, turno = :turno WHERE ID_R = :idRegistro"
            );
            $consultaRegistro->execute([
                "grupo" => $grupo,
                "horaEntrada" => $horaEntrada,
                "horaFin" => $horaFin,
                "asignatura" => $asignatura,
                "fecha" => $fecha,
                "turno" => $turno,
                "idRegistro" => $idRegistro
            ]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log("Error al modificar el registro: " . $error->getMessage());
            return false;
        }
    }

 }
    ?>
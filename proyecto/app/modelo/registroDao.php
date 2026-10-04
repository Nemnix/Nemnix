<?php

/**
 * Proporciona las operaciones de consulta y modificación de préstamos.
 */
class RegistrosDao
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
    public function registrarRegistro(string $cedula, string $grupo, string $horaInicio, string $horaFin, string $asignatura, string $fecha, string $nombreLab, string $numLab, string $turno): bool
    {
        if (empty($cedula) || empty($grupo) || empty($horaInicio) || empty($horaFin) || empty($asignatura) || empty($fecha) || empty($nombreLab) || empty($numLab) || empty($turno)) {
            $this->ultimoError = "Todos los campos son obligatorios.";
            return false;
        }

        try {
            $this->conexion->beginTransaction();

            // 1) Insertar primero el laboratorio para obtener su ID_L
            $sqlLaboratorio = "INSERT INTO LABORATORIO (nombreLaboratorio, numLaboratorio) VALUES (:nombreLaboratorio, :numLaboratorio)";
            $consultaLaboratorio = $this->conexion->prepare($sqlLaboratorio);
            $consultaLaboratorio->execute([
                "nombreLaboratorio" => $nombreLab,
                "numLaboratorio" => $numLab,
            ]);

            $idLaboratorio = $this->conexion->lastInsertId();

            // 2) Insertar el registro diario usando el ID_L recién generado
            $sqlRegistro = "INSERT INTO REGISTRO_DIARIO (ID_L, cedula, grupo, horaEntrada, horaFin, asignatura, fecha, turno) VALUES (:ID_L, :cedula, :grupo, :horaInicio, :horaFin, :asignatura, :fecha, :turno)";
            $consultaRegistro = $this->conexion->prepare($sqlRegistro);
            $consultaRegistro->execute([
                "ID_L" => $idLaboratorio,
                "cedula" => $cedula,
                "grupo" => $grupo,
                "horaInicio" => $horaInicio,
                "horaFin" => $horaFin,
                "asignatura" => $asignatura,
                "fecha" => $fecha,
                "turno" => $turno
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            $this->ultimoError = $error->getMessage();
            error_log("Error en AltaRegistro: " . $this->ultimoError);

            return false;
        }
    }
    public function eliminarRegistro(string $idRegistro): bool
    {

        try {
            $this->conexion->beginTransaction();

            $consultaLaboratorio = $this->conexion->prepare(
                "SELECT ID_L FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
            );
            $consultaLaboratorio->execute(["idRegistro" => $idRegistro]);
            $idLaboratorio = $consultaLaboratorio->fetchColumn();

            if ($idLaboratorio === false) {
                $this->conexion->rollBack();
                return false;
            }
            $consultaRegistro = $this->conexion->prepare(
                "DELETE FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
            );
            $consultaRegistro->execute(["idRegistro" => $idRegistro]);

            $consultaOtrosRegistros = $this->conexion->prepare(
                "SELECT COUNT(*) FROM REGISTRO_DIARIO WHERE ID_L = :idLaboratorio"
            );
            $consultaOtrosRegistros->execute(["idLaboratorio" => $idLaboratorio]);

            if ((int) $consultaOtrosRegistros->fetchColumn() === 0) {
                $consultaLaboratorio = $this->conexion->prepare(
                    "DELETE FROM LABORATORIO WHERE ID_L = :idLaboratorio"
                );
                $consultaLaboratorio->execute(["idLaboratorio" => $idLaboratorio]);
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
    ?>
<?php


class LaboratorioDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista todos los laboratorios con su equipamiento.
     * Los laboratorios sin fila en EQUIPO se muestran con cantidades en 0.
     * @return array Los laboratorios con ID_L, nombre, número y cantidades.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarLaboratorios(): array
    {
        $sql = "
    SELECT
        l.ID_L,
        l.nombreLaboratorio,
        l.numLaboratorio,
        COALESCE(e.cantMonitor, 0) AS cantMonitores,
        COALESCE(e.cantTeclado, 0) AS cantTeclados,
        COALESCE(e.cantMouse, 0) AS cantMouses,
        COALESCE(e.cantPC, 0) AS cantComputadoras

    FROM LABORATORIO AS l

    LEFT JOIN EQUIPO AS e
        ON e.ID_L = l.ID_L

    ORDER BY l.nombreLaboratorio, l.numLaboratorio
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarLaboratorio(string $nombreLaboratorio, int $numLaboratorio, int $cantMonitores, int $cantTeclados, int $cantMouses, int $cantComputadoras): bool {
        try {
            $this->conexion->beginTransaction();

            // Se busca si ya existe un laboratorio con el mismo tipo y número.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM LABORATORIO WHERE nombreLaboratorio = :nombreLaboratorio AND numLaboratorio = :numLaboratorio");
            $consultaExiste->execute(["nombreLaboratorio" => $nombreLaboratorio, "numLaboratorio" => $numLaboratorio]);

            if ($consultaExiste->fetchColumn() !== false) {
                // Existe: es un duplicado.
                $this->conexion->rollBack();
                return false;
            }

            $sqlLaboratorio = "INSERT INTO LABORATORIO (nombreLaboratorio, numLaboratorio) VALUES (:nombreLaboratorio, :numLaboratorio)";
            $consultaLaboratorio = $this->conexion->prepare($sqlLaboratorio);
            $consultaLaboratorio->execute(["nombreLaboratorio" => $nombreLaboratorio, "numLaboratorio" => $numLaboratorio]);

            $idLaboratorio = $this->conexion->lastInsertId();

            $sqlEquipo = "INSERT INTO EQUIPO (ID_L, cantMonitor, cantPC, cantTeclado, cantMouse) VALUES (:idLaboratorio, :cantMonitores, :cantComputadoras, :cantTeclados, :cantMouses)";
            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute([
                "idLaboratorio" => $idLaboratorio,
                "cantMonitores" => $cantMonitores,
                "cantComputadoras" => $cantComputadoras,
                "cantTeclados" => $cantTeclados,
                "cantMouses" => $cantMouses
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    public function modificarLaboratorio(int $idLaboratorio, string $nombreLaboratorio, int $numLaboratorio, int $cantMonitores, int $cantTeclados, int $cantMouses, int $cantComputadoras): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se modifican laboratorios existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM LABORATORIO WHERE ID_L = :idLaboratorio");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorio]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlLaboratorio = "UPDATE LABORATORIO SET nombreLaboratorio = :nombreLaboratorio, numLaboratorio = :numLaboratorio WHERE ID_L = :idLaboratorio";
            $consultaLaboratorio = $this->conexion->prepare($sqlLaboratorio);
            $consultaLaboratorio->execute(["nombreLaboratorio" => $nombreLaboratorio, "numLaboratorio" => $numLaboratorio, "idLaboratorio" => $idLaboratorio]);

            $parametrosEquipo = [
                "idLaboratorio" => $idLaboratorio,
                "cantMonitores" => $cantMonitores,
                "cantComputadoras" => $cantComputadoras,
                "cantTeclados" => $cantTeclados,
                "cantMouses" => $cantMouses
            ];

            // Si el laboratorio todavía no tiene equipo cargado, se crea la fila en EQUIPO.
            $consultaBuscarEquipo = $this->conexion->prepare("SELECT 1 FROM EQUIPO WHERE ID_L = :idLaboratorio");
            $consultaBuscarEquipo->execute(["idLaboratorio" => $idLaboratorio]);

            if ($consultaBuscarEquipo->fetchColumn() === false) {
                $sqlEquipo = "INSERT INTO EQUIPO (ID_L, cantMonitor, cantPC, cantTeclado, cantMouse) VALUES (:idLaboratorio, :cantMonitores, :cantComputadoras, :cantTeclados, :cantMouses)";
            } else {
                $sqlEquipo = "UPDATE EQUIPO SET cantMonitor = :cantMonitores, cantPC = :cantComputadoras, cantTeclado = :cantTeclados, cantMouse = :cantMouses WHERE ID_L = :idLaboratorio";
            }
            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute($parametrosEquipo);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    /**
     * Busca el identificador de un laboratorio por su tipo y número.
     * @param string $nombreLaboratorio Tipo de aula (Laboratorio o Taller).
     * @param int $numLaboratorio Número del laboratorio.
     * @return int|null El ID_L del laboratorio, o NULL si no existe.
     */
    public function buscarIdLaboratorio(string $nombreLaboratorio, int $numLaboratorio): ?int {
        $sql = "SELECT ID_L FROM LABORATORIO WHERE nombreLaboratorio = :nombreLaboratorio AND numLaboratorio = :numLaboratorio";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["nombreLaboratorio" => $nombreLaboratorio, "numLaboratorio" => $numLaboratorio]);

        $idLaboratorio = $consulta->fetchColumn();

        if ($idLaboratorio === false) {
            return null;
        }

        return (int) $idLaboratorio;
    }

    /**
     * Indica si otro laboratorio ya tiene el mismo tipo y número.
     * @param int $idLaboratorio Identificador del laboratorio que se está modificando (se excluye de la búsqueda).
     * @param string $nombreLaboratorio Tipo de aula (Laboratorio o Taller).
     * @param int $numLaboratorio Número del laboratorio.
     * @return bool TRUE si existe otro laboratorio con ese tipo y número.
     */
    public function existeOtroLaboratorio(int $idLaboratorio, string $nombreLaboratorio, int $numLaboratorio): bool {
        $sql = "SELECT 1 FROM LABORATORIO WHERE nombreLaboratorio = :nombreLaboratorio AND numLaboratorio = :numLaboratorio AND ID_L <> :idLaboratorio";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["nombreLaboratorio" => $nombreLaboratorio, "numLaboratorio" => $numLaboratorio, "idLaboratorio" => $idLaboratorio]);

        return $consulta->fetchColumn() !== false;
    }

    /**
     * Indica si el laboratorio tiene registros diarios o solicitudes asociados.
     * @param int $idLaboratorio Identificador del laboratorio.
     * @return bool TRUE si tiene registros o solicitudes.
     */
    public function tieneDependencias(int $idLaboratorio): bool {
        $sql = "
    SELECT
        (SELECT COUNT(*) FROM REGISTRO_DIARIO WHERE ID_L = :idRegistro) +
        (SELECT COUNT(*) FROM SOLICITA WHERE ID_L = :idSolicitud) AS total
";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idRegistro" => $idLaboratorio, "idSolicitud" => $idLaboratorio]);

        return (int) $consulta->fetchColumn() > 0;
    }

    public function eliminarLaboratorio(int $idLaboratorio): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan laboratorios existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM LABORATORIO WHERE ID_L = :idLaboratorio");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorio]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Se borran primero las tablas que dependen del laboratorio.
            foreach (["EQUIPO", "GESTIONA", "LABORATORIO"] as $tabla) {
                $consultaEliminar = $this->conexion->prepare("DELETE FROM $tabla WHERE ID_L = :idLaboratorio");
                $consultaEliminar->execute(["idLaboratorio" => $idLaboratorio]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }
}

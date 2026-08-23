<?php

/**
 * Clase encargada de registrar nuevos diagnósticos en la base de datos.
 */
class AltaDiagnostico
{
    private PDO $conexion;
    private string $ultimoError = "";

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Inserta un nuevo diagnóstico en la tabla DIAGNOSTICO.
     * @param int|null $idT Identificador del ticket asociado (puede ser NULL).
     * @param int|null $idR Identificador de registro diario (puede ser NULL).
     * @param string $estadoD Estado del diagnóstico.
     * @param string $fechaD Fecha del diagnóstico (YYYY-MM-DD).
     * @param string $descripcionD Descripción del diagnóstico.
     * @return bool True si se insertó correctamente, false en caso contrario.
     */
    public function registrarDiagnostico(?int $idT, ?int $idR, string $estadoD, string $fechaD, string $descripcionD): bool
    {
        if (empty($estadoD) || empty($fechaD) || empty($descripcionD)) {
            $this->ultimoError = "Campos obligatorios faltantes";
            return false;
        }

        try {
            $sql = "INSERT INTO DIAGNOSTICO (ID_R, ID_T, estadoD, fechaD, descripcionD) VALUES (:idR, :idT, :estadoD, :fechaD, :descripcionD)";
            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                'idR' => $idR,
                'idT' => $idT,
                'estadoD' => $estadoD,
                'fechaD' => $fechaD,
                'descripcionD' => $descripcionD
            ]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log("Error en AltaDiagnostico: " . $this->ultimoError);
            return false;
        }
    }

    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}

?>

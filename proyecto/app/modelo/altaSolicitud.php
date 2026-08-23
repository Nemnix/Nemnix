<?php

/**
 * Clase encargada de realizar operaciones de alta
 * relacionadas con las solicitudes del sistema.
 */
class AltaSolicitud
{
    /**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;

    /**
     * Último error producido durante el registro de una solicitud.
     *
     * @var string
     */
    private string $ultimoError = "";

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra una nueva solicitud con su laboratorio asociado.
     *
      * @param string $cedula Cédula del solicitante.
      * @param string $nombreLab Tipo de laboratorio.
      * @param string $numeroLab Número del laboratorio.
      * @param string $fechaSolicitud Fecha solicitada.
      * @param string $descripcionSolicitud Descripción de la solicitud.
      * @param string $gmailDocente Correo del docente.
     *
     * @return bool TRUE si el registro se completa correctamente, FALSE en caso contrario.
     */
    public function registrarSolicitud(string $cedula, string $nombreLab, string $numeroLab, string $fechaSolicitud, string $descripcionSolicitud, string $gmailDocente): bool
    {
        // Validar que los campos obligatorios no estén vacíos
        if (empty($nombreLab) || empty($numeroLab) || empty($fechaSolicitud) || empty($descripcionSolicitud) || empty($gmailDocente)) {
            $this->ultimoError = "Todos los campos son obligatorios.";
            return false;
        }

        try {
            $consultaSolicitante = $this->conexion->prepare(
                "SELECT 1 FROM SOLICITANTE WHERE cedula = :cedula"
            );
            $consultaSolicitante->execute(["cedula" => $cedula]);

            if ($consultaSolicitante->fetchColumn() === false) {
                $this->ultimoError = "La cédula del solicitante no existe en la tabla SOLICITANTE. Cierre sesión e inicie sesión con un solicitante registrado.";
                return false;
            }

            //Método que ejecuta de forma agrupada todas las instrucciones dirigidas a la base de datos
            //Si una instrucción falla, retorna excepción con la posibilidad de deshacer cambios con rollBack()
            $this->conexion->beginTransaction();

                        $sqlSolicitudLab = "INSERT INTO LABORATORIO (nombreLab, numeroLab) VALUES (:nombreLab, :numeroLab)";

            $consultaSolicitudLab = $this->conexion->prepare($sqlSolicitudLab);

            $consultaSolicitudLab->execute([
                "nombreLab" => $nombreLab,
                "numeroLab" => $numeroLab
            ]);

            $idLab = $this->conexion->lastInsertId();

            $sqlPrestamo = "INSERT INTO SOLICITA (cedula, ID_L, descripcionSolicitud, fecha, gmail) VALUES (:cedula, :ID_L, :descripcionSolicitud, :fecha, :gmail)";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);

            $consultaPrestamo->execute([
                "cedula" => $cedula,
                "ID_L" => $idLab,
                "descripcionSolicitud" => $descripcionSolicitud,
                "fecha" => $fechaSolicitud,
                "gmail" => $gmailDocente
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            //Verifica si se encuentra en una transacción
            if ($this->conexion->inTransaction()) {
                //Deshace los cambios causados por la excepción
                $this->conexion->rollBack();
            }

            // Registrar el error para debug
            $this->ultimoError = $error->getMessage();
            error_log("Error en AltaPrestamos: " . $this->ultimoError);

            return false;
        }
    }

    

    /**
     * Devuelve el último error registrado por la operación de alta.
     *
     * @return string Mensaje del último error; cadena vacía si no hubo error.
     */
    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}


?>
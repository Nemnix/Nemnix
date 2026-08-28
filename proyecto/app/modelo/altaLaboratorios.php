<?php

/**
 * Clase encargada de realizar operaciones de alta
 * relacionadas con los préstamos del sistema.
 */
class AltaLaboratorios
{
    /**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;

    /**
     * Último error producido durante el registro de un préstamo.
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
    * Registra un nuevo laboratorio con su equipo asociado.
     *
      * @param string $cedula Cédula del administrador que registra el laboratorio.
      * @param string $idLaboratorio Identificador del laboratorio, generado por la base de datos.
      * @param string $nombreLaboratorio Nombre o tipo del laboratorio.
      * @param string $numLaboratorio Número del laboratorio.
      * @param string $cantMonitores Cantidad de monitores.
      * @param string $cantTeclados Cantidad de teclados.
      * @param string $cantMouses Cantidad de ratones.
      * @param string $cantComputadoras Cantidad de computadoras.
     *
     * @return bool TRUE si el registro se completa correctamente, FALSE en caso contrario.
     */
    public function registrarLaboratorio(string $cedula, string $idLaboratorio, string $nombreLaboratorio, string $numLaboratorio, string $cantMonitores, string $cantTeclados, string $cantMouses, string $cantComputadoras): bool
    {
        // Validar que los campos obligatorios no estén vacíos
        if (empty($nombreLaboratorio) || empty($numLaboratorio) || empty($cantMonitores) || empty($cantTeclados) || empty($cantMouses) || empty($cantComputadoras)) {
            $this->ultimoError = "Todos los campos son obligatorios.";
            return false;
        }
        try {
            $consultaTecnico = $this->conexion->prepare(
                "SELECT 1 FROM ADMINISTRADOR WHERE cedula = :cedula"
            );
            $consultaTecnico->execute(["cedula" => $cedula]);

            if ($consultaTecnico->fetchColumn() === false) {
                $this->ultimoError = "La cédula del administrador no existe en la tabla ADMINISTRADOR. Cierre sesión e inicie sesión con un administrador registrado.";
                return false;
            }
            //Método que ejecuta de forma agrupada todas las instrucciones dirigidas a la base de datos
            //Si una instrucción falla, retorna excepción con la posibilidad de deshacer cambios con rollBack()
            $this->conexion->beginTransaction();

                        $sqlLaboratorio = "INSERT INTO LABORATORIO (nombreLaboratorio, numLaboratorio) VALUES (:nombreLaboratorio, :numLaboratorio)";

            $consultaLaboratorio = $this->conexion->prepare($sqlLaboratorio);

            $consultaLaboratorio->execute([
                "nombreLaboratorio" => $nombreLaboratorio,
                "numLaboratorio" => $numLaboratorio
            ]);

            $idLaboratorio = $this->conexion->lastInsertId();
            $sqlEquipo = "INSERT INTO EQUIPO (ID_L, cantMonitor, cantPC, cantTeclado, cantMouse) VALUES (:ID_L, :cantMonitor, :cantPC, :cantTeclado, :cantMouse)";

            $consultaEquipo = $this->conexion->prepare($sqlEquipo);

            $consultaEquipo->execute([
                "ID_L" => $idLaboratorio,
                "cantMonitor" => $cantMonitores,
                "cantPC" => $cantComputadoras,
                "cantTeclado" => $cantTeclados,
                "cantMouse" => $cantMouses
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
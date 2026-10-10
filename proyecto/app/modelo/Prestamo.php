<?php

/**
 * Representa el préstamo de un equipo a un alumno, con los datos del alumno y del equipo prestado.
 */
class Prestamo {
    private int $idPrestamo;
    private int $idEquipo;
    private string $modelo;
    private string $cedula;
    private string $ciAlumno;
    private string $nombreAlumno;
    private string $apellidoAlumno;
    private string $estado;
    private string $fechaInicio;
    private string $fechaLimite;

    /**
     * Constructor parametrizado con los datos del préstamo.
     * @param int $idPrestamo Identificador del préstamo.
     * @param int $idEquipo Identificador del equipo prestado.
     * @param string $modelo Modelo del equipo prestado.
     * @param string $cedula Cédula del técnico que registró el préstamo.
     * @param string $ciAlumno Cédula del alumno.
     * @param string $nombreAlumno Nombre del alumno.
     * @param string $apellidoAlumno Apellido del alumno.
     * @param string $estado Estado del préstamo.
     * @param string $fechaInicio Fecha de inicio del préstamo.
     * @param string $fechaLimite Fecha límite de devolución.
     */
    public function __construct(int $idPrestamo, int $idEquipo, string $modelo, string $cedula, string $ciAlumno, string $nombreAlumno, string $apellidoAlumno, string $estado, string $fechaInicio, string $fechaLimite) {
        $this->idPrestamo = $idPrestamo;
        $this->idEquipo = $idEquipo;
        $this->modelo = $modelo;
        $this->cedula = $cedula;
        $this->ciAlumno = $ciAlumno;
        $this->nombreAlumno = $nombreAlumno;
        $this->apellidoAlumno = $apellidoAlumno;
        $this->estado = $estado;
        $this->fechaInicio = $fechaInicio;
        $this->fechaLimite = $fechaLimite;
    }

    /**
     * @return int El identificador del préstamo.
     */
    public function getIdPrestamo(): int {
        return $this->idPrestamo;
    }

    /**
     * @return int El identificador del equipo prestado.
     */
    public function getIdEquipo(): int {
        return $this->idEquipo;
    }

    /**
     * @return string El modelo del equipo prestado.
     */
    public function getModelo(): string {
        return $this->modelo;
    }

    /**
     * @return string La cédula del técnico.
     */
    public function getCedula(): string {
        return $this->cedula;
    }

    /**
     * @return string La cédula del alumno.
     */
    public function getCiAlumno(): string {
        return $this->ciAlumno;
    }

    /**
     * @return string El nombre del alumno.
     */
    public function getNombreAlumno(): string {
        return $this->nombreAlumno;
    }

    /**
     * @return string El apellido del alumno.
     */
    public function getApellidoAlumno(): string {
        return $this->apellidoAlumno;
    }

    /**
     * @return string El estado del préstamo.
     */
    public function getEstado(): string {
        return $this->estado;
    }

    /**
     * @return string La fecha de inicio.
     */
    public function getFechaInicio(): string {
        return $this->fechaInicio;
    }

    /**
     * @return string La fecha límite de devolución.
     */
    public function getFechaLimite(): string {
        return $this->fechaLimite;
    }
}

?>

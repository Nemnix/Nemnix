<?php

/**
 * Representa el registro diario de uso de un laboratorio o taller hecho por un solicitante.
 */
class Registro {
    private int $idRegistro;
    private string $cedula;
    private int $idLaboratorio;
    private string $grupo;
    private string $horaEntrada;
    private string $horaFin;
    private string $asignatura;
    private string $fecha;
    private string $turno;

    /**
     * Constructor parametrizado con los datos del registro diario.
     * @param int $idRegistro Identificador del registro (0 si todavía no existe).
     * @param string $cedula Cédula del solicitante.
     * @param int $idLaboratorio Laboratorio o taller usado.
     * @param string $grupo Grupo que usó el laboratorio.
     * @param string $horaEntrada Hora de entrada.
     * @param string $horaFin Hora de salida.
     * @param string $asignatura Asignatura dictada.
     * @param string $fecha Fecha del registro.
     * @param string $turno Turno (Matutino, Vespertino o Nocturno).
     */
    public function __construct(int $idRegistro, string $cedula, int $idLaboratorio, string $grupo, string $horaEntrada, string $horaFin, string $asignatura, string $fecha, string $turno) {
        $this->idRegistro = $idRegistro;
        $this->cedula = $cedula;
        $this->idLaboratorio = $idLaboratorio;
        $this->grupo = $grupo;
        $this->horaEntrada = $horaEntrada;
        $this->horaFin = $horaFin;
        $this->asignatura = $asignatura;
        $this->fecha = $fecha;
        $this->turno = $turno;
    }

    /**
     * @return int El identificador del registro.
     */
    public function getIdRegistro(): int {
        return $this->idRegistro;
    }

    /**
     * @return string La cédula del solicitante.
     */
    public function getCedula(): string {
        return $this->cedula;
    }

    /**
     * @return int El laboratorio o taller usado.
     */
    public function getIdLaboratorio(): int {
        return $this->idLaboratorio;
    }

    /**
     * @return string El grupo.
     */
    public function getGrupo(): string {
        return $this->grupo;
    }

    /**
     * @return string La hora de entrada.
     */
    public function getHoraEntrada(): string {
        return $this->horaEntrada;
    }

    /**
     * @return string La hora de salida.
     */
    public function getHoraFin(): string {
        return $this->horaFin;
    }

    /**
     * @return string La asignatura.
     */
    public function getAsignatura(): string {
        return $this->asignatura;
    }

    /**
     * @return string La fecha del registro.
     */
    public function getFecha(): string {
        return $this->fecha;
    }

    /**
     * @return string El turno.
     */
    public function getTurno(): string {
        return $this->turno;
    }
}

?>

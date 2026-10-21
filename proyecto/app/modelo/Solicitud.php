<?php

/**
 * Representa la solicitud de preparación de un laboratorio hecha por un solicitante.
 */
class Solicitud {
    private int $idLaboratorio;
    private string $cedula;
    private string $nombreLaboratorio;
    private int $numLaboratorio;
    private string $fecha;
    private string $hora;
    private string $descripcionSolicitud;
    private ?string $gmail;

    /**
     * Constructor parametrizado con los datos de la solicitud.
     * @param int $idLaboratorio Identificador del laboratorio solicitado.
     * @param string $cedula Cédula del solicitante.
     * @param string $nombreLaboratorio Tipo de aula (Laboratorio o Taller).
     * @param int $numLaboratorio Número del laboratorio.
     * @param string $fecha Fecha para la que se solicita el laboratorio.
     * @param string $hora Hora para la que se solicita el laboratorio.
     * @param string $descripcionSolicitud Descripción de lo que se necesita.
     * @param string|null $gmail Gmail de contacto de esta solicitud.
     */
    public function __construct(int $idLaboratorio, string $cedula, string $nombreLaboratorio, int $numLaboratorio, string $fecha, string $hora, string $descripcionSolicitud, ?string $gmail) {
        $this->idLaboratorio = $idLaboratorio;
        $this->cedula = $cedula;
        $this->nombreLaboratorio = $nombreLaboratorio;
        $this->numLaboratorio = $numLaboratorio;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->descripcionSolicitud = $descripcionSolicitud;
        $this->gmail = $gmail;
    }

    /**
     * @return int El identificador del laboratorio solicitado.
     */
    public function getIdLaboratorio(): int {
        return $this->idLaboratorio;
    }

    /**
     * @return string La cédula del solicitante.
     */
    public function getCedula(): string {
        return $this->cedula;
    }

    /**
     * @return string El tipo de aula (Laboratorio o Taller).
     */
    public function getNombreLaboratorio(): string {
        return $this->nombreLaboratorio;
    }

    /**
     * @return int El número del laboratorio.
     */
    public function getNumLaboratorio(): int {
        return $this->numLaboratorio;
    }

    /**
     * @return string La fecha de la solicitud.
     */
    public function getFecha(): string {
        return $this->fecha;
    }

    /**
     * @return string La hora de la solicitud.
     */
    public function getHora(): string {
        return $this->hora;
    }

    /**
     * @return string La descripción de la solicitud.
     */
    public function getDescripcionSolicitud(): string {
        return $this->descripcionSolicitud;
    }

    /**
     * @return string|null El gmail de contacto de esta solicitud.
     */
    public function getGmail(): ?string {
        return $this->gmail;
    }
}

?>

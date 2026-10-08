<?php

/**
 * Representa el diagnóstico que un técnico hace sobre un ticket.
 */
class Diagnostico {
    private int $idDiagnostico;
    private int $idRegistro;
    private int $idTicket;
    private string $cedula;
    private string $estado;
    private string $fecha;
    private string $descripcion;

    /**
     * Constructor parametrizado con los datos del diagnóstico.
     * @param int $idDiagnostico Identificador del diagnóstico.
     * @param int $idRegistro Registro diario al que pertenece el ticket.
     * @param int $idTicket Ticket diagnosticado.
     * @param string $cedula Cédula del técnico que lo hizo.
     * @param string $estado Estado del diagnóstico.
     * @param string $fecha Fecha del diagnóstico.
     * @param string $descripcion Descripción del diagnóstico.
     */
    public function __construct(int $idDiagnostico, int $idRegistro, int $idTicket, string $cedula, string $estado, string $fecha, string $descripcion) {
        $this->idDiagnostico = $idDiagnostico;
        $this->idRegistro = $idRegistro;
        $this->idTicket = $idTicket;
        $this->cedula = $cedula;
        $this->estado = $estado;
        $this->fecha = $fecha;
        $this->descripcion = $descripcion;
    }

    /**
     * @return int El identificador del diagnóstico.
     */
    public function getIdDiagnostico(): int {
        return $this->idDiagnostico;
    }

    /**
     * @return int El registro diario al que pertenece el ticket.
     */
    public function getIdRegistro(): int {
        return $this->idRegistro;
    }

    /**
     * @return int El ticket diagnosticado.
     */
    public function getIdTicket(): int {
        return $this->idTicket;
    }

    /**
     * @return string La cédula del técnico.
     */
    public function getCedula(): string {
        return $this->cedula;
    }

    /**
     * @return string El estado del diagnóstico.
     */
    public function getEstado(): string {
        return $this->estado;
    }

    /**
     * @return string La fecha del diagnóstico.
     */
    public function getFecha(): string {
        return $this->fecha;
    }

    /**
     * @return string La descripción del diagnóstico.
     */
    public function getDescripcion(): string {
        return $this->descripcion;
    }
}

?>

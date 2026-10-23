<?php

/**
 * Representa un ticket de un equipo con problemas, levantado dentro de un registro diario.
 */
class Ticket {
    private int $idRegistro;
    private int $idTicket;
    private string $estado;
    private string $tipoDispositivo;
    private string $nombreEquipo;
    private string $nombreAlumno;
    private string $descripcion;
    private string $prioridad;

    /**
     * Constructor parametrizado con los datos del ticket.
     * @param int $idRegistro Registro diario al que pertenece el ticket.
     * @param int $idTicket Número del ticket dentro del registro.
     * @param string $estado Estado del ticket (Abierto, Pendiente, En proceso, Cerrado).
     * @param string $tipoDispositivo Tipo del dispositivo.
     * @param string $nombreEquipo Nombre o número del dispositivo.
     * @param string $nombreAlumno Alumno que usaba el equipo (puede estar vacío).
     * @param string $descripcion Motivo del ticket.
     * @param string $prioridad Prioridad del ticket.
     */
    public function __construct(int $idRegistro, int $idTicket, string $estado, string $tipoDispositivo, string $nombreEquipo, string $nombreAlumno, string $descripcion, string $prioridad) {
        $this->idRegistro = $idRegistro;
        $this->idTicket = $idTicket;
        $this->estado = $estado;
        $this->tipoDispositivo = $tipoDispositivo;
        $this->nombreEquipo = $nombreEquipo;
        $this->nombreAlumno = $nombreAlumno;
        $this->descripcion = $descripcion;
        $this->prioridad = $prioridad;
    }

    /**
     * @return int El registro diario al que pertenece el ticket.
     */
    public function getIdRegistro(): int {
        return $this->idRegistro;
    }

    /**
     * @return int El número del ticket dentro del registro.
     */
    public function getIdTicket(): int {
        return $this->idTicket;
    }

    /**
     * @return string El estado del ticket.
     */
    public function getEstado(): string {
        return $this->estado;
    }

    /**
     * @return string El tipo del dispositivo.
     */
    public function getTipoDispositivo(): string {
        return $this->tipoDispositivo;
    }

    /**
     * @return string El nombre o número del dispositivo.
     */
    public function getNombreEquipo(): string {
        return $this->nombreEquipo;
    }

    /**
     * @return string El alumno que usaba el equipo.
     */
    public function getNombreAlumno(): string {
        return $this->nombreAlumno;
    }

    /**
     * @return string El motivo del ticket.
     */
    public function getDescripcion(): string {
        return $this->descripcion;
    }

    /**
     * @return string La prioridad del ticket.
     */
    public function getPrioridad(): string {
        return $this->prioridad;
    }
}

?>

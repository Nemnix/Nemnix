<?php

class solicitud{
    private string $ID_R;
    private string $cedula;
    private string $nombreLaboratorio;
    private string $numLaboratorio;
    private string $ID_L;
    private string $grupo;
    private string $horaEntrada;
    private string $horaSalida;
    private string $asignatura;
    private string $fecha;
    private string $turno;

    public function __construct(string $ID_R, string $cedula, string $nombreLaboratorio, string $numLaboratorio, string $ID_L, string $grupo, string $horaEntrada, string $horaSalida, string $asignatura, string $fecha, string $turno) {
            $this->ID_R = $ID_R;
            $this->cedula = $cedula;
            $this->nombreLaboratorio = $nombreLaboratorio;
            $this->numLaboratorio = $numLaboratorio;
            $this->ID_L = $ID_L;
            $this->grupo = $grupo;
            $this->horaEntrada = $horaEntrada;
            $this->horaSalida = $horaSalida;
            $this->asignatura = $asignatura;
            $this->fecha = $fecha;
            $this->turno = $turno;
        }
        public function getID_R(): string {
            return $this->ID_R;
        }
        public function getCedula(): string {
            return $this->cedula;
        }
        public function getNombreLaboratorio(): string {
            return $this->nombreLaboratorio;
        }
        public function getNumLaboratorio(): string {
            return $this->numLaboratorio;
        }
        public function getGrupo(): string {
            return $this->grupo;
        }
        public function getHoraEntrada(): string {
            return $this->horaEntrada;
        }
        public function getHoraSalida(): string {
            return $this->horaSalida;
        }
        public function getAsignatura(): string {
            return $this->asignatura;
        }
        public function getFecha(): string {
            return $this->fecha;
        }
        public function getTurno(): string {
            return $this->turno;
        }
}
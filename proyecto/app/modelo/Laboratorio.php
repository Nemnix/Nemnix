<?php

/**
 * Representa a un laboratorio o taller del centro y su equipamiento
 * (monitores, teclados, mouses y computadoras).
 */
class Laboratorio {
    private int $idLaboratorio;
    private string $nombreLaboratorio;
    private int $numLaboratorio;
    private int $cantMonitores;
    private int $cantTeclados;
    private int $cantMouses;
    private int $cantComputadoras;

    /**
     * Constructor parametrizado con los datos del laboratorio y su equipamiento.
     * @param int $idLaboratorio Identificador del laboratorio.
     * @param string $nombreLaboratorio Tipo de aula (Laboratorio o Taller).
     * @param int $numLaboratorio Número del laboratorio.
     * @param int $cantMonitores Cantidad de monitores.
     * @param int $cantTeclados Cantidad de teclados.
     * @param int $cantMouses Cantidad de mouses.
     * @param int $cantComputadoras Cantidad de computadoras.
     */
    public function __construct(int $idLaboratorio, string $nombreLaboratorio, int $numLaboratorio, int $cantMonitores, int $cantTeclados, int $cantMouses, int $cantComputadoras) {
        $this->idLaboratorio = $idLaboratorio;
        $this->nombreLaboratorio = $nombreLaboratorio;
        $this->numLaboratorio = $numLaboratorio;
        $this->cantMonitores = $cantMonitores;
        $this->cantTeclados = $cantTeclados;
        $this->cantMouses = $cantMouses;
        $this->cantComputadoras = $cantComputadoras;
    }

    /**
     * @return int El identificador del laboratorio.
     */
    public function getIdLaboratorio(): int {
        return $this->idLaboratorio;
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
     * @return int La cantidad de monitores.
     */
    public function getCantMonitores(): int {
        return $this->cantMonitores;
    }

    /**
     * @return int La cantidad de teclados.
     */
    public function getCantTeclados(): int {
        return $this->cantTeclados;
    }

    /**
     * @return int La cantidad de mouses.
     */
    public function getCantMouses(): int {
        return $this->cantMouses;
    }

    /**
     * @return int La cantidad de computadoras.
     */
    public function getCantComputadoras(): int {
        return $this->cantComputadoras;
    }
}

?>

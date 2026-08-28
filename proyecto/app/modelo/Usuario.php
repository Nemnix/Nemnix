<?php

/**
 * Representa a un usuario del sistema y sus roles (administrador, tecnico, solicitante).
 */
class Usuario {
    private string $cedula;
    private string $nombre;
    private string $apellido;
    private string $claveHash;
    private bool $activo;
    private bool $administrador;
    private bool $tecnico;
    private bool $solicitante;

    /**
     * Constructor parametrizado con los datos del usuario y sus roles.
     * @param string $cedula Cédula del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $apellido Apellido del usuario.
     * @param string $claveHash Hash de la contraseña del usuario.
     * @param bool $activo Indica si el usuario está activo.
     * @param bool $administrador Indica si el usuario tiene el rol administrador.
     * @param bool $tecnico Indica si el usuario tiene el rol tecnico.
     * @param bool $solicitante Indica si el usuario tiene el rol solicitante.
     */
    public function __construct(string $cedula, string $nombre, string $apellido, string $claveHash, bool $activo, bool $administrador, bool $tecnico, bool $solicitante) {
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->claveHash = $claveHash;
        $this->activo = $activo;
        $this->administrador = $administrador;
        $this->tecnico = $tecnico;
        $this->solicitante = $solicitante;
    }

    /**
     * @return string La cédula del usuario.
     */
    public function getCedula(): string {
        return $this->cedula;
    }

    /**
     * @return string El nombre del usuario.
     */
    public function getNombre(): string {
        return $this->nombre;
    }

    /**
     * @return string El apellido del usuario.
     */
    public function getApellido(): string {
        return $this->apellido;
    }

    /**
     * @return string El hash de la contraseña del usuario.
     */
    public function getClaveHash(): string {
        return $this->claveHash;
    }

    /**
     * @return bool TRUE si el usuario está activo.
     */
    public function estaActivo(): bool {
        return $this->activo;
    }

    /**
     * @return bool TRUE si el usuario tiene el rol administrador.
     */
    public function esAdministrador(): bool {
        return $this->administrador;
    }

    /**
     * @return bool TRUE si el usuario tiene el rol tecnico.
     */
    public function esTecnico(): bool {
        return $this->tecnico;
    }

    /**
     * @return bool TRUE si el usuario tiene el rol solicitante.
     */
    public function esSolicitante(): bool {
        return $this->solicitante;
    }

    /**
     * @return bool TRUE si el usuario no tiene ningún rol asignado.
     */
    public function sinRoles(): bool {
        return !$this->administrador && !$this->tecnico && !$this->solicitante;
    }
}

?>
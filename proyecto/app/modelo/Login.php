<?php

/**
 * Encapsula la lógica de autenticación de usuarios del sistema.
 */
class Login {
    private UsuarioDAO $accesoDatosUsuario;

    /**
     * Constructor parametrizado que recibe el acceso a datos de usuario.
     * @param UsuarioDAO $accesoDatosUsuario Fuente de datos de usuarios.
     */
    public function __construct(UsuarioDAO $accesoDatosUsuario) {
        $this->accesoDatosUsuario = $accesoDatosUsuario;
    }

    /**
     * Autentica a un usuario por cédula y contraseña.
     * @param string $cedula Cédula del usuario.
     * @param string $clave Contraseña en texto plano ingresada por el usuario.
     * @return Usuario|null El usuario autenticado, o null si la cédula no existe,
     *                       el usuario está inactivo o la contraseña es incorrecta.
     */
    public function autenticar(string $cedula, string $clave): ?Usuario {
        $usuario = $this->accesoDatosUsuario->buscarUsuario($cedula);

        
        if ($usuario === null) {
            return null;
        }

        if (!$usuario->estaActivo()) {
            return null;
        }

        if ( !password_verify($clave, $usuario->getClaveHash() ) ){
            return null;
        }

        return $usuario;
    }

     /**
     * Genera un token JWT para un usuario ya autenticado.
     * @param Usuario $usuario Usuario autenticado.
     * @return string Token JWT firmado.
     */

    public function generarToken(Usuario $usuario): string {
    $roles = [];
    if ($usuario->esAdministrador()) $roles[] = "administrador";
    if ($usuario->esTecnico())       $roles[] = "tecnico";
    if ($usuario->esSolicitante())   $roles[] = "solicitante";

    $payload = [
        'iat'   => time(),
        'exp'   => time() + JWT_EXPIRA,
        'sub'   => $usuario->getCedula(),
        'roles' => $roles,
    ];

    return JWT::encode($payload, JWT_SECRET, 'HS256');
}
}


?>
<?php
require_once RUTA_MODELO . "/Usuario.php";
class Sesion
{
    public static function iniciar(Usuario $usuario): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION["cedula"] = $usuario->getCedula();
        $_SESSION["administrador"] = $usuario->esAdministrador();
        $_SESSION["tecnico"] = $usuario->esTecnico();
        $_SESSION["solicitante"] = $usuario->esSolicitante();
    }

    public static function cerrar(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        //https://www.php.net/manual/es/function.session-destroy.php
        if (ini_get("session.use_cookies")) {
            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $parametros["path"],
                $parametros["domain"],
                $parametros["secure"],
                $parametros["httponly"]
            );
        }

        session_destroy();
    }

    public static function verificarRol(string $rol): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        
        if (!($_SESSION[$rol] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }

    }

    public static function verificarAlgunRol(array $roles): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION["cedula"])) {
            RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }

        foreach ($roles as $rol) {
            if ($_SESSION[$rol] ?? false) {
                return;
            }
        }

        RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
    }

}
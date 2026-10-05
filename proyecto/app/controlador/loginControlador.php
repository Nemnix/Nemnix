<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/usuarioDAO.php";
require_once RUTA_MODELO . "/Login.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Token.php";
require_once RUTA_NUCLEO . "/Sesion.php";

class LoginControlador
{
    public function autenticar(string $metodo): void
    {
        if ($metodo !== "POST") {
            RespuestaJson::error("Método no permitido", 405);
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        $cedula = trim($datos["cedula"] ?? "");
        $clave = $datos["clave"] ?? "";

        if ($cedula === "" || $clave === "") {
            RespuestaJson::error("Cédula y contraseña son obligatorias", 422);
        }

        $conexion = Conexion::conectar();
        $dao = new AccesoDatosUsuario($conexion);
        $login = new Login($dao);
        $usuario = $login->autenticar($cedula, $clave);
        Conexion::desconectar();

        if ($usuario === null || $usuario->sinRoles()) {
            RespuestaJson::error("Cédula o contraseña incorrectas", 401);
        }

        Sesion::iniciar($usuario);
        Token::generarTokenCSRF();

        RespuestaJson::exito([
            "mensaje" => "Sesión iniciada correctamente",
            "csrfToken" => $_SESSION["csrfToken"],
            "administrador" => $usuario->esAdministrador(),
            "tecnico" => $usuario->esTecnico(),
            "solicitante" => $usuario->esSolicitante()
        ]);
    }

    public function cerrarSesion(string $metodo): void
    {
        if ($metodo !== "POST") {
            RespuestaJson::error("Método no permitido", 405);
        }

        Token::verificarCSRF();
        Sesion::cerrar();

        RespuestaJson::exito(["mensaje" => "Sesión cerrada correctamente"]);
    }

    
}


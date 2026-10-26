<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_MODELO . "/Login.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

require_once RUTA_NUCLEO . "/Conexion.php";

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
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }

        try {
            $conexion = Conexion::conectar();
            $dao = new UsuarioDAO($conexion);
            $login = new Login($dao);
            $usuario = $login->autenticar($cedula, $clave);
            Conexion::desconectar();
        } catch (PDOException $error) {
            error_log("Error en inicio de sesión: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if ($usuario === null || $usuario->sinRoles()) {
            RespuestaJson::error("Cédula o contraseña incorrectas", 401);
        }

        $token = $login->generarToken($usuario);

        RespuestaJson::exito([
            "mensaje" => "Sesión iniciada correctamente",
            "token" => $token,
            // Datos del usuario autenticado.
            "cedula" => $usuario->getCedula(),
            "nombre" => $usuario->getNombre(),
            "apellido" => $usuario->getApellido(),
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

        // Con JWT el cierre de sesión se hace en el cliente borrando el token.
        RespuestaJson::exito(["mensaje" => "Sesión cerrada correctamente"]);
    }
}
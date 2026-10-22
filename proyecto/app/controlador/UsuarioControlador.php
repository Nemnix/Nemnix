<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Usuario.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class UsuarioControlador
{

    public function gestionar(string $metodo): void
    {
        Sesion::verificarRol("administrador");

         if ($metodo !== "GET") {
            Token::verificarCSRF();
        }
        
        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PUT" => $this->modificar(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function listar(): void {
        try{
            $conexion = $this->conectar();
            $dao = new UsuarioDAO($conexion);
             $usuarios = $dao->listarUsuarios();
        }
            catch (PDOException $error) {
                error_log("Error en listarUsuarios: " . $error->getMessage());
                RespuestaJson::error("Error al listar usuarios", 500);
        }
        RespuestaJson::exito($usuarios);
    }

    private function alta(): void
    {

        $datos = $this->leerJson();

        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");
        $clave = $datos["clave"] ?? "";
        $confirmarClave = $datos["confirmarClave"] ?? "";
        $rol = trim($datos["rol"] ?? "");
        // El segundo rol es opcional.
        $segundoRol = trim($datos["segundoRol"] ?? "");
        $gmail = trim($datos["gmail"] ?? "");

        if ($cedula === "" || $nombre === "" || $apellido === "" || $clave === "" || $confirmarClave === "" || $rol === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }
        if (!in_array($rol, ["Administrador", "Tecnico", "Solicitante"], true)) {
            RespuestaJson::error("Rol incorrecto", 422);
        }
        if ($segundoRol !== "" && !in_array($segundoRol, ["Administrador", "Tecnico", "Solicitante"], true)) {
            RespuestaJson::error("Segundo rol incorrecto", 422);
        }
        if ($segundoRol === $rol) {
            RespuestaJson::error("El segundo rol debe ser distinto del primero", 422);
        }
        $roles = [$rol, $segundoRol];
        if (strlen($clave) < 12) {
            RespuestaJson::error("La contraseña debe contener al menos 12 caracteres", 422);
        }
        if ($clave !== $confirmarClave) {
            RespuestaJson::error("Las contraseñas ingresadas no coinciden", 422);
        }

        // El gmail solo se pide si uno de los roles es Solicitante.
        if (in_array("Solicitante", $roles, true)) {
            if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
                RespuestaJson::error("Gmail incorrecto", 422);
            }
        } else {
            $gmail = null;
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        try {
            $conexion = $this->conectar();
            $dao = new UsuarioDAO($conexion);
            // Se arma el usuario con los datos ya validados: activo y con los roles elegidos.
            $usuario = new Usuario($cedula, $nombre, $apellido, $claveHash, true, in_array("Administrador", $roles, true), in_array("Tecnico", $roles, true), in_array("Solicitante", $roles, true), $gmail);
            $resultado = $dao->registrarUsuario($usuario);
        } catch (PDOException $error) {
            error_log("Error en alta de usuario: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Ya existe un usuario activo con esa cédula", 409);
        }

        RespuestaJson::exito(["mensaje" => "Usuario ingresado exitosamente"], 201);
    }

    private function modificar(): void
    {

        $datos = $this->leerJson();

        $cedula = trim($datos["cedula"] ?? "");
        $nombre = trim($datos["nombre"] ?? "");
        $apellido = trim($datos["apellido"] ?? "");
        $clave = $datos["clave"] ?? "";
        $rol = trim($datos["rol"] ?? "");
        // El segundo rol es opcional.
        $segundoRol = trim($datos["segundoRol"] ?? "");
        $gmail = trim($datos["gmail"] ?? "");

        if ($cedula === "" || $nombre === "" || $apellido === "" || $rol === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }
        if (!in_array($rol, ["Administrador", "Tecnico", "Solicitante"], true)) {
            RespuestaJson::error("Rol incorrecto", 422);
        }
        if ($segundoRol !== "" && !in_array($segundoRol, ["Administrador", "Tecnico", "Solicitante"], true)) {
            RespuestaJson::error("Segundo rol incorrecto", 422);
        }
        if ($segundoRol === $rol) {
            RespuestaJson::error("El segundo rol debe ser distinto del primero", 422);
        }
        $roles = [$rol, $segundoRol];

        // El gmail solo se pide si uno de los roles es Solicitante.
        if (in_array("Solicitante", $roles, true)) {
            if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
                RespuestaJson::error("Gmail incorrecto", 422);
            }
        } else {
            $gmail = null;
        }

        // Si no se ingresó una clave nueva, se mantiene la actual.
        $claveHash = null;
        if ($clave !== "") {
            if (strlen($clave) < 12) {
                RespuestaJson::error("La contraseña debe contener al menos 12 caracteres", 422);
            }
            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        }

        try {
            $conexion = $this->conectar();
            $dao = new UsuarioDAO($conexion);
            // Se arma el usuario con los datos ya validados; la clave es NULL si no se ingresó una nueva.
            $usuario = new Usuario($cedula, $nombre, $apellido, $claveHash, true, in_array("Administrador", $roles, true), in_array("Tecnico", $roles, true), in_array("Solicitante", $roles, true), $gmail);
            $resultado = $dao->modificarUsuario($usuario);
        } catch (PDOException $error) {
            error_log("Error en modificación de usuario: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El usuario no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Usuario modificado exitosamente"]);
    }

    private function baja(): void
    {

        $datos = $this->leerJson();
        $cedula = trim($datos["cedula"] ?? "");

        if ($cedula === "") {
            RespuestaJson::error("Falta la cédula del empleado", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }
        if ($cedula === $_SESSION["cedula"]) {
            RespuestaJson::error("No puede eliminar su propio usuario", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new UsuarioDAO($conexion);
            $resultado = $dao->eliminarUsuario($cedula);
        } catch (PDOException $error) {
            error_log("Error en baja de usuario: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El usuario no existe o ya fue dado de baja", 404);
        }

        RespuestaJson::exito(["mensaje" => "Usuario eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }

    //Decodifica el cuerpo JSON de la petición; si no es un objeto JSON válido responde 400
    private function leerJson(): array
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            RespuestaJson::error("JSON inválido", 400);
        }

        return $datos;
    }
}
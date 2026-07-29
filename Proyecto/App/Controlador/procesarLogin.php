<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/usuario.php";
require_once __DIR__ . "/../modelo/AccesoDatosUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";





//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$clave = $_POST["clave"] ?? "";

$conectorPDO = new ConectorPDO ("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);



$usuario = $login->autenticar($cedula, $clave);

$conectorPDO->desconectar();

//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    $mensaje = "Acceso Denegado: La cédula o la contraseña son incorrectas.";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["solicitante"] = $usuario->esSolicitante();

if ($usuario->sinRoles()) {
    $mensaje = "Acceso Denegado: El usuario no tiene un rol asignado.";
    header("Location: login.php?error=" . urlencode($mensaje));
    exit;
}

if ($usuario->esAdministrador() && $usuario->esTecnico()) {
    header("Location: ../Public/panelRoles.php");
} elseif ($usuario->esTecnico()) {
    header("Location: usuarioTecnico/paginaBienvenidaTecnico.php");
} elseif ($usuario->esAdministrador()) {
    header("Location: usuarioAdministrador/paginaBienvenidaAdministrador.php");
} elseif ($usuario->esSolicitante()) {
    header("Location: usuarioSolicitante/paginaBienvenidaSolicitante.php");
}

exit;


?>
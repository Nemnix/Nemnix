<?php



require_once __DIR__ . "/../modelo/usuario.php";
require_once __DIR__ . "/../modelo/consultaUsuario.php";
require_once __DIR__ . "/../modelo/Login.php";



//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$clave = $_POST["clave"] ?? "";

$consultaUsuario = new ConsultaUsuario();
$login = new Login($consultaUsuario);

$usuario = $login->autenticar($cedula, $clave);

//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    exit("La cédula o la contraseña son incorrectas.");
}

//Solo se encuentra implementado el rol administrador
if ($usuario->esAdministrador()) {



session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["solicitante"] = $usuario->esSolicitante();

header("Location: usuarioAdministrador/paginaBienvenidaCoordinador.php");
exit;

}


if ($usuario->esTecnico()) {
    


session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["solicitante"] = $usuario->esSolicitante();

header("Location: usuarioTecnico/paginaBienvenidaTecnico.php");
exit;
}

if ($usuario->esSolicitante()) {
 
session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["solicitante"] = $usuario->esSolicitante();

header("Location: usuarioSolicitante/paginaBienvenidaSolicitante.php");
exit;
}
?>
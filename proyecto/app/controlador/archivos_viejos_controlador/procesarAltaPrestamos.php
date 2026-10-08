<?php

/**
 * Procesa el alta de un prestamo de equipo enviada mediante POST.
 *
 * Valida la sesion del tecnico y delega el registro en AltaPrestamos.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaPrestamos.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

//Comprueba que haya un técnico logueado (cedula es NOT NULL y FK en la tabla PRESTAMO)
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    $mensaje = "Debe iniciar sesión como técnico para registrar un préstamo.";

    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera los datos del formulario, con variables nombradas según las columnas de PRESTAMO

$cedulaTecnico = $_SESSION["cedula"];
$ciAlumno       = trim($_POST["ciAlumno"] ?? "");
$nombreAlumno   = trim($_POST["nombreAlumnoPrestamo"] ?? "");
$apellidoAlumno = trim($_POST["apellidoAlumnoPrestamo"] ?? "");
$estadoPrestamo = trim($_POST["estadoEquipos"] ?? "");
$fechaInicio    = trim($_POST["fechaPrestamo"] ?? "");
$fechaLimite    = trim($_POST["fechaDevolucion"] ?? "");
$idEquipo       = trim($_POST["ID_EP"] ?? "");
$modeloEquipo   = trim($_POST["modelo_prestamo"] ?? "");

// Validar que todos los campos obligatorios estén completos
if (empty($ciAlumno) || empty($nombreAlumno) || empty($apellidoAlumno) || empty($fechaInicio) || empty($fechaLimite) || empty($estadoPrestamo) || empty($idEquipo) || empty($modeloEquipo)) {
    $mensaje = "Error: Todos los campos son obligatorios.";
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$altaPrestamos = new AltaPrestamos($conexion);

$resultado = $altaPrestamos->registrarPrestamo($cedulaTecnico, $ciAlumno, $nombreAlumno, $apellidoAlumno, $fechaInicio, $fechaLimite, $estadoPrestamo, $idEquipo, $modeloEquipo);

$conectorPDO->desconectar();


if (!$resultado) {
    $mensaje = "No se pudo registrar el préstamo: " . $altaPrestamos->obtenerUltimoError();

    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Préstamo registrado exitosamente.";
header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?resultado=" . urlencode($mensaje));
exit;

?>

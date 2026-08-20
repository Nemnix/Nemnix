<?php

/**
 * Procesa la modificación de un préstamo enviada mediante POST.
 *
 * Valida la sesión del técnico, recoge los datos del formulario y delega
 * la actualización transaccional en AccesoPrestamos.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoPrestamos.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["tecnico"] ?? false) !== true) {
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode("Acceso denegado."));
    exit;
}

$idPrestamo = trim($_POST["ID_P"] ?? "");
$modelo = trim($_POST["modelo_prestamo"] ?? "");
$estadoPrestamo = trim($_POST["estadoEquipos"] ?? "");
$ciAlumno = trim($_POST["ciAlumno"] ?? "");
$nombreAlumno = trim($_POST["nombreAlumnoPrestamo"] ?? "");
$apellidoAlumno = trim($_POST["apellidoAlumnoPrestamo"] ?? "");
$fechaInicio = trim($_POST["fechaPrestamo"] ?? "");
$fechaLimite = trim($_POST["fechaDevolucion"] ?? "");

if ($idPrestamo === "" || $modelo === "" || $estadoPrestamo === "" || $ciAlumno === "" || $nombreAlumno === "" || $apellidoAlumno === "" || $fechaInicio === "" || $fechaLimite === "") {
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode("Todos los campos son obligatorios."));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();
$accesoPrestamos = new AccesoPrestamos($conexion);
$resultado = $accesoPrestamos->modificarPrestamo($idPrestamo, $modelo, $estadoPrestamo, $nombreAlumno, $apellidoAlumno, $fechaInicio, $fechaLimite, $ciAlumno);
$conectorPDO->desconectar();

$parametro = $resultado ? "resultado" : "error";
$mensaje = $resultado ? "Préstamo modificado exitosamente." : "No se pudo modificar el préstamo.";
header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?" . $parametro . "=" . urlencode($mensaje));
exit;

<?php

/**
 * Procesa la modificación de un registro diario enviada mediante POST.
 *
 * Valida la sesión del solicitante, recoge los datos del formulario y delega
 * la actualización transaccional en AccesoRegistros.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoRegistros.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode("Acceso denegado."));
    exit;
}

$idRegistro = trim($_POST["ID_R"] ?? "");
$nombreLaboratorio = trim($_POST["preparacionLaboratorio"] ?? "");
$numLaboratorio = trim($_POST["numLabTaller"] ?? "");
$grupo = trim($_POST["grupoRegistroDiario"] ?? "");
$horaEntrada = trim($_POST["horaEntrada"] ?? "");
$horaFin = trim($_POST["horaSalida"] ?? "");
$asignatura = trim($_POST["asignaturaRegistroDiario"] ?? "");
$fecha = trim($_POST["fechaRegistroDiario"] ?? "");
$turno = trim($_POST["turnoRegistroDiario"] ?? "");

if ($idRegistro === "" || $nombreLaboratorio === "" || $numLaboratorio === "" || $grupo === "" || $horaEntrada === "" || $horaFin === "" || $asignatura === "" || $fecha === "" || $turno === "") {
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode("Todos los campos son obligatorios."));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();
$accesoRegistros = new AccesoRegistros($conexion);
$resultado = $accesoRegistros->modificarRegistro($idRegistro, $nombreLaboratorio, $numLaboratorio, $grupo, $horaEntrada, $horaFin, $asignatura, $fecha, $turno);
$conectorPDO->desconectar();

$parametro = $resultado ? "resultado" : "error";
$mensaje = $resultado ? "Registro modificado exitosamente." : "No se pudo modificar el registro.";
header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?" . $parametro . "=" . urlencode($mensaje));
exit;

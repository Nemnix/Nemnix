<?php

/**
 * Procesa la modificación de un diagnostico enviada mediante POST.
 *
 * Valida la sesión del técnico, recoge los datos del formulario y delega
 * la actualización en ModificarDiagnostico.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/mdificarDiagnostico.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["tecnico"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$idDiagnostico = (int) trim($_POST["ID_D"] ?? "0");
$estado = trim($_POST["estadoDiagnostico"] ?? "");
$fecha = trim($_POST["IDfechaDiagnostico"] ?? "");
$descripcion = trim($_POST["IDdescripcionDiagnostico"] ?? "");

if ($idDiagnostico <= 0 || empty($estado) || empty($fecha) || empty($descripcion)) {
    $mensaje = "Todos los campos son obligatorios.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$modificarDiagnostico = new ModificarDiagnostico($conexion);
$resultado = $modificarDiagnostico->actualizarDiagnostico($idDiagnostico, $estado, $fecha, $descripcion);

$conectorPDO->desconectar();

$parametro = $resultado ? "resultado" : "error";
$mensaje = $resultado ? "Diagnóstico actualizado correctamente." : "No se pudo actualizar el diagnóstico.";
header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?" . $parametro . "=" . urlencode($mensaje));
exit;

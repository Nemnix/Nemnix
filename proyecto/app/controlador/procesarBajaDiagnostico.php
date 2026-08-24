<?php

/**
 * Procesa la eliminación de un diagnostico enviada mediante POST.
 *
 * Valida la sesión del técnico y delega la eliminación en BajaDiagnostico.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/BajaDiagnostico.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["tecnico"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$idDiagnostico = (int) trim($_POST["ID_D"] ?? "0");

if ($idDiagnostico <= 0) {
    $mensaje = "Identificador de diagnóstico inválido.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$bajaDiagnostico = new BajaDiagnostico($conexion);
$resultado = $bajaDiagnostico->eliminarDiagnostico($idDiagnostico);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el diagnóstico.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Diagnóstico eliminado correctamente.";
header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?resultado=" . urlencode($mensaje));
exit;

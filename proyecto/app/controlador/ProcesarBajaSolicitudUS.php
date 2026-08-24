<?php
session_start();
require_once RUTA_MODELO . '/conexion.php';
require_once RUTA_MODELO . '/BajaSolicitudUS.php';

function validarSesion(): void {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Debe iniciar sesion para acceder a estos datos.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vista/solicitudesLabs.php?mensaje=Peticion no valida');
    exit;
}
 
validarSesion();
 
$idL = $_POST['ID_L'] ?? null;
$cedula = $_POST['cedula'] ?? null;
 
if (!$idL || !$cedula) {
    header('Location: ../vista/solicitudesLabs.php?mensaje=Debe indicar el laboratorio y la cedula del solicitante');
    exit;
}
 
$conexion = obtenerConexion();
 
$bajaSolicitud = new BajaSolicitudUS($conexion);
$eliminado = $bajaSolicitud->eliminarSolicitud((int) $idL, (string) $cedula);
 
if ($eliminado) {
    header('Location: ../vista/solicitudesLabs.php?mensaje=Solicitud eliminada correctamente');
} else {
    header('Location: ../vista/solicitudesLabs.php?mensaje=No se pudo eliminar la solicitud');
}
exit;
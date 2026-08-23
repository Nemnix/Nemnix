<?php

session_start();

require_once RUTA_MODELO . '/conexion.php';
require_once RUTA_MODELO . '/modelo/BajaSolicitud.php';

/**
 * Valida que exista una sesion de usuario activa.
 * Si no hay sesion, redirige al login con un mensaje de error.
 */
function validarSesion(): void {
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../vista/login.php?mensaje=Debe iniciar sesion para realizar esta accion');
        exit;
    }
}

// Solo se permite procesar la baja mediante una peticion POST.
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

$bajaSolicitud = new BajaSolicitud($conexion);
$eliminado = $bajaSolicitud->eliminarSolicitud((int) $idL, (string) $cedula);

if ($eliminado) {
    header('Location: ../vista/solicitudesLabs.php?mensaje=Solicitud eliminada correctamente');
} else {
    header('Location: ../vista/solicitudesLabs.php?mensaje=No se pudo eliminar la solicitud');
}
exit;
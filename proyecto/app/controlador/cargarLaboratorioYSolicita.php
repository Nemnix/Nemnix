<?php

session_start();

require_once RUTA_MODELO . '/conexion.php';
require_once RUTA_MODELO . '/accesoLaboratorio.php';
require_once RUTA_MODELO . '/modelo/accesoSolicita.php';

header('Content-Type: application/json');

/**
 * Valida que exista una sesion de usuario activa.
 * Si no hay sesion, corta la ejecucion y responde con error 401.
 */
function validarSesion(): void {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Debe iniciar sesion para acceder a estos datos.']);
        exit;
    }
}

validarSesion();

$conexion = obtenerConexion();

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    case 'laboratorio':
        $accesoLaboratorio = new AccesoLaboratorio($conexion);
        $laboratorios = $accesoLaboratorio->listarLaboratorio();

        echo json_encode($laboratorios);
        break;

    case 'solicita':
        $accesoSolicita = new AccesoSolicita($conexion);
        $solicitudes = $accesoSolicita->listarSolicita();

        echo json_encode($solicitudes);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Accion no valida. Use accion=laboratorio o accion=solicita.']);
        break;
}
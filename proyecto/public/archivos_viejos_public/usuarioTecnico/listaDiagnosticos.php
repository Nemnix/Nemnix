<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioTecnico/listaDiagnosticos.php
 * Reemplazado por: public/paginas/listaDiagnosticos.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico del listado de diagnosticos del usuario tecnico.
 *
 * Requiere sesion iniciada con rol tecnico; en caso contrario
 * redirige al login con un mensaje de error. Delega la carga de datos
 * en el controlador cargarDiagnostico.
 * /

require_once __DIR__ . "/../../config/config.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

if ( !isset($_SESSION["tecnico"]) || !($_SESSION["tecnico"] ?? false) ) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location:  /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}


require_once RUTA_CONTROLADOR . "/cargarDiagnostico.php";

?>




*/

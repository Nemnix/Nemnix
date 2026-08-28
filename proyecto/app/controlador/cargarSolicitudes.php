<?php

/**
 * Carga el listado de solicitudes de laboratorios del usuario solicitante
 * y lo pasa a la vista solicitudesLabs.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoSolicitudes.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoSolicitudes = new AccesoSolicitudes($conexion);
    $solicitudes = $accesoSolicitudes->listarSolicitudes();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/solicitudesLabs.php";
?>
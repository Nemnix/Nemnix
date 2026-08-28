<?php

/**
 * Carga el resumen de solicitudes por laboratorio y la cantidad total
 * de solicitudes pendientes, y los pasa a la vista listaGlobalSolicitudes.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoSolicita.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoSolicita = new AccesoSolicita($conexion);
    $solicitudesPorAula = $accesoSolicita->listarSolicitudesPorLaboratorio();
    $solicitudesPendientes = $accesoSolicita->contarSolicitudes();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaGlobalSolicitudes.php";

?>

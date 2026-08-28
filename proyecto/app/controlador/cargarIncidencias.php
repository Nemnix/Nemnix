<?php

/**
 * Carga el resumen de incidencias (tickets) por laboratorio y los
 * contadores de incidencias criticas, y los pasa a la vista listaIncidencias.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoIncidencias.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoIncidencias = new AccesoIncidencias($conexion);
    $incidenciasPorAula = $accesoIncidencias->listarIncidenciasPorLaboratorio();
    $incidenciasPrioridadAlta = $accesoIncidencias->contarIncidenciasPrioridadAltaSinResolver();
    $incidenciasSinResolver = $accesoIncidencias->contarIncidenciasSinResolver();
    $incidenciasAutorizacion = $accesoIncidencias->contarIncidenciasRequierenAutorizacion();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaIncidencias.php";

?>

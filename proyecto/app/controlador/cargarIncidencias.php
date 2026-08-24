<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/AccesoIncidencias.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoIncidencias = new AccesoIncidencias($conexion);
    $incidenciasPorAula = $accesoIncidencias->listarIncidenciasPorLaboratorio();
    $incidenciasPrioridadAlta = $accesoIncidencias->contarIncidenciasPrioridadAltaSinResolver();
    $incidenciasSinResolver = $accesoIncidencias->contarIncidenciasSinResolver();
    $incidenciasAutorizacion = $accesoIncidencias->contarIncidenciasRequierenAutorizacion();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaIncidencias.php";

?>

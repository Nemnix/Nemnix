<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/AccesoSolicita.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoSolicita = new AccesoSolicita($conexion);
    $solicitudesPorAula = $accesoSolicita->listarSolicitudesPorLaboratorio();
    $solicitudesPendientes = $accesoSolicita->contarSolicitudes();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaGlobalSolicitudes.php";

?>

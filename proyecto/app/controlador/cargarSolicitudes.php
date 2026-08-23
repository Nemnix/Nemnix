<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/accesoSolicitudes.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoSolicitudes = new AccesoSolicitudes($conexion);
    $solicitudes = $accesoSolicitudes->listarSolicitudes();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/solicitudesLabs.php";
?>
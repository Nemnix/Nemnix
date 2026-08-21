<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/accesoDiagnosticos.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoDiagnosticos = new AccesoDiagnosticos($conexion);
    $diagnosticos = $accesoDiagnosticos->listarDiagnosticos();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaDiagnosticos.php";

?>
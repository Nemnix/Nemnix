<?php

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDiagnosticos.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoDiagnosticos = new AccesoDiagnosticos($conexion);
    $diagnosticos = $accesoDiagnosticos->listarDiagnosticos();
    
$conectorPDO->desconectar();

require_once __DIR__ . "/../vista/listaDiagnosticos.php";

?>
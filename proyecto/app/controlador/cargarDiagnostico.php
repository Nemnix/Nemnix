<?php

require_once __DIR__ . "/../modelo/ConectorPDO.php";
require_once __DIR__ . "/../modelo/AccesoDiagnosticos.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDiagnosticos($conexion);
    $diagnosticos = $accesoDatosUsuario->listarDiagnosticos();
    
$conectorPDO->desconectar();

require_once __DIR__ . "/../vista/listaDiagnosticos.php";

?>
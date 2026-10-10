<?php

/**
 * Carga la lista de diagnosticos y la pasa a la vista listaDiagnosticos.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoDiagnosticos.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoDiagnosticos = new AccesoDiagnosticos($conexion);
    $diagnosticos = $accesoDiagnosticos->listarDiagnosticos();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaDiagnosticos.php";

?>
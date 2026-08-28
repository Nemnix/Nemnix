<?php

/**
 * Carga el resumen de inventario por laboratorio y los
 * contadores de artículos con prioridad alta, y los pasa a la vista listaInventario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoInventario.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoInventario = new AccesoInventario($conexion);
    $inventarios = $accesoInventario->listarInventarios();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaInventario.php";

?>
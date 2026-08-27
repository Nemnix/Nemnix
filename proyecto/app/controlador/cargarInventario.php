<?php

/**
 * Carga el resumen de inventario por laboratorio y los
 * contadores de artículos con prioridad alta, y los pasa a la vista listaInventario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/AccesoInventario.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoInventario = new AccesoInventario($conexion);
    $inventarios = $accesoInventario->listarInventarios();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaInventario.php";

?>
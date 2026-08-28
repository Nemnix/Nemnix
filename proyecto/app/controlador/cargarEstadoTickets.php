<?php

/**
 * Carga la lista de tickets y la pasa a la vista estadoTickets.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoTickets.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoTickets = new AccesoTickets($conexion);
    $tickets = $accesoTickets->listarTickets();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/estadoTickets.php";

?>

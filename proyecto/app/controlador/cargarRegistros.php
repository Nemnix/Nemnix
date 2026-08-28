<?php

/**
 * Carga los registros diarios junto con sus tickets asociados
 * (agrupados por ID_R) y los pasa a la vista listaRegistroDiario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoRegistros.php";
require_once RUTA_MODELO . "/AccesoTickets.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoPrestamos = new AccesoRegistros($conexion);
    $registros = $accesoPrestamos->listarRegistros();

    $accesoTickets = new AccesoTickets($conexion);
    $ticketsPorRegistro = [];
    foreach ($accesoTickets->listarTodosLosTickets() as $ticket) {
        $ticketsPorRegistro[$ticket["ID_R"]][] = $ticket;
    }

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaRegistroDiario.php";

?>

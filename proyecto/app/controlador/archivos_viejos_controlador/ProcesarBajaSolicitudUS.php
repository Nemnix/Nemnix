<?php

// /**
//  * Procesa la baja de una solicitud enviada mediante POST.
//  *
//  * Valida la sesion del usuario y delega la eliminacion en BajaSolicitudUS.
//  */

// require_once __DIR__ . "/../../config/config.php";
// require_once __DIR__ . "/../../config/env.php";
// require_once RUTA_MODELO . "/ConectorPDO.php";
// require_once RUTA_MODELO . "/BajaSolicitudUS.php";

// session_start();

// /**
//  * Verifica que exista una sesion de usuario iniciada.
//  *
//  * Responde con HTTP 401 y detiene la ejecucion si no hay sesion.
//  */
// function validarSesion(): void {
//     if (!isset($_SESSION['usuario'])) {
//         http_response_code(401);
//         echo json_encode(['error' => 'Debe iniciar sesion para acceder a estos datos.']);
//         exit;
//     }
// }

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     header('Location: ../vista/solicitudesLabs.php?mensaje=Peticion no valida');
//     exit;
// }
 
// validarSesion();
 
// $idL = $_POST['ID_L'] ?? null;
// $cedula = $_POST['cedula'] ?? null;
 
// if (!$idL || !$cedula) {
//     header('Location: ../vista/solicitudesLabs.php?mensaje=Debe indicar el laboratorio y la cedula del solicitante');
//     exit;
// }
 
// $conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
// $conexion = $conectorPDO->establecerConexion();

// if ($conexion === null) {
//     http_response_code(500);
//     echo json_encode(['error' => 'No se pudo establecer conexion con la base de datos.']);
//     exit;
// }
 
// $bajaSolicitud = new BajaSolicitudUS($conexion);
// $eliminado = $bajaSolicitud->eliminarSolicitud((int) $idL, (string) $cedula);

// $conectorPDO->desconectar();
 
// if ($eliminado) {
//     header('Location: ../vista/solicitudesLabs.php?mensaje=Solicitud eliminada correctamente');
// } else {
//     header('Location: ../vista/solicitudesLabs.php?mensaje=No se pudo eliminar la solicitud');
// }
// exit;
?>
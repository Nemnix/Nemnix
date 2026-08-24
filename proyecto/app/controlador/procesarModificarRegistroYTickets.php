<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/AccesoRegistroYTickets.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Comprobar que la petición sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

session_start();

// Validar sesión de usuario
if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

// Determinar si se modifica un registro o un ticket
$isTicket = isset($_POST['ID_T']) && $_POST['ID_T'] !== "";

if ($isTicket && (!isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true)) {
    $mensaje = "Debe iniciar sesión como técnico para modificar un ticket.";
    header("Location: /proyecto/public/usuarioTecnico/listaTicketsEntrantes.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

$acceso = new AccesoRegistroYTickets($conexion);

if ($isTicket) {
    // Campos esperados para ticket
    $idT = (int)($_POST['ID_T'] ?? 0);
    $idR = (int)($_POST['ID_R'] ?? 0);
    $estado = trim($_POST['estado'] ?? '');
    $nombreEquipo = trim($_POST['nombreEquipo'] ?? trim($_POST['nombreDispositivo'] ?? ''));
    $nombreAlumno = trim($_POST['nombreAlumno'] ?? '');
    $descripcionT = trim($_POST['descripcionT'] ?? trim($_POST['motivo_Ticket'] ?? ''));
    $prioridad = trim($_POST['prioridad'] ?? '');

    if ($idT <= 0 || $idR <= 0 || empty($estado) || empty($descripcionT)) {
        $mensaje = "Datos inválidos o incompletos para actualizar el ticket.";
        header("Location: /proyecto/public/usuarioTecnico/listaTicketsEntrantes.php?error=" . urlencode($mensaje));
        exit;
    }

    $resultado = $acceso->actualizarTicket($idT, $idR, $estado, $nombreEquipo, $nombreAlumno, $descripcionT, $prioridad);

    $conectorPDO->desconectar();

    if (!$resultado) {
        $mensaje = "No se pudo actualizar el ticket.";
        header("Location: /proyecto/public/usuarioTecnico/listaTicketsEntrantes.php?error=" . urlencode($mensaje));
        exit;
    }

    $mensaje = "Ticket actualizado correctamente.";
    header("Location: /proyecto/public/usuarioTecnico/listaTicketsEntrantes.php?resultado=" . urlencode($mensaje));
    exit;

} else {
    // Campos esperados para registro diario
    $idR = (int)($_POST['ID_R'] ?? 0);
    $grupo = trim($_POST['grupo'] ?? '');
    $horaEntrada = trim($_POST['horaEntrada'] ?? '');
    $horaFin = trim($_POST['horaFin'] ?? '');
    $docente = trim($_POST['docente'] ?? '');
    $asignatura = trim($_POST['asignatura'] ?? '');
    $fecha = trim($_POST['fecha'] ?? trim($_POST['fechaRegistroDiario'] ?? ''));
    $nombreAula = trim($_POST['nombreAula'] ?? trim($_POST['numLabTaller'] ?? ''));
    $turno = trim($_POST['turno'] ?? trim($_POST['turnoRegistroDiario'] ?? ''));

    if ($idR <= 0 || empty($fecha) || empty($grupo)) {
        $mensaje = "Datos inválidos o incompletos para actualizar el registro diario.";
        header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
        exit;
    }

    $resultado = $acceso->actualizarRegistro($idR, $grupo, $horaEntrada, $horaFin, $docente, $asignatura, $fecha, $nombreAula, $turno);

    $conectorPDO->desconectar();

    if (!$resultado) {
        $mensaje = "No se pudo actualizar el registro diario.";
        header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
        exit;
    }

    $mensaje = "Registro diario actualizado correctamente.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?resultado=" . urlencode($mensaje));
    exit;
}

?>

<?php

// /**
//  * Clase encargada de realizar operaciones de alta
//  * relacionadas con los registros diarios del sistema.
//  */
// class AltaRegistro
// {
//     private PDO $conexion;
//     private string $ultimoError = "";

//     /**
//      * Constructor parametrizado que recibe una conexión a la base de datos.
//      * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
//      */
//     public function __construct(PDO $conexion)
//     {
//         $this->conexion = $conexion;
//     }

//     /**
//      * Registra un nuevo registro diario y crea el laboratorio asociado.
//      *
//      * @param string $cedula Cédula del solicitante (FK a SOLICITANTE).
//      * @param string $grupo Grupo del curso.
//      * @param string $horaInicio Hora de entrada.
//      * @param string $horaFin Hora de salida.
//      * @param string $asignatura Asignatura dictada.
//      * @param string $fecha Fecha del registro.
//      * @param string $nombreLab Nombre del laboratorio o taller.
//      * @param string $numLab Número del laboratorio o taller.
//      * @param string $turno Turno (Matutino/Vespertino/Nocturno).
//      *
//      * @return bool TRUE si el registro se completa correctamente, FALSE en caso contrario.
//      */
//     public function registrarRegistro(string $cedula, string $grupo, string $horaInicio, string $horaFin, string $asignatura, string $fecha, string $nombreLab, string $numLab, string $turno): bool
//     {
//         if (empty($cedula) || empty($grupo) || empty($horaInicio) || empty($horaFin) || empty($asignatura) || empty($fecha) || empty($nombreLab) || empty($numLab) || empty($turno)) {
//             $this->ultimoError = "Todos los campos son obligatorios.";
//             return false;
//         }

//         try {
//             $this->conexion->beginTransaction();

//             // 1) Insertar primero el laboratorio para obtener su ID_L
//             $sqlLaboratorio = "INSERT INTO LABORATORIO (nombreLaboratorio, numLaboratorio) VALUES (:nombreLaboratorio, :numLaboratorio)";
//             $consultaLaboratorio = $this->conexion->prepare($sqlLaboratorio);
//             $consultaLaboratorio->execute([
//                 "nombreLaboratorio" => $nombreLab,
//                 "numLaboratorio" => $numLab,
//             ]);

//             $idLaboratorio = $this->conexion->lastInsertId();

//             // 2) Insertar el registro diario usando el ID_L recién generado
//             $sqlRegistro = "INSERT INTO REGISTRO_DIARIO (ID_L, cedula, grupo, horaEntrada, horaFin, asignatura, fecha, turno) VALUES (:ID_L, :cedula, :grupo, :horaInicio, :horaFin, :asignatura, :fecha, :turno)";
//             $consultaRegistro = $this->conexion->prepare($sqlRegistro);
//             $consultaRegistro->execute([
//                 "ID_L" => $idLaboratorio,
//                 "cedula" => $cedula,
//                 "grupo" => $grupo,
//                 "horaInicio" => $horaInicio,
//                 "horaFin" => $horaFin,
//                 "asignatura" => $asignatura,
//                 "fecha" => $fecha,
//                 "turno" => $turno
//             ]);

//             $this->conexion->commit();

//             return true;

//         } catch (PDOException $error) {
//             if ($this->conexion->inTransaction()) {
//                 $this->conexion->rollBack();
//             }

//             $this->ultimoError = $error->getMessage();
//             error_log("Error en AltaRegistro: " . $this->ultimoError);

//             return false;
//         }
//     }

//     /**
//      * Devuelve el último error registrado por la operación de alta.
//      * @return string Mensaje del último error; cadena vacía si no hubo error.
//      */
//     public function obtenerUltimoError(): string
//     {
//         return $this->ultimoError;
//     }
// }
?>
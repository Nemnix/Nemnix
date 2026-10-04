<?php
// /**
//  * Ejecuta operaciones de baja para los registros diarios.
//  */
// class BajaRegistro
// {
//     /**
//      * Conexión activa con la base de datos.
//      *
//      * @var PDO
//      */
//     private PDO $conexion;


//     /**
//      * Constructor parametrizado que recibe una conexión a la base de datos.
//      *
//      * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
//      */
//     public function __construct(PDO $conexion)
//     {
//         $this->conexion = $conexion;
//     }


//     /**
//      * Elimina un registro diario y el laboratorio asociado.
//      *
//      * Los tickets se eliminan antes que el registro para respetar la clave foránea
//      * de TICKET hacia REGISTRO_DIARIO. El laboratorio se elimina únicamente si
//      * no tiene otros registros asociados.
//      *
//      * @param string $idRegistro Identificador del registro diario.
//      * @return bool TRUE si la operación se completa correctamente; FALSE si falla.
//      */
//     public function eliminarRegistro(string $idRegistro): bool
//     {

//         try {
//             $this->conexion->beginTransaction();

//             $consultaLaboratorio = $this->conexion->prepare(
//                 "SELECT ID_L FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
//             );
//             $consultaLaboratorio->execute(["idRegistro" => $idRegistro]);
//             $idLaboratorio = $consultaLaboratorio->fetchColumn();

//             if ($idLaboratorio === false) {
//                 $this->conexion->rollBack();
//                 return false;
//             }

//             $consultaTicket = $this->conexion->prepare(
//                 "DELETE FROM TICKET WHERE ID_R = :idRegistro"
//             );
//             $consultaTicket->execute(["idRegistro" => $idRegistro]);

//             $consultaRegistro = $this->conexion->prepare(
//                 "DELETE FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
//             );
//             $consultaRegistro->execute(["idRegistro" => $idRegistro]);

//             $consultaOtrosRegistros = $this->conexion->prepare(
//                 "SELECT COUNT(*) FROM REGISTRO_DIARIO WHERE ID_L = :idLaboratorio"
//             );
//             $consultaOtrosRegistros->execute(["idLaboratorio" => $idLaboratorio]);

//             if ((int) $consultaOtrosRegistros->fetchColumn() === 0) {
//                 $consultaLaboratorio = $this->conexion->prepare(
//                     "DELETE FROM LABORATORIO WHERE ID_L = :idLaboratorio"
//                 );
//                 $consultaLaboratorio->execute(["idLaboratorio" => $idLaboratorio]);
//             }

//             $this->conexion->commit();
//             return true;

//         } catch (PDOException $error) {
//             if ($this->conexion->inTransaction()) {
//                 $this->conexion->rollBack();
//             }

//             return false;
//         }
//     }
// }
?>
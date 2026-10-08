<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/IncidenciaDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";


class IncidenciaControlador
{

    public function gestionar(string $metodo): void
    {
        Sesion::verificarRol("administrador");

        // Las incidencias son solo de consulta.
        match ($metodo) {
            "GET" => $this->listar(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function listar(): void
    {
        try {
            $conexion = $this->conectar();
            $dao = new IncidenciaDAO($conexion);

            if (isset($_GET["metricas"])) {
                // Página de métricas y reportes.
                $incidencias = $dao->listarMetricasPorLaboratorio();
            } elseif (isset($_GET["equipos"])) {
                // Página de estado de equipos.
                $incidencias = $dao->obtenerTotalesEquipo();
                $incidencias["incidenciasActivas"] = $dao->contarIncidenciasSinResolver();
                $incidencias["totalIncidencias"] = $dao->contarIncidencias();
            } else {
                // Página de incidencias.
                $incidencias = [
                    "porLaboratorio" => $dao->listarIncidenciasPorLaboratorio(),
                    "prioridadAltaSinResolver" => $dao->contarIncidenciasPrioridadAltaSinResolver(),
                    "sinResolver" => $dao->contarIncidenciasSinResolver(),
                    "requierenAutorizacion" => $dao->contarIncidenciasRequierenAutorizacion()
                ];
            }
        } catch (PDOException $error) {
            error_log("Error en listarIncidencias: " . $error->getMessage());
            RespuestaJson::error("Error al listar incidencias", 500);
        }
        RespuestaJson::exito($incidencias);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}

<?php
require_once RUTA_VISTA . "/RespuestaJson.php";
class Token
{
    public static function verificarCSRF(): void
    {
        $token = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
        if (!isset($_SESSION["csrfToken"]) || !hash_equals($_SESSION["csrfToken"], $token)) {
            RespuestaJson::error("Solicitud rechazada", 403);
        }
    }

    public static function generarTokenCSRF(): void
    {
        $_SESSION["csrfToken"] = bin2hex(random_bytes(32));
    }
}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css">
</head>

<body>
    <header>
        <button onclick="subir()" class="botonSubir">▲</button>
        <img class="logo" src="../Assets/img/logoUTU.png">
        <button id="btnMenu" aria-label="Toggle menu">&#9776;</button>
        <button id="btnCerrar" type="button" aria-label="Cerrar menú">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="20" height="20">
                <path
                    d="M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z" />
            </svg>
        </button>

        <nav>
            <ul class="MenuPrincipal">
               <li><a href="/Proyecto/App/Vista/paginaBienvenidaAdministrador.php" class="BotonNavegacion">Inicio</a></li>
                <li><a href="panelAdministradores.html" class="BotonNavegacion">Panel administradores</a></li>
                <li><a href="listaIncidencias.html" class="BotonNavegacion">Vizualización de incidencias</a></li>
                <li><a href="listaGlobalSolicitudes.html" class="BotonNavegacion">Vizualización de solicitudes</a></li>
                <li><a href="listaInventario.html" class="BotonNavegacion">Inventario tecnologico </a></li>
                <li><a href="listaMetricas.html" class="BotonNavegacion">Metricas y reportes</a></li>
                <li><a href="listaEstadoEquipos.html" class="BotonNavegacion">Estado de equipos</a></li>
            </ul>
        </nav>

    </header>
    <main>
        <section class="Tablas">
            <fieldset>
                <table id="Metricas">

                    <tr>
                        <th>Total computadoras</th>
                        <th>Total perifericos</th>
                        <th>Incidencias activas</th>
                        <th>Cantidad total de incidencias</th>

                    </tr>
                    <tr>

                    </tr>

                </table>
            </fieldset>
        </section>
    </main>
    <footer>
        <span>¿Problemas con la pagina? Escribenos a: nemnix1@gmail.com</span>
    </footer>
    <script src="../Assets/JavaScript/botonSubir.js"></script>
    <script src="../Assets/JavaScript/menu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../Assets/css/global.css">
</head>

<body>
    <header>
        <button onclick="subir()" class="botonSubir">▲</button>
        <img class="logo" src="../Assets/img/logoUTU.png">
        <button id="btnMenu" class="btnMenu" aria-label="Toggle menu">&#9776;</button>
        <button id="btnCerrar" type="button" aria-label="Cerrar menú">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="20" height="20">
                <path
                    d="M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z" />
            </svg>
        </button>
        <nav>
            <ul id="MenuPrincipal" class="MenuPrincipal">
                <li><a href="/proyecto/app/vista/paginaBienvenidaSolicitante.php" class="BotonNavegacion">Inicio</a></li>
                <li><a href="listaRegistroDiario.html" class="BotonNavegacion">Registro</a></li>
                <li><a href="estadoTickets.html" class="BotonNavegacion">Estado Tickets</a></li>
                <li><a href="solicitudesLabs.html" class="BotonNavegacion">Solicitudes</a></li>
            </ul>
        </nav>

    </header>
    <main>
        <h2>Estado de tickets</h2>

        <section class="Tablas">
            <fieldset>
                <table id="tablaTickets">
                    <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Numero de aula</th>
                        <th>Nombre del Estudiante</th>
                        <th>Dispositivo</th>
                        <th>Tipo de dispositivo</th>
                        <th>Motivo</th>
                        <th>Tipo de aula</th>
                        <th>Fecha</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>

                    </tr>
                    </tbody>

                </table>
            </fieldset>
        </section>
    </main>
    <footer>
        <span>¿Problemas con la pagina? Escribenos a: nemnix1@gmail.com</span>
    </footer>

    <script src="/proyecto/public/assets/JavaScript/botonSubir.js"></script>
    <script src="/proyecto/public/assets/JavaScript/menu.js"></script>
    <script src="/proyecto/public/assets/JavaScript/ListaTickets.js"></script>
    <script src="/proyecto/public/assets/JavaScript/menu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
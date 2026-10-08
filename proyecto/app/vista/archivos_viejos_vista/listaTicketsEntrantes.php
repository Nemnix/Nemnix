<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: app/vista/listaTicketsEntrantes.php
 * Reemplazado por: public/paginas/listaTicketsEntrantes.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php
/**
 * Vista: listado de tickets entrantes del usuario tecnico.
 *
 * Variables esperadas:
 * @var array $tickets Lista de tickets con los datos del registro diario y laboratorio asociados.
 * /
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets entrantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css">
</head>

<body>
    <header>
        <button onclick="subir()" class="botonSubir">▲</button>
        <a href="/proyecto/public/login.php" class="salida">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd"
                    d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg>
        </a>
        <img class="logo" src="../assets/img/logoUTU.png">
        <button id="btnMenu" aria-label="Toggle menu">&#9776;</button>
        <button id="btnCerrar" type="button" aria-label="Cerrar menú">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="20" height="20">
                <path
                    d="M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z" />
            </svg>
        </button>
        <nav>
            <ul class="MenuPrincipal">
                <li><a href="paginaBienvenidaTecnico.php" class="BotonNavegacion">Inicio</a></li>
                <li><a href="listaTicketsEntrantes.php" class="BotonNavegacion">Tickets entrantes</a></li>
                <li><a href="listaDiagnosticos.php" class="BotonNavegacion">Diagnosticos</a></li>
                <li><a href="inventarioPrestamos.php" class="BotonNavegacion">Prestamo equipos</a></li>
                

            </ul>
        </nav>
        <script src="../assets/JavaScript/menu.js"></script>
    </header>
    <main>
        <h2>Tickets entrantes</h2>

        <ul>
            <li>
            <div class="opciones">
                <label for="IDprioridad">Prioridad</label>
                <select id="IDprioridad" name="Prioridad">
                    <option>Todos</option>
                    <option>Alta</option>
                    <option>Media</option>
                    <option>Baja</option>
                    <option>Sin definir</option>
                </select>
            </div>
            </li>
            <li>
                <div class="opciones">
                <label for="IDestado">Estado</label>
                <select id="IDestado" name="Estado">
                    <option>Todos</option>
                    <option>Pendiente</option>
                    <option>En proceso</option>
                </select>
                </div>
            </li>
            <li>
                <div class="opciones">
                <label for="IDtiempo">Tiempo</label>
                <select id="IDtiempo" name="Tiempo">
                    <option>Más recientes</option>
                    <option>Más antiguos</option>
                    </div>
            </li>
        </select>
        </ul>

        <dialog id="RegistroyTicket" class="Formulario">

            <form id="FormularioRegistroyTicket" method="post">
                <fieldset>
                    <legend>Ticket</legend>
                    <ul>
                        <input type="hidden" id="modificarTicketID_T" name="ID_T">
                        <input type="hidden" id="modificarTicketID_R" name="ID_R">
                        <li>
                            <label for="IDLaboratorio">Seleccione el tipo de aula</label>
                            <select id="IDLaboratorio" name="preparacionLaboratorio">
                            <option>Laboratorio</option>
                            <option>Taller</option>
                            </select>
                        </li>
                        <li>
                            <label for="numLabTaller">Escriba el numero del laboratorio o taller</label>
                            <input type="text" id="numLabTaller" name="numLabTaller"pattern="[0-9]{2}"  maxlength="2" required 
                                required placeholder="Numero laboratorio/taller"/>
                        </li>
                        <li>
                            <label for="asignaturaRegistroDiario">Asignatura</label>
                            <input type="text" id="asignaturaRegistroDiario" name="asignaturaRegistroDiario" required
                                placeholder="Escriba su asignatura"/>
                        </li>
                        <li>
                            <label for="horaEntrada">Hora de Entrada</label>
                            <input type="time" id="horaEntrada" name="horaEntrada" required>
                        </li>
                        <li>
                            <label for="horaSalida">Hora de salida</label>
                            <input type="time" id="horaSalida" name="horaSalida" required>
                        </li>
                        <li>
                            <label for="grupoRegistroDiario">Grupo</label>
                            <input type="text" id="grupoRegistroDiario" name="grupoRegistroDiario"
                            placeholder="Ingrese nombre del grupo aqui" required>
                        </li>
                        <li>
                            <label for="turnoRegistroDiario">Turno</label>
                            <select id="turnoRegistroDiario" name="turnoRegistroDiario" required>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Nocturno">Nocturno</option>
                            </select>
                        </li>

                            <label for="IDDispositivo">Tipo de dispositivo</label>
                            <input type="text" id="IDDispositivo" name="IDDispositivo"
                                required placeholder="Escribe el tipo del dispositivo aqui" />
                        </li>
                        <li>
                            <label for="nombreAlumno">En caso de ser una maquina, escriba el nombre completo del alumno que utilizo ese equipo</label>
                            <input type="text" id="nombreAlumno" name="nombreAlumno" 
                            required placeholder="Nombre del alumno" />
                        </li>
                        <li>
                            <label for="nombreDispositivo">Nombre o numero del dispositivo</label>
                            <input type="text" id="nombreDispositivo" name="nombreDispositivo" required
                                placeholder="Nombre del equipo" />
                        </li>
                        <li>
                            <label for="motivoTicket">Motivo del ticket</label>
                            <input type="text" id="motivoTicket" name="descripcionT"
                            required placeholder="Ingrese el motivo del ticket" />
                        </li>
                        <li>
                            <label for="prioridadTicket">Prioridad</label>
                            <select id="prioridadTicket" name="prioridad">
                                <option>Alta</option>
                                <option>Media</option>
                                <option>Baja</option>
                                <option>Sin definir</option>
                            </select>
                        </li>
                        <li>
                            <label for="estadoTicket">Estado</label>
                            <select id="estadoTicket" name="estado">
                                <option>Abierto</option>
                                <option>Pendiente</option>
                                <option>En proceso</option>
                                <option>Cerrado</option>
                            </select>
                        </li>
                        <li>
                            <button type="submit"> Enviar </button>
                        </li>
                    </ul>
                </fieldset>
            </form>
            <button id="btnDiagnostico">Realizar diagnostico</button>
            <button id="CerrarTicket">Cerrar</button>
            </dialog>

            <dialog id="RegistroDiagnostico" class="Formulario">
                <form id="FormularioDiagnostico" action="/proyecto/public/usuarioTecnico/procesarAltaDiagnostico.php" method="post">
                <fieldset>
                    <legend>Diagnostico</legend>
                    <ul>
                        <input type="hidden" id="registroAsociado" name="registroAsociado">
                        <li>
                            <label for="estadoDiagnostico">Estado </label>
                            <select id="estadoDiagnostico" name="estado">
                                <option>En proceso</option>
                                <option>Solucionado</option>
                                <option>Requiere otro factor</option>
                                <option>Requiere autorización</option>

                            </select>
                        </li>
                        <li>
                            <label for="TicketAsociado">Ticket Asociado</label>
                            <input type="number" id="TicketAsociado" name="TicketAsociado">
                        </li>
                        <li>
                            <label for="fechaDiagnostico">Fecha</label>
                            <input type="date" id="fechaDiagnostico" name="fechaDiagnostico" required>
                        </li>
                        <li>
                            <label for="descripcionDiagnostico">Descripcion</label>
                            <input type="text" id="descripcionDiagnostico" name="descripcionDiagnostico" required
                            placeholder="Escriba una descripción del diagnostico o tematicas realizadas" />
                        </li>
                        <li>
                            <button type="submit"> Enviar </button>
                            <button type="button" id="btnCerrarDiagnostico"> Cerrar </button>
                        </li>
                    </ul>
                </fieldset>
            </form>
            </section>
            </dialog>

            <section class="Tablas">
            <fieldset>
            <table id="estadoTickets">
            <thead>
            <tr>
                <th>Ticket</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Vizualización</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $ticket) { ?>
                <tr>
                    <td><?= htmlspecialchars($ticket["ID_T"] ?? "") ?></td>
                    <td><?= htmlspecialchars($ticket["prioridad"] ?? "") ?></td>
                    <td><?= htmlspecialchars($ticket["estado"] ?? "") ?></td>
                    <td><?= htmlspecialchars($ticket["fechaRegistro"] ?? "") ?></td>
                    <td>
                        <button type="button" class="btnVisualizar"
                            data-ticket="<?= htmlspecialchars(json_encode($ticket), ENT_QUOTES, "UTF-8") ?>">Visualizar</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
            </fieldset>
            </section>


    </main>
    <footer>
    <span>¿Problemas con la pagina? Escribenos a: nemnix1@gmail.com</span>
    </footer>
    <script src="../assets/JavaScript/botonSubir.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/JavaScript/ticketsEntrantes.js"></script>
    <script src="../assets/JavaScript/formularioDiagnosticos.js"></script>
</body>
</html>
*/

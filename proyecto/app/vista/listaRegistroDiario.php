<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista registro diario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body>
    <header>
        <button onclick="subir()" class="botonSubir">▲</button>
        <img class="logo" src="../assets/img/logoUTU.png">
        <button id="btnMenu" aria-label="Toggle menu">&#9776;</button>
        <button id="btnCerrar" type="button" aria-label="Cerrar menú">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="20" height="20">
                <path
                    d="M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z" />
            </svg>
            <nav>
                <ul class="MenuPrincipal">
                    <li><a href="paginaBienvenidaSolicitante.php" class="BotonNavegacion">Inicio</a></li>
                    <li><a href="listaRegistroDiario.php" class="BotonNavegacion">Registro</a></li>
                    <li><a href="estadoTickets.php" class="BotonNavegacion">Estado Tickets</a></li>
                    <li><a href="solicitudesLabs.php" class="BotonNavegacion">Solicitudes</a></li>
                </ul>
            </nav>

    </header>
    <main>
        <?php if (isset($_GET["resultado"])): ?>
            <p class="mensajeExito"><?= htmlspecialchars($_GET["resultado"], ENT_QUOTES, "UTF-8") ?></p>
        <?php elseif (isset($_GET["error"])): ?>
            <p class="mensajeError"><?= htmlspecialchars($_GET["error"], ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>
        <h2>Registro diario</h2>
        <button id="btnAgregarRegistro" class="botonesAgregar">Agregar registro</button>

        <dialog id="RegistroSolicitante" class="Formulario">

            <form action="/proyecto/public/usuarioSolicitante/procesarAltaRegistros.php" method="POST" id="FormularioRegistroDiario">
                <fieldset>
                    <legend>Solicitud de preparación de laboratorios</legend>
                    <ul>
                        <input type="hidden" id="ID_R" name="ID_R">
                        <li>
                            <label for="IDLaboratorio">Seleccione el tipo de aula</label>
                            <select id="IDLaboratorio" name="preparacionLaboratorio" required>
                                <option>Laboratorio</option>
                                <option>Taller</option>
                            </select>
                        </li>
                        <li>
                            <label for="numLabTaller">Escriba el numero del laboratorio o taller</label>
                            <input type="text" id="numLabTaller" name="numLabTaller" pattern="[0-9]{2}" maxlength="2"
                                required required placeholder="Numero laboratorio/taller" />
                        </li>
                        <li>
                            <label for="fechaRegistroDiario">Fecha</label>
                            <input type="date" id="fechaRegistroDiario" name="fechaRegistroDiario" required>
                        </li>
                        <li>
                            <label for="asignaturaRegistroDiario">Asignatura</label>
                            <input type="text" id="asignaturaRegistroDiario" name="asignaturaRegistroDiario" required
                                placeholder="Escriba su asignatura" />
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

                        <li>
                            <button type="submit"> Enviar </button>
                        </li>
                        <li>

                        </li>
                    </ul>

            </form>

            <button type="button" id="btnCerrarRegistro"> Cerrar </button>
            </fieldset>
        </dialog>

        <dialog id="TicketSolicitante" class="Formulario">

            <form action="/proyecto/public/usuarioSolicitante/procesarAltaTickets.php" method="POST" id="FormularioTickets">
                <fieldset>
                    <legend>Ticket 1</legend>
                    <ul>
                        <input type="hidden" id="ID_R_Ticket" name="ID_R">
                        <li>
                            <label for="idDispositivo">Tipo de dispositivo</label>
                            <input type="text" id="idDispositivo" name="idDispositivo" required
                                placeholder="Escribe el tipo del dispositivo aqui" />
                        </li>
                        <li>
                            <label for="nombreAlumno">En caso de ser una maquina, escriba el nombre completo del alumno
                                que utilizo ese equipo</label>
                            <input type="text" id="nombreAlumno" name="nombreAlumno"
                                placeholder="Nombre del alumno" />
                        </li>
                        <li>
                            <label for="nombreDispositivo">Nombre o numero del dispositivo</label>
                            <input type="text" id="nombreDispositivo" name="nombreDispositivo" required
                                placeholder="Nombre del equipo" />
                        </li>
                        <li>
                            <label for="ticket">Motivo del ticket</label>
                            <input type="text" id="motivoTicket" required name="motivo_Ticket"
                                placeholder="Ingrese el motivo del ticket" />
                        </li>
                        <li>
                            <label for="prioridadTicket">Prioridad</label>
                            <select id="prioridadTicket" name="prioridad" required>
                                <option value="">-- Seleccione una prioridad --</option>
                                <option value="Baja">Baja</option>
                                <option value="Media">Media</option>
                                <option value="Alta">Alta</option>
                            </select>
                        </li>
                        <li>
                            <button type="submit"> Enviar </button>
                        </li>
                        <li>
                            <button type="button" id="btnCerrarTicket">Cerrar</button>
                        </li>
                    </ul>
                </fieldset>
            </form>

            <fieldset>

                <table id="registroTicket">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Problema</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTicketModal">
                    </tbody>
                </table>
            </fieldset>
        </dialog>

        <section class="Tablas">
            <fieldset>

                <table id="registroDiario"> 
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo de aula</th>
                            <th>Numero de aula</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Turno</th>
                            <th>Grupo</th>
                            <th>Asignatura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                <tbody id="tablaRegistroDiario">
                    <?php foreach ($registros as $registro) { ?>
                        <tr>
                            <td><?= htmlspecialchars($registro["fecha"]) ?></td>
                            <td><?= htmlspecialchars($registro["nombreLaboratorio"]) ?></td>
                            <td><?= htmlspecialchars($registro["numLaboratorio"]) ?></td>
                            <td><?= htmlspecialchars($registro["horaEntrada"]) ?></td>
                            <td><?= htmlspecialchars($registro["horaFin"]) ?></td>
                            <td><?= htmlspecialchars($registro["turno"]) ?></td>
                            <td><?= htmlspecialchars($registro["grupo"]) ?></td>
                            <td><?= htmlspecialchars($registro["asignatura"]) ?></td>
                            <td>
                                <button class="btnEditar" data-id="<?= htmlspecialchars($registro["ID_R"]) ?>">Editar</button>
                                <button type="button" class="btnAgregarTicket" data-id="<?= htmlspecialchars($registro["ID_R"]) ?>"
                                    data-tickets="<?= htmlspecialchars(json_encode($ticketsPorRegistro[$registro["ID_R"]] ?? []), ENT_QUOTES, "UTF-8") ?>">Ticket</button>
                               <form action="/proyecto/public/usuarioSolicitante/procesarBajaRegistro.php" method="post" class="formularioBajaRegistro">
                                    <input type="hidden" name="idRegistro" value="<?= htmlspecialchars($registro["ID_R"]) ?>">
                                    <button type="submit" class="btnEliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>                    
                    <tr>

                    </tr>
            </fieldset>
            </table>
        </section>
    </main>
    <footer>
        <span>¿Problemas con la pagina? Escribenos a: nemnix1@gmail.com</span>
    </footer>

    <script src="../assets/JavaScript/botonSubir.js"></script>
    <script src="../assets/JavaScript/menu.js"></script>
    <script src="../assets/JavaScript/RegistroDiario.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
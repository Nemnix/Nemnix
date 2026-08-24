<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario prestamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css"> 
</head>
<body>
    <header>
        <button onclick="subir()" class="botonSubir">▲</button>
        <img class="logo" src="../Assets/img/logoUTU.png">
        <button id="btnMenu" aria-label="Toggle menu">&#9776;</button>
        <button id="btnCerrar" type="button" aria-label="Cerrar menú">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="20" height="20">
        <path d="M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z"/>
        </svg>
        </button>
        <nav>
            <ul class="MenuPrincipal">
                <li><a href="/Proyecto/App/Vista/paginaBienvenidaTecnico.php" class="BotonNavegacion">Inicio</a></li>
                <li><a href="listaTicketsEntrantes.html" class="BotonNavegacion">Tickets entrantes</a></li>
                <li><a href="listaDiagnosticos.html" class="BotonNavegacion">Diagnosticos</a></li>
                <li><a href="inventarioPrestamos.html" class="BotonNavegacion">Prestamo prestamo</a></li>
            </ul>
        </nav>
        
    </header>
    <main>
        <?php if (isset($_GET["resultado"])): ?>
            <p class="mensajeExito"><?= htmlspecialchars($_GET["resultado"], ENT_QUOTES, "UTF-8") ?></p>
        <?php elseif (isset($_GET["error"])): ?>
            <p class="mensajeError"><?= htmlspecialchars($_GET["error"], ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>
        <h2>inventario prestamos</h2>
        <button id="btnAgregarEquipo" class="botonesAgregar">Agregar nuevo equipo</button>
        <dialog id="RegistroEquipoP" class="Formulario">
            <form action="/proyecto/public/usuarioTecnico/procesarAltaPrestamos.php" method="POST" id="FormularioInventarioEquipos">
                    <fieldset>
                        <legend>Equipo de prestamo</legend>
                        <ul>
                            <input type="hidden" id="ID_P" name="ID_P">
                            <li>
                                <label for="ID_EP">ID equipo</label>
                                <input type="text" id="ID_EP" name="ID_EP"
                                    required placeholder="Escribe la ID del equipo" />
                            </li>
                            <li>
                                <label for="modeloPrestamo">Modelo</label>
                                <input type="text" id="modeloPrestamo" name="modelo_prestamo" required placeholder="Ingrese el modelo del equipo">
                            </li>
                            <li>
                                <label for="estadoEquipos">Seleccione estado</label>
                                <select id="estadoEquipos" name="estadoEquipos" required>
                                <option value="">-- Seleccione un estado --</option>
                                <option>Disponible</option>
                                <option>Prestamo en fecha</option>
                                <option>VENCIDO</option>
                                </select>
                            </li>
                            <li>
                                <label for="ciAlumno">Cedula alumno</label>
                                <input type="text" id="ciAlumno" name="ciAlumno"
                                    required placeholder="Escribe la ID del equipo" />
                            </li>
                            <li>
                                <label for="nombreAlumnoPrestamo">Nombre</label>
                                <input type="text" id="nombreAlumnoPrestamo" name="nombreAlumnoPrestamo" required placeholder="Ingrese el nombre del alumno">
                            </li>
                            <li>
                                <label for="apellidoAlumnoPrestamo">Apellido</label>
                                <input type="text" id="apellidoAlumnoPrestamo" name="apellidoAlumnoPrestamo" required placeholder="Ingrese el apellido del alumno">
                            </li>
                            <li>
                                <label for="fechaPrestamo">Fecha de inicio</label>
                                <input type="date" id="fechaPrestamo" name="fechaPrestamo" required>
                            </li>
                            <li>
                                <label for="fechaDevolucion">Fecha de devolucion</label>
                                <input type="date" id="fechaDevolucion" name="fechaDevolucion" required>
                            </li>
                            <li>
                                <button type="submit" id="btnGuardarInventario"> Enviar </button>
                                <button type="button" id="btnCerrarInventario">Cerrar</button>
                            </li>
                        </ul>
                    </fieldset>
                </form>
        </dialog>

        <section class="Tablas">
        <fieldset>
            <table id="tablaInventarioPRestamo">
                <thead>
                    <tr>
                        <th>IDequipo</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="registroInventarioPRestamo">
                    <?php foreach ($prestamos as $prestamo) { ?>
                        <tr data-ci-alumno="<?= htmlspecialchars($prestamo["ciAlumno"], ENT_QUOTES, "UTF-8") ?>">
                            <td><?= htmlspecialchars($prestamo["ID_EP"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["modelo"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["estadoPrestamo"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["nombreAlumno"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["apellidoAlumno"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["fechaInicio"]) ?></td>
                            <td><?= htmlspecialchars($prestamo["fechaLimite"]) ?></td>
                            <td>
                                <button class="btnEditar" data-id="<?= htmlspecialchars($prestamo["ID_P"]) ?>">Editar</button>
                               <form action="/proyecto/public/usuarioTecnico/procesarBajaPrestamo.php" method="post" class="formularioBajaPrestamo">
                                    <input type="hidden" name="idPrestamo" value="<?= htmlspecialchars($prestamo["ID_P"]) ?>">
                                    <button type="submit" class="btnEliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
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
    <script src="../Assets/JavaScript/botonSubir.js"></script>
    <script src="../Assets/JavaScript/menu.js"></script>
    <script src="../assets/JS/script.js"></script>
    <script src="../Assets/JavaScript/listaPrestamos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    


</body>
</html>
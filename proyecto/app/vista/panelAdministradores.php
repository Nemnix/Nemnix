<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css">
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
        </button>
        <nav>
            <ul class="MenuPrincipal">
               <li><a href="paginaBienvenidaAdministrador.php" class="BotonNavegacion">Inicio</a></li>
                <li><a href="panelAdministradores.php" class="BotonNavegacion">Panel administradores</a></li>
                <li><a href="listaIncidencias.php" class="BotonNavegacion">Vizualización de incidencias</a></li>
                <li><a href="listaGlobalSolicitudes.php" class="BotonNavegacion">Vizualización de solicitudes</a></li>
                <li><a href="listaInventario.php" class="BotonNavegacion">Inventario tecnologico </a></li>
                <li><a href="listaMetricas.php" class="BotonNavegacion">Metricas y reportes</a></li>
                <li><a href="listaEstadoEquipos.php" class="BotonNavegacion">Estado de equipos</a></li>
            </ul>
        </nav>

    </header>
    <main>
        <?php if (isset($_GET["resultado"])): ?>
            <p class="mensajeExito"><?= htmlspecialchars($_GET["resultado"], ENT_QUOTES, "UTF-8") ?></p>
        <?php elseif (isset($_GET["error"])): ?>
            <p class="mensajeError"><?= htmlspecialchars($_GET["error"], ENT_QUOTES, "UTF-8") ?></p>
        <?php endif; ?>
        <h2>Panel administradores</h2>

        <button id="btnAltaEmpleado" class="botonesAgregar">Agregar nuevo usuario</button>

        <dialog class="Formulario dialogGestionarEmpleado">
            <form id="formularioGestionarEmpleado" action="procesarAltaUsuario.php" method="post">
                <fieldset>
                    <legend>Panel de administradores</legend>
                    <ul>
                        <li>
                            <label for="cedula">Cedula de identidad</label>
                            <input type="text" id="cedula" name="cedula" pattern="[1-9][0-9]{7}"
                                title="8 numeros, sin puntos ni guiones" maxlength="8" required
                                placeholder="Escribe la cedula del usuario" />
                        </li>
                        <li>
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" required
                                placeholder="Escribe el nombre del usuario aqui" />
                        </li>
                        <li>
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" required
                                placeholder="Escribe el apellido del usuario" />
                        </li>
                        <li>
                            <label for="rol">Seleccione el cargo del usuario</label>
                            <select id="rol" name="rol" required>
                                <option value="">-- Seleccione un cargo --</option>
                                <option>Solicitante</option>
                                <option>Tecnico</option>
                                <option>Administrador</option>
                            </select>
                        </li>
                        <li>
                            <label for="clave">Contraseña</label>
                            <input type="password" id="clave" name="clave" required minlength="12"
                                placeholder="Minimo 12 caracteres" />
                        </li>
                        <li>
                            <label for="confirmarClave">Confirmar contraseña</label>
                            <input type="password" id="confirmarClave" name="confirmarClave" required minlength="12"
                                placeholder="Repita la contraseña" />
                        </li>
                        <li>
                            <button type="submit" id="btnGuardarUsuario">Ingresar </button>
                            <button type="button" id="btnCerrarGestionarEmpleado">Cancelar</button>
                        </li>
                    </ul>
                </fieldset>
            </form>
        </dialog>

        <section class="Tablas">
            <fieldset>
                <table id="panelAdministradores">
                    <thead>
                        <tr>
                            <th>CI</th>
                            <th>Nombres</th>
                            <th>Apellido</th>
                            <th>Cargo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTablaEmpleados">
                        <?php foreach ($usuarios as $usuario) { ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario["cedula"]) ?></td>
                            <td><?= htmlspecialchars($usuario["nombre"]) ?></td>
                            <td><?= htmlspecialchars($usuario["apellido"]) ?></td>
                            <td><?= htmlspecialchars($usuario["cargo"]) ?></td>
                            <td>
                                <div class="cajaOperaciones">
                                    <button type="button" class="btnOperacion btnModificar">Editar</button>
                                    <form action="procesarBajaUsuario.php" method="post" class="formularioEliminarEmpleado">
                                        <input type="hidden" name="cedula" value="<?= htmlspecialchars($usuario["cedula"]) ?>">
                                        <button type="submit" class="btnEliminar">Eliminar</button>
                                    </form>
                                </div>
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
    <script src="../assets/JavaScript/menu.js"></script>
    <script src="../assets/JavaScript/panelAdministradores.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
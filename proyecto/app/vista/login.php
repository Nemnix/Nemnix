<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto/public/assets/css/global.css">
    <link rel="stylesheet" href="/proyecto/public/assets/css/inicioSesion.css">
    
</head>
<body>
    <main>
        <section class="seccionLogin">
            <h2>Ingreso al sistema</h2>

            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
            <?php endif; ?>

           <form class="formLogin" action="/proyecto/public/procesarLogin.php" method="post">
                <fieldset>
                    <legend>Inicio de sesión</legend>

                    <div class="cajaEntradaDeDatos">
                        <label for="cedula">
                            Cédula
                        </label>

                        <input type="text" id="cedula" name="cedula" autocomplete="username" pattern="[1-9][0-9]{7}"
                            title="Ingrese exactamente 8 dígitos sin puntos ni guiones" inputmode="numeric"
                            maxlength="8" required>
                    </div>

                    <div class="cajaEntradaDeDatos">
                        <label for="clave">
                            Contraseña
                        </label>

                        <input type="password" id="clave" name="clave" autocomplete="current-password" minlength="12"
                            required>
                    </div>
                </fieldset>

                <button class="btnInicio"type="submit">
                    Iniciar sesión
                </button>
            </form>
        </section>
    </main>
    <footer>
        <img class="logo" src="/proyecto/public/assets/img/logoUTU.png" alt="Logo">
    </footer>
</body>
</html>
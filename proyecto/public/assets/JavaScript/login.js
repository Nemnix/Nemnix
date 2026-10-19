const API_LOGIN = "../index.php?ruta=login";

const formularioLogin = document.getElementById("formularioLogin");

const entradaCedula = document.getElementById("cedula");
const entradaClave = document.getElementById("clave");


async function loguear(cedula, clave) {

    const credenciales = {
        cedula: cedula,
        clave: clave
    }

    const respuesta = await fetch(
        API_LOGIN,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(credenciales)
        }
    );

    const json = await respuesta.json();

    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "No se pudo iniciar sesión.")
    }

    return json.datos;
}

async function gestionarLogin(eventoFormulario) {
    eventoFormulario.preventDefault();

    try {
        const sesion = await loguear(entradaCedula.value.trim(), entradaClave.value);

        if (!sesion.csrfToken) {
            throw new Error("La API no devolvió el token CSRF.")
        }

        //Guardamos el token en la sesión
        sessionStorage.setItem("csrfToken", sesion.csrfToken)
        //replace() abre la siguiente página borrando el historial de la previa
        if (sesion.administrador && sesion.tecnico) {
            window.location.replace("./panelRoles.html");
        } else if (sesion.administrador) {
            window.location.replace("./paginaBienvenidaAdministrador.html");
        } else if (sesion.tecnico) {
            window.location.replace("./paginaBienvenidaTecnico.html");
        } else {
            window.location.replace("./paginaBienvenidaSolicitante.html");
        }
    } catch (error) {
        window.alert(error.message);
    }
}

formularioLogin.addEventListener("submit", gestionarLogin);
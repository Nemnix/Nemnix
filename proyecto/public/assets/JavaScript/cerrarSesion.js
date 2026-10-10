const API_CERRAR_SESION = "/proyecto/public/api/cerrarSesion.php";

const botonSalida = document.querySelector(".salida");

async function cerrarSesion(evento) {
    evento.preventDefault();

    try {
        await fetch(API_CERRAR_SESION, {
            method: "POST",
            headers: {
                "X-CSRF-Token": sessionStorage.getItem("csrfToken") ?? ""
            }
        });
    } finally {
        // Aunque la API falle, se borra el token y se vuelve al login
        sessionStorage.removeItem("csrfToken");
        window.location.replace("/proyecto/public/paginas/login.html");
    }
}

botonSalida.addEventListener("click", cerrarSesion);

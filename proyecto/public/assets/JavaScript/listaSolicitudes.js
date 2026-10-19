/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de solicitudes
const API_SOLICITUDES = "../index.php?ruta=solicitudes";

//Constante para trabajar con la tabla de solicitudes
const cuerpoTablaSolicitudes = document.querySelector("#VizualizacionSolicitudes tbody");

//Contador de solicitudes pendientes
const solicitudesPendientes = document.getElementById("SolicitudesPendientes");


/**
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API con el token CSRF y devuelve los datos de la respuesta
async function llamarApi(metodo, datos = null) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

    if (metodo !== "GET") {
        opciones.headers["X-CSRF-Token"] = sessionStorage.getItem("csrfToken") ?? "";
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(API_SOLICITUDES, opciones);
    const json = await respuesta.json();

    //Si la sesión no está iniciada o venció, vuelve al login
    if (respuesta.status === 401) {
        window.location.replace("./index.html");
    }
    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "Error en la solicitud.");
    }

    return json.datos;
}


/**
 * TABLA DE SOLICITUDES
 */

//Crea la fila de una solicitud. Usa textContent para que los datos no se interpreten como HTML
function crearFila(solicitud) {
    const fila = document.createElement("tr");

    for (const valor of [solicitud.nombreLaboratorio, solicitud.numLaboratorio, solicitud.nombre, solicitud.apellido, solicitud.fecha, solicitud.gmail, solicitud.descripcionSolicitud]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    //La solicitud se identifica por el laboratorio y la cédula del solicitante
    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = solicitud.ID_L;
    btnEliminar.dataset.cedula = solicitud.cedula;

    celdaAcciones.appendChild(btnEliminar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide las solicitudes a la API, dibuja la tabla y actualiza el contador
async function cargarSolicitudes() {
    try {
        const solicitudes = await llamarApi("GET");
        cuerpoTablaSolicitudes.replaceChildren(...solicitudes.map(crearFila));
        solicitudesPendientes.textContent = solicitudes.length;
    } catch (error) {
        window.alert(error.message);
    }
}

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja la solicitud
async function eliminarSolicitud(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar la solicitud?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_L: btnEliminar.dataset.id, cedula: btnEliminar.dataset.cedula });
        window.alert(respuesta.mensaje);
        cargarSolicitudes();
    } catch (error) {
        window.alert(error.message);
    }
}

//Baja de solicitudes desde el botón eliminar de cada fila
cuerpoTablaSolicitudes.addEventListener("click", eliminarSolicitud);

//Carga inicial de la tabla
cargarSolicitudes();

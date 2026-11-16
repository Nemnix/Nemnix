/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de solicitudes, pidiendo el resumen por laboratorio
const API_SOLICITUDES = "../index.php?ruta=solicitudes&resumen=1";

//Constante para trabajar con la tabla del resumen
const cuerpoTablaResumen = document.querySelector("#VizualizacionGlobalSolicitudes tbody");

//Contador de solicitudes pendientes
const solicitudesPendientes = document.getElementById("SolicitudesPendientes");


/**
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API y devuelve los datos de la respuesta
async function llamarApi(metodo) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

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
 * TABLA DEL RESUMEN
 */

//Crea la fila de un laboratorio. Usa textContent para que los datos no se interpreten como HTML
function crearFila(laboratorio) {
    const fila = document.createElement("tr");

    for (const valor of [laboratorio.nombreLaboratorio, laboratorio.numLaboratorio, laboratorio.cantidadSolicitudes]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    return fila;
}

//Pide el resumen a la API, dibuja la tabla y actualiza el contador
async function cargarResumen() {
    try {
        const resumen = await llamarApi("GET");
        cuerpoTablaResumen.replaceChildren(...resumen.porLaboratorio.map(crearFila));
        solicitudesPendientes.textContent = resumen.pendientes;
    } catch (error) {
        window.alert(error.message);
    }
}

//Carga inicial de la tabla
cargarResumen();

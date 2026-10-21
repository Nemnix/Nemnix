/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de incidencias
const API_INCIDENCIAS = "../index.php?ruta=incidencias";

//Constante para trabajar con la tabla de incidencias por laboratorio
const cuerpoTablaIncidencias = document.querySelector("#VizualizacionIncidencias tbody");

//Contadores
const incidenciasAlta = document.getElementById("IncidenciasAlta");
const incidenciasSinResolver = document.getElementById("IncidenciasSinResolver");
const incidenciasAutorizacion = document.getElementById("IncidenciasAutorización");


/**
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API y devuelve los datos de la respuesta
async function llamarApi(metodo) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

    const respuesta = await fetch(API_INCIDENCIAS, opciones);
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
 * TABLA DE INCIDENCIAS
 */

//Crea la fila de un laboratorio. Usa textContent para que los datos no se interpreten como HTML
function crearFila(laboratorio) {
    const fila = document.createElement("tr");

    for (const valor of [laboratorio.nombreLaboratorio, laboratorio.numLaboratorio, laboratorio.cantidadIncidencias]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    return fila;
}

//Pide las incidencias a la API, dibuja la tabla y actualiza los contadores
async function cargarIncidencias() {
    try {
        const incidencias = await llamarApi("GET");
        cuerpoTablaIncidencias.replaceChildren(...incidencias.porLaboratorio.map(crearFila));
        incidenciasAlta.textContent = incidencias.prioridadAltaSinResolver;
        incidenciasSinResolver.textContent = incidencias.sinResolver;
        incidenciasAutorizacion.textContent = incidencias.requierenAutorizacion;
    } catch (error) {
        window.alert(error.message);
    }
}

//Carga inicial de la tabla
cargarIncidencias();

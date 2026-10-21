/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de incidencias, pidiendo las métricas por laboratorio
const API_INCIDENCIAS = "../index.php?ruta=incidencias&metricas=1";

//Constante para trabajar con la tabla de métricas
const cuerpoTablaMetricas = document.querySelector("#VizualizacionGlobalEquipos tbody");


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
 * TABLA DE MÉTRICAS
 */

//Crea la fila de un laboratorio. Usa textContent para que los datos no se interpreten como HTML
function crearFila(laboratorio) {
    const fila = document.createElement("tr");

    for (const valor of [laboratorio.nombreLaboratorio, laboratorio.numLaboratorio, laboratorio.incidenciasActivas, laboratorio.equipoMasFallos, laboratorio.turnoMasReportes, laboratorio.totalIncidencias]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    return fila;
}

//Pide las métricas a la API y dibuja la tabla
async function cargarMetricas() {
    try {
        const metricas = await llamarApi("GET");
        cuerpoTablaMetricas.replaceChildren(...metricas.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

//Carga inicial de la tabla
cargarMetricas();

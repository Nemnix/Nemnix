/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de incidencias, pidiendo el estado de los equipos
const API_INCIDENCIAS = "../api/incidencias.php?equipos=1";

//La tabla tiene una sola fila de datos (la segunda), debajo de los encabezados
const filaEstadoEquipos = document.getElementById("Metricas").rows[1];


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
        window.location.replace("./login.html");
    }
    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "Error en la solicitud.");
    }

    return json.datos;
}


/**
 * TABLA DE ESTADO DE EQUIPOS
 */

//Pide el estado de los equipos a la API y completa la fila. Usa textContent para que los datos no se interpreten como HTML
async function cargarEstadoEquipos() {
    try {
        const estado = await llamarApi("GET");
        const celdas = [estado.totalComputadoras, estado.totalPerifericos, estado.incidenciasActivas, estado.totalIncidencias].map(function (valor) {
            const celda = document.createElement("td");
            celda.textContent = valor;
            return celda;
        });
        filaEstadoEquipos.replaceChildren(...celdas);
    } catch (error) {
        window.alert(error.message);
    }
}

//Carga inicial de la tabla
cargarEstadoEquipos();

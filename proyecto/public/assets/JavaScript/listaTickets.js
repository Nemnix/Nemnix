/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de tickets
const API_TICKETS = "../index.php?ruta=tickets";

//Constante para trabajar con la tabla de tickets
const cuerpoTablaTickets = document.querySelector("#tablaTickets tbody");


/**
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API y devuelve los datos de la respuesta
async function llamarApi(metodo) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

    const respuesta = await fetch(API_TICKETS, opciones);
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
 * TABLA DE TICKETS
 */

//Crea la fila de un ticket. Usa textContent para que los datos no se interpreten como HTML
function crearFila(ticket) {
    const fila = document.createElement("tr");

    for (const valor of [ticket.ID_T, ticket.numLaboratorio, ticket.nombreAlumno, ticket.nombreEquipo, ticket.tipoDispositivo, ticket.descripcionT, ticket.nombreLaboratorio, ticket.fechaRegistro]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    return fila;
}

//Pide los tickets del solicitante a la API y dibuja la tabla
async function cargarTickets() {
    try {
        const tickets = await llamarApi("GET");
        cuerpoTablaTickets.replaceChildren(...tickets.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

//Carga inicial de la tabla
cargarTickets();



const filtroEstado = document.getElementById("idticketEstadosfiltro");

//Dibuja la tabla solo con los tickets que tienen el estado elegido
async function aplicarFiltros() {
    const estado = filtroEstado.value;

    try {
        const tickets = await llamarApi("GET");
        const filtrados = tickets.filter(ticket => estado === "Todos" || ticket.estado === estado);
        cuerpoTablaTickets.replaceChildren(...filtrados.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

filtroEstado.addEventListener("change", aplicarFiltros);

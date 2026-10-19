// const tablaTicket = document.getElementById("tablaTickets")


// function agregarTicket(ticket) {

//     let filaTicket = document.createElement("tr");

//     let tdId = document.createElement("td");
//     tdId.innerText = ticket.id;

//     let tdNumAula = document.createElement("td");
//     tdNumAula.innerText = ticket.numAula;

//     let tdNombreAlumno = document.createElement("td");
//     tdNombreAlumno.innerText = ticket.nombreAlumno;

//     let tdNombreDispositivo = document.createElement("td");
//     tdNombreDispositivo.innerText = ticket.nombreDispositivo;

//     let tdIdDispositivo = document.createElement("td");
//     tdIdDispositivo.innerText = ticket.idDispositivo;

//     let tdMotivoTicket = document.createElement("td");
//     tdMotivoTicket.innerText = ticket.motivoTicket;

//     let tdTipoAula = document.createElement("td");
//     tdTipoAula.innerText = ticket.tipoAula;

//     let tdFecha = document.createElement("td");
//     tdFecha.innerText = ticket.fecha;

//     filaTicket.appendChild(tdId);
//     filaTicket.appendChild(tdNumAula);
//     filaTicket.appendChild(tdNombreAlumno);
//     filaTicket.appendChild(tdNombreDispositivo);
//     filaTicket.appendChild(tdIdDispositivo);
//     filaTicket.appendChild(tdMotivoTicket);
//     filaTicket.appendChild(tdTipoAula);
//     filaTicket.appendChild(tdFecha);

//     tablaTicket.appendChild(filaTicket);
// }
// function actualizarTablaTicket() {

//     while (tablaTicket.rows.length > 1) {
//         tablaTicket.deleteRow(1);
//     }

//     const tickets = cargarTicketsLocal();

//     for (const ticket of tickets) {
//         agregarTicket(ticket);
//     }
// }
// function cargarTicketsLocal() {

//     const ticketsGuardados = localStorage.getItem("tickets");

//     if (ticketsGuardados === null) {
//         return [];
//     }

//     return JSON.parse(ticketsGuardados);

// }
// actualizarTablaTicket()


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

    //El tipo de dispositivo se guarda junto con el nombre en nombreEquipo, por eso esa columna queda vacía
    for (const valor of [ticket.ID_T, ticket.numLaboratorio, ticket.nombreAlumno, ticket.nombreEquipo, "", ticket.descripcionT, ticket.nombreLaboratorio, ticket.fechaRegistro]) {
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

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
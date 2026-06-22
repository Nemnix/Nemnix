const tablaTicket = document.getElementById("tablaTickets")


function agregarTicket(ticket) {

    let filaTicket = document.createElement("tr");

    let tdId = document.createElement("td");
    tdId.innerText = ticket.id;

    let tdIdDispositivo = document.createElement("td");
    tdIdDispositivo.innerText = ticket.idDispositivo;

    let tdNombreAlumno = document.createElement("td");
    tdNombreAlumno.innerText = ticket.nombreAlumno;

    let tdNombreDispositivo = document.createElement("td");
    tdNombreDispositivo.innerText = ticket.nombreDispositivo;

    let tdMotivoTicket = document.createElement("td");
    tdMotivoTicket.innerText = ticket.motivoTicket;

    filaTicket.appendChild(tdId);
    filaTicket.appendChild(tdIdDispositivo);
    filaTicket.appendChild(tdNombreAlumno);
    filaTicket.appendChild(tdNombreDispositivo);
    filaTicket.appendChild(tdMotivoTicket);

    tablaTicket.appendChild(filaTicket);
}
function actualizarTablaTicket() {

    while (tablaTicket.rows.length > 1) {
        tablaTicket.deleteRow(1);
    }

    const tickets = cargarTicketsLocal();

    for (const ticket of tickets) {
        agregarTicket(ticket);
    }
}
function cargarTicketsLocal() {

    const ticketsGuardados = localStorage.getItem("tickets");

    if (ticketsGuardados === null) {
        return [];
    }

    return JSON.parse(ticketsGuardados);

}
actualizarTablaTicket()
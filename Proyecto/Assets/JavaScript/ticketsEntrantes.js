/**
 * CONSTANTES Y VARIABLES
 */

// dialog
const modal = document.getElementById("RegistroyTicket");

// formulario
const formulario = document.getElementById("FormularioRegistroyTicket");

// botones
const btnCerrarTicket = document.getElementById("CerrarTicket");

// tabla
const tabla = document.getElementById("estadoTickets");

// filtros
const filtroPrioridad = document.getElementById("IDprioridad");
const filtroEstado = document.getElementById("IDestado");
const filtroTiempo = document.getElementById("IDtiempo");

// campos del formulario (modal)
const entradaTipoAula = document.getElementById("IDLaboratorio");
const entradaNumAula = document.getElementById("numLabTaller");
const entradaAsignatura = document.getElementById("asignaturaRegistroDiario");
const entradaHoraEntrada = document.getElementById("horaEntrada");
const entradaHoraSalida = document.getElementById("horaSalida");
const entradaGrupo = document.getElementById("grupoRegistroDiario");
const entradaTurno = document.getElementById("turnoRegistroDiario");
const entradaIdDispositivo = document.getElementById("IDDispositivo");
const entradaNombreAlumno = document.getElementById("nombreAlumno");
const entradaNombreDispositivo = document.getElementById("nombreDispositivo");
const entradaMotivo = document.getElementById("motivoTicket");
const entradaPrioridad = document.getElementById("prioridadTicket");
const entradaEstado = document.getElementById("estadoTicket");

// variable global que guarda el ticket que se esta viendo/modificando
let ticketEditando = null;


/**
 * LOCAL STORAGE
 */

function cargarTicketsLocal() {

    const ticketsGuardados = localStorage.getItem("tickets");

    if (ticketsGuardados === null) {
        return [];
    }

    return JSON.parse(ticketsGuardados);

}

function actualizarTicketsLocal(tickets) {

    localStorage.setItem(
        "tickets",
        JSON.stringify(tickets)
    );

}

function cargarRegistrosLocal() {

    const registrosGuardados = localStorage.getItem("registros");

    if (registrosGuardados === null) {
        return [];
    }

    return JSON.parse(registrosGuardados);

}

function buscarRegistroAsociado(ticket) {

    const registros = cargarRegistrosLocal();

    return registros.find(
        r => r.numAula === ticket.numAula && r.fecha === ticket.fecha
    );

}

function modificarTicketLocal(ticketEditado) {

    const tickets = cargarTicketsLocal();

    const ticket = tickets.find(
        t => t.id === ticketEditado.id
    );

    if (ticket === undefined) {
        return;
    }

    ticket.prioridad = ticketEditado.prioridad;
    ticket.estado = ticketEditado.estado;

    actualizarTicketsLocal(tickets);
}


/**
 * FILTRADO Y ORDEN
 */

function obtenerTicketsFiltrados() {

    let tickets = cargarTicketsLocal();

    const prioridadSeleccionada = filtroPrioridad.value;

    if (prioridadSeleccionada !== "Todos") {
        tickets = tickets.filter(
            t => (t.prioridad ?? "Sin definir") === prioridadSeleccionada
        );
    }

    const estadoSeleccionado = filtroEstado.value;

    if (estadoSeleccionado !== "Todos") {
        tickets = tickets.filter(
            t => (t.estado ?? "Pendiente") === estadoSeleccionado
        );
    }

    const tiempoSeleccionado = filtroTiempo.value;

    tickets.sort(function (a, b) {

        const fechaA = new Date(a.fecha);
        const fechaB = new Date(b.fecha);

        if (tiempoSeleccionado === "Mas antiguos") {
            return fechaA - fechaB;
        }

        return fechaB - fechaA;

    });

    return tickets;

}


/**
 * OBTENCION DE DATOS
 */

function obtenerDatosFormulario() {

    return {
        id: ticketEditando.id,
        prioridad: entradaPrioridad.value,
        estado: entradaEstado.value
    };

}


/**
 * GESTION DEL MODAL
 */

function cerrarVisualizacionTicket() {

    ticketEditando = null;
    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    ticketEditando = null;
    formulario.reset();

});

/**
 * GESTION DE LA TABLA
 */

function agregarTicket(ticket) {

    let fila = document.createElement("tr");

    let tdId = document.createElement("td");
    tdId.innerText = ticket.id;

    let tdPrioridad = document.createElement("td");
    tdPrioridad.innerText = ticket.prioridad ?? "Sin definir";

    let tdEstado = document.createElement("td");
    tdEstado.innerText = ticket.estado ?? "Pendiente";

    let tdFecha = document.createElement("td");
    tdFecha.innerText = ticket.fecha;

    let tdVisualizacion = document.createElement("td");
    let btnVisualizar = document.createElement("button");
    btnVisualizar.innerText = "Visualizar";
    btnVisualizar.onclick = function () {
        visualizar(ticket);
    };
    tdVisualizacion.appendChild(btnVisualizar);
    fila.appendChild(tdId);
    fila.appendChild(tdPrioridad);
    fila.appendChild(tdEstado);
    fila.appendChild(tdFecha);
    fila.appendChild(tdVisualizacion);
    tabla.appendChild(fila);

}

function actualizarTabla() {

    while (tabla.rows.length > 1) {
        tabla.deleteRow(1);
    }
    const tickets = obtenerTicketsFiltrados();
    for (const ticket of tickets) {
        agregarTicket(ticket);
    }

}

function visualizar(ticket) {
    ticketEditando = ticket;
    window.ticketEnVisualizacion = ticket;

    const registroAsociado = buscarRegistroAsociado(ticket);

    entradaTipoAula.value = ticket.tipoAula ?? "";
    entradaNumAula.value = ticket.numAula ?? "";
    entradaIdDispositivo.value = ticket.idDispositivo ?? "";
    entradaNombreAlumno.value = ticket.nombreAlumno ?? "";
    entradaNombreDispositivo.value = ticket.nombreDispositivo ?? "";
    entradaMotivo.value = ticket.motivoTicket ?? "";
    entradaPrioridad.value = ticket.prioridad ?? "Sin definir";
    entradaEstado.value = ticket.estado ?? "Pendiente";

    if (registroAsociado !== undefined) {
        entradaAsignatura.value = registroAsociado.asignatura ?? "";
        entradaHoraEntrada.value = registroAsociado.entrada ?? "";
        entradaHoraSalida.value = registroAsociado.salida ?? "";
        entradaGrupo.value = registroAsociado.grupo ?? "";
        entradaTurno.value = registroAsociado.turno ?? "";
    } else {
        entradaAsignatura.value = "";
        entradaHoraEntrada.value = "";
        entradaHoraSalida.value = "";
        entradaGrupo.value = "";
        entradaTurno.value = "";
    }
    modal.showModal();

}

btnCerrarTicket.addEventListener("click", cerrarVisualizacionTicket);

filtroPrioridad.addEventListener("change", actualizarTabla);

filtroEstado.addEventListener("change", actualizarTabla);

filtroTiempo.addEventListener("change", actualizarTabla);

formulario.addEventListener("submit", function (event) {

    event.preventDefault();
    const ticket = obtenerDatosFormulario();
    modificarTicketLocal(ticket);
    ticketEditando = null;
    actualizarTabla();
    modal.close();
});

actualizarTabla();
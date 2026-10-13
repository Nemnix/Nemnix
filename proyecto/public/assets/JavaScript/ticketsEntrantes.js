const modal = document.getElementById("RegistroyTicket");

const formulario = document.getElementById("FormularioRegistroyTicket");

const btnCerrarTicket = document.getElementById("CerrarTicket");

const tabla = document.getElementById("estadoTickets");

const filtroPrioridad = document.getElementById("IDprioridad");
const filtroEstado = document.getElementById("IDestado");
const filtroTiempo = document.getElementById("IDtiempo");

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
const entradaModificarIdT = document.getElementById("modificarTicketID_T");
const entradaModificarIdR = document.getElementById("modificarTicketID_R");

let ticketEditando = null;

//Dirección de la API de tickets
const API_TICKETS = "../api/tickets.php";

//Constante para trabajar con el cuerpo de la tabla de tickets
const cuerpoTablaTickets = tabla.tBodies[0];


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

    const respuesta = await fetch(API_TICKETS, opciones);
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
 * TABLA DE TICKETS
 */

//Crea la fila de un ticket. Usa textContent para que los datos no se interpreten como HTML
function crearFila(ticket) {
    const fila = document.createElement("tr");

    for (const valor of [ticket.ID_T, ticket.prioridad, ticket.estado, ticket.fechaRegistro]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    //El botón Visualizar guarda el ticket completo para cargarlo en el modal
    const celdaAcciones = document.createElement("td");
    const btnVisualizar = document.createElement("button");
    btnVisualizar.type = "button";
    btnVisualizar.className = "btnVisualizar";
    btnVisualizar.textContent = "Visualizar";
    btnVisualizar.dataset.ticket = JSON.stringify(ticket);

    celdaAcciones.appendChild(btnVisualizar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide los tickets a la API y dibuja la tabla
async function cargarTickets() {
    try {
        const tickets = await llamarApi("GET");
        cuerpoTablaTickets.replaceChildren(...tickets.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}


/**
 * LOCAL STORAGE
 */

// function cargarTicketsLocal() {

    // const ticketsGuardados = localStorage.getItem("tickets");

    // if (ticketsGuardados === null) {
        // return [];
    // }

    // return JSON.parse(ticketsGuardados);

// }

// function actualizarTicketsLocal(tickets) {

    // localStorage.setItem(
        // "tickets",
        // JSON.stringify(tickets)
    // );

// }

// function cargarRegistrosLocal() {

    // const registrosGuardados = localStorage.getItem("registros");

    // if (registrosGuardados === null) {
        // return [];
    // }

    // return JSON.parse(registrosGuardados);

// }

// function buscarRegistroAsociado(ticket) {

    // const registros = cargarRegistrosLocal();

    // return registros.find(
        // r => r.numAula === ticket.numAula && r.fecha === ticket.fecha
    // );

// }

// function modificarTicketLocal(ticketEditado) {

    // const tickets = cargarTicketsLocal();

    // const ticket = tickets.find(
        // t => t.id === ticketEditado.id
    // );

    // if (ticket === undefined) {
        // return;
    // }

    // ticket.prioridad = ticketEditado.prioridad;
    // ticket.estado = ticketEditado.estado;

    // actualizarTicketsLocal(tickets);
// }


// function obtenerTicketsFiltrados() {

    // let tickets = cargarTicketsLocal();

    // const prioridadSeleccionada = filtroPrioridad.value;

    // if (prioridadSeleccionada !== "Todos") {
        // tickets = tickets.filter(
            // t => (t.prioridad ?? "Sin definir") === prioridadSeleccionada
        // );
    // }

    // const estadoSeleccionado = filtroEstado.value;

    // if (estadoSeleccionado !== "Todos") {
        // tickets = tickets.filter(
            // t => (t.estado ?? "Pendiente") === estadoSeleccionado
        // );
    // }

    // const tiempoSeleccionado = filtroTiempo.value;

    // tickets.sort(function (a, b) {

        // const fechaA = new Date(a.fecha);
        // const fechaB = new Date(b.fecha);

        // if (tiempoSeleccionado === "Mas antiguos") {
            // return fechaA - fechaB;
        // }

        // return fechaB - fechaA;

    // });

    // return tickets;

// }


// function obtenerDatosFormulario() {

    // return {
        // id: ticketEditando.id,
        // prioridad: entradaPrioridad.value,
        // estado: entradaEstado.value
    // };

// }

function cerrarVisualizacionTicket() {

    ticketEditando = null;
    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    ticketEditando = null;
    formulario.reset();

});

// Datos del registro diario asociado: solo informativos, no se modifican desde este formulario.
const camposSoloLectura = [
    entradaTipoAula,
    entradaNumAula,
    entradaAsignatura,
    entradaHoraEntrada,
    entradaHoraSalida,
    entradaGrupo,
    entradaTurno,
    entradaIdDispositivo
];

// Datos propios del ticket: editables al visualizarlo.
const camposEditablesTicket = [
    entradaNombreAlumno,
    entradaNombreDispositivo,
    entradaMotivo,
    entradaPrioridad,
    entradaEstado
];

function visualizar(ticket) {

    ticketEditando = ticket;
    window.ticketEnVisualizacion = ticket;

    entradaTipoAula.value = ticket.nombreLaboratorio ?? "";
    entradaNumAula.value = ticket.numLaboratorio ?? "";
    entradaAsignatura.value = ticket.asignatura ?? "";
    entradaHoraEntrada.value = ticket.horaEntrada ?? "";
    entradaHoraSalida.value = ticket.horaFin ?? "";
    entradaGrupo.value = ticket.grupo ?? "";
    entradaTurno.value = ticket.turno ?? "";
    entradaIdDispositivo.value = "";
    entradaNombreAlumno.value = ticket.nombreAlumno ?? "";
    entradaNombreDispositivo.value = ticket.nombreEquipo ?? "";
    entradaMotivo.value = ticket.descripcionT ?? "";
    entradaPrioridad.value = ticket.prioridad ?? "Sin definir";
    entradaEstado.value = ticket.estado ?? "Pendiente";

    entradaModificarIdT.value = ticket.ID_T;
    entradaModificarIdR.value = ticket.ID_R;

    camposSoloLectura.forEach(function (campo) {
        campo.disabled = true;
    });
    camposEditablesTicket.forEach(function (campo) {
        campo.disabled = false;
    });

    modal.showModal();

}

function abrirVisualizacionTicket(eventoVisualizar) {
    const btnVisualizar = eventoVisualizar.target.closest(".btnVisualizar");
    if (btnVisualizar === null) return;

    visualizar(JSON.parse(btnVisualizar.dataset.ticket));
}

// function agregarTicket(ticket) {

    // let fila = document.createElement("tr");

    // let tdId = document.createElement("td");
    // tdId.innerText = ticket.id;

    // let tdPrioridad = document.createElement("td");
    // tdPrioridad.innerText = ticket.prioridad ?? "Sin definir";

    // let tdEstado = document.createElement("td");
    // tdEstado.innerText = ticket.estado ?? "Pendiente";

    // let tdFecha = document.createElement("td");
    // tdFecha.innerText = ticket.fecha;

    // let tdVisualizacion = document.createElement("td");
    // let btnVisualizar = document.createElement("button");
    // btnVisualizar.innerText = "Visualizar";
    // btnVisualizar.onclick = function () {
        // visualizar(ticket);
    // };
    // tdVisualizacion.appendChild(btnVisualizar);
    // fila.appendChild(tdId);
    // fila.appendChild(tdPrioridad);
    // fila.appendChild(tdEstado);
    // fila.appendChild(tdFecha);
    // fila.appendChild(tdVisualizacion);
    // tabla.appendChild(fila);

// }

// function actualizarTabla() {

    // while (tabla.rows.length > 1) {
        // tabla.deleteRow(1);
    // }
    // const tickets = obtenerTicketsFiltrados();
    // for (const ticket of tickets) {
        // agregarTicket(ticket);
    // }

// }

// function visualizar(ticket) {
    // ticketEditando = ticket;
    // window.ticketEnVisualizacion = ticket;

    // const registroAsociado = buscarRegistroAsociado(ticket);

    // entradaTipoAula.value = ticket.tipoAula ?? "";
    // entradaNumAula.value = ticket.numAula ?? "";
    // entradaIdDispositivo.value = ticket.idDispositivo ?? "";
    // entradaNombreAlumno.value = ticket.nombreAlumno ?? "";
    // entradaNombreDispositivo.value = ticket.nombreDispositivo ?? "";
    // entradaMotivo.value = ticket.motivoTicket ?? "";
    // entradaPrioridad.value = ticket.prioridad ?? "Sin definir";
    // entradaEstado.value = ticket.estado ?? "Pendiente";

    // if (registroAsociado !== undefined) {
        // entradaAsignatura.value = registroAsociado.asignatura ?? "";
        // entradaHoraEntrada.value = registroAsociado.entrada ?? "";
        // entradaHoraSalida.value = registroAsociado.salida ?? "";
        // entradaGrupo.value = registroAsociado.grupo ?? "";
        // entradaTurno.value = registroAsociado.turno ?? "";
    // } else {
        // entradaAsignatura.value = "";
        // entradaHoraEntrada.value = "";
        // entradaHoraSalida.value = "";
        // entradaGrupo.value = "";
        // entradaTurno.value = "";
    // }
    // modal.showModal();

// }

// btnCerrarTicket.addEventListener("click", cerrarVisualizacionTicket);

// filtroPrioridad.addEventListener("change", actualizarTabla);

// filtroEstado.addEventListener("change", actualizarTabla);

// filtroTiempo.addEventListener("change", actualizarTabla);

// formulario.addEventListener("submit", function (event) {

    // event.preventDefault();
    // const ticket = obtenerDatosFormulario();
    // modificarTicketLocal(ticket);
    // ticketEditando = null;
    // actualizarTabla();
    // modal.close();
// });

// actualizarTabla();

//Envía los cambios del ticket a la API con PUT
async function gestionarTicket(evento) {
    evento.preventDefault();

    const datos = {
        ID_R: entradaModificarIdR.value,
        ID_T: entradaModificarIdT.value,
        estado: entradaEstado.value,
        nombreEquipo: entradaNombreDispositivo.value.trim(),
        nombreAlumno: entradaNombreAlumno.value.trim(),
        descripcion: entradaMotivo.value.trim(),
        prioridad: entradaPrioridad.value
    };

    try {
        const respuesta = await llamarApi("PUT", datos);
        window.alert(respuesta.mensaje);
        cerrarVisualizacionTicket();
        cargarTickets();
    } catch (error) {
        window.alert(error.message);
    }
}

tabla.addEventListener("click", abrirVisualizacionTicket);
btnCerrarTicket.addEventListener("click", cerrarVisualizacionTicket);

formulario.addEventListener("submit", gestionarTicket);

//Carga inicial de la tabla
cargarTickets();
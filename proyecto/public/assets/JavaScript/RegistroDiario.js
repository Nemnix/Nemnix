const modal = document.getElementById("RegistroSolicitante");
const formulario = document.getElementById("FormularioRegistroDiario");

const modalTicket = document.getElementById("TicketSolicitante");
const formularioTicket = document.getElementById("FormularioTickets");

const tabla = document.getElementById("registroDiario");
const tablaTicketModal = document.getElementById("cuerpoTicketModal");

const btnAgregarRegistro = document.getElementById("btnAgregarRegistro");
const btnCerrarRegistro = document.getElementById("btnCerrarRegistro");
const btnCerrarTicket = document.getElementById("btnCerrarTicket");

const entradaIdRegistro = document.getElementById("ID_R");
const entradaNumAula = document.getElementById("numLabTaller");
const entradaTipoAula = document.getElementById("IDLaboratorio");
const entradaFecha = document.getElementById("fechaRegistroDiario");
const entradaAsignatura = document.getElementById("asignaturaRegistroDiario");
const entradaHoraSalida = document.getElementById("horaSalida");
const entradaHoraEntrada = document.getElementById("horaEntrada");
const entradaGrupo = document.getElementById("grupoRegistroDiario");
const entradaTurno = document.getElementById("turnoRegistroDiario");

const entradaIdRegistroTicket = document.getElementById("ID_R_Ticket");
const entradaIdDispositivo = document.getElementById("idDispositivo");
const entradaNombreAlumno = document.getElementById("nombreAlumno");
const entradaNombreDispositivo = document.getElementById("nombreDispositivo");
const entradaMotivoTicket = document.getElementById("motivoTicket");


let filaEditandoTicket = null
let idEditandoTicket = null;
let filaEditando = null;
let idEditando = null;

// function cargarRegistrosLocal() {

//     const registrosGuardados = localStorage.getItem("registros");

//     if (registrosGuardados === null) {
//         return [];
//     }

//     return JSON.parse(registrosGuardados);

// }

// function actualizarRegistrosLocal(registros) {

//     localStorage.setItem(
//         "registros",
//         JSON.stringify(registros)
//     );

// }

// function guardarRegistroLocal(registro) {

//     const registros = cargarRegistrosLocal();

//     registros.push(registro);

//     actualizarRegistrosLocal(registros);

// }
// function modificarRegistroLocal(registroEditado) {

//     const registros = cargarRegistrosLocal();

//     const registro = registros.find(
//         r => r.id === registroEditado.id
//     );

//     if (registro === undefined) {
//         return;
//     }

//     registro.fecha = registroEditado.fecha;
//     registro.numAula = registroEditado.numAula;
//     registro.asignatura = registroEditado.asignatura;
//     registro.entrada = registroEditado.entrada;
//     registro.salida = registroEditado.salida;
//     registro.grupo = registroEditado.grupo;
//     registro.turno = registroEditado.turno;

//     actualizarRegistrosLocal(registros);
// }

// function eliminarRegistroLocal(id) {

//     const registros = cargarRegistrosLocal();

//     const registrosActualizados = registros.filter(
//         r => r.id !== Number(id)
//     );

//     actualizarRegistrosLocal(registrosActualizados);
// }

// function actualizarTabla() {

//     while (tabla.rows.length > 1) {
//         tabla.deleteRow(1);
//     }

//     const registros = cargarRegistrosLocal();

//     for (const registro of registros) {
//         agregarRegistro(registro);
//     }
// }

// function generarIdRegistro() {
//     let ultimoId = Number(localStorage.getItem("ultimoIdRegistro")) || 0;

//     ultimoId++;

//     localStorage.setItem("ultimoIdRegistro", ultimoId);

//     return ultimoId;
// }

// function obtenerDatosFormulario() {

//     let id;

//     if (filaEditando === null) {
//         id = generarIdRegistro();
//     } else {
//         id = idEditando;
//     }

//     return {
//         id: id,
//         tipoAula: entradaTipoAula.value,
//         fecha: entradaFecha.value,
//         numAula: entradaNumAula.value,
//         asignatura: entradaAsignatura.value,
//         entrada: entradaHoraEntrada.value,
//         salida: entradaHoraSalida.value,
//         grupo: entradaGrupo.value,
//         turno: entradaTurno.value
//     };

// }

function abrirAltaRegistro() {

    filaEditando = null;
    formulario.reset();
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarAltaRegistros.php";
    modal.showModal();

}

function cerrarAltaRegistro() {

    filaEditando = null;
    formulario.reset();
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarAltaRegistros.php";
    modal.close();

}

modal.addEventListener("cancel", function () {

    filaEditando = null;
    formulario.reset();
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarAltaRegistros.php";

});

function abrirTicket() {

    filaEditandoTicket = null;
    idEditandoTicket = null;
    formularioTicket.reset();
    actualizarTablaTicketModal();
    modalTicket.showModal();

}

function cerrarTicket() {

    filaEditandoTicket = null;
    idEditandoTicket = null;
    formularioTicket.reset();
    modalTicket.close();

}

modalTicket.addEventListener("cancel", function () {

    filaEditandoTicket = null;
    idEditandoTicket = null;
    formularioTicket.reset();

});

// function agregarRegistro(registro) {

//     let fila = document.createElement("tr");

//     let tdId = document.createElement("td");
//     tdId.innerText = registro.id;

//     let tdFecha = document.createElement("td");
//     tdFecha.innerText = registro.fecha;

//     let tdTipoAula = document.createElement("td");
//     tdTipoAula.innerText = registro.tipoAula;

//     let tdNumAula = document.createElement("td");
//     tdNumAula.innerText = registro.numAula;

//     let tdEntrada = document.createElement("td");
//     tdEntrada.innerText = registro.entrada;

//     let tdSalida = document.createElement("td");
//     tdSalida.innerText = registro.salida;

//     let tdTurno = document.createElement("td");
//     tdTurno.innerText = registro.turno;

//     let tdGrupo = document.createElement("td");
//     tdGrupo.innerText = registro.grupo;

//     let tdAcciones = document.createElement("td");

//     let btnModificar = document.createElement("button");
//     btnModificar.innerText = "Modificar";
//     btnModificar.onclick = function () {
//         modificar(this);
//     };

//     let btnEliminar = document.createElement("button");
//     btnEliminar.innerText = "Eliminar";
//     btnEliminar.onclick = function () {
//         borrar(this);
//     };

//     tdAcciones.appendChild(btnModificar);
//     tdAcciones.appendChild(btnEliminar);

//     fila.appendChild(tdId);
//     fila.appendChild(tdFecha);
//     fila.appendChild(tdTipoAula);
//     fila.appendChild(tdNumAula);
//     fila.appendChild(tdEntrada);
//     fila.appendChild(tdSalida);
//     fila.appendChild(tdTurno);
//     fila.appendChild(tdGrupo);
//     fila.appendChild(tdAcciones);   

//     tabla.appendChild(fila);

// }

//     function borrar(boton) {

//     const fila = boton.parentNode.parentNode;

//     const id = Number(fila.children[0].innerText);

//     eliminarRegistroLocal(id);

//     actualizarTabla();
// }

//     function modificar(boton) {

//     filaEditando = boton.parentNode.parentNode;

//     const celdas = filaEditando.getElementsByTagName("td");

//     idEditando = Number(celdas[0].innerText);

//     entradaFecha.value = celdas[1].innerText;
//     entradaTipoAula.value = celdas[2].innerText;
//     entradaNumAula.value = celdas[3].innerText;
//     entradaHoraEntrada.value = celdas[4].innerText;
//     entradaHoraSalida.value = celdas[5].innerText;
//     entradaTurno.value = celdas[6].innerText;
//     entradaGrupo.value = celdas[7].innerText;

//     modal.showModal();
// }

// function agregarTicketModal(ticket) {

//     let fila = document.createElement("tr");

//     let tdTicket = document.createElement("td");
//     tdTicket.innerText = ticket.idDispositivo;

//     let tdProblema = document.createElement("td");
//     tdProblema.innerText = ticket.motivoTicket;

//     let tdAcciones = document.createElement("td");

//     let btnModificar = document.createElement("button");
//     btnModificar.innerText = "Modificar";
//     btnModificar.onclick = function () {
//         modificarTicket(ticket);
//     };

//     let btnEliminar = document.createElement("button");
//     btnEliminar.innerText = "Eliminar";
//     btnEliminar.onclick = function () {
//         borrarTicket(ticket);
//     };

//     tdAcciones.appendChild(btnModificar);
//     tdAcciones.appendChild(btnEliminar);

//     fila.appendChild(tdTicket);
//     fila.appendChild(tdProblema);
//     fila.appendChild(tdAcciones);

//     tablaTicketModal.appendChild(fila);

// }

// function actualizarTablaTicketModal() {

//     while (tablaTicketModal.rows.length > 1) {
//         tablaTicketModal.deleteRow(1);
//     }

//     const tickets = cargarTicketsLocal();

//     for (const ticket of tickets) {
//         agregarTicketModal(ticket);
//     }

// }

// function borrarTicket(ticket) {

//     eliminarTicketsLocal(ticket.id);

//     actualizarTablaTicketModal();

// }

// function modificarTicket(ticket) {

//     filaEditandoTicket = true;
//     idEditandoTicket = ticket.id;

//     entradaIdDispositivo.value = ticket.idDispositivo;
//     entradaNombreAlumno.value = ticket.nombreAlumno;
//     entradaNombreDispositivo.value = ticket.nombreDispositivo;
//     entradaMotivoTicket.value = ticket.motivoTicket;

//     modalTicket.showModal();

// }

// formulario.addEventListener("submit", function (event) {

//     event.preventDefault();

//     const registro = obtenerDatosFormulario();

//     if (filaEditando === null) {

//         guardarRegistroLocal(registro);

//     } else {

//         modificarRegistroLocal(registro);

//         filaEditando = null;
//     }

//     actualizarTabla();

//     modal.close();
// });

// actualizarTabla();




// function cargarTicketsLocal() {

//     const ticketsGuardados = localStorage.getItem("tickets");

//     if (ticketsGuardados === null) {
//         return [];
//     }

//     return JSON.parse(ticketsGuardados);

// }

// function actualizarTicketsLocal(tickets) {

//     localStorage.setItem(
//         "tickets",
//         JSON.stringify(tickets)
//     );

// }

// function guardarTicketsLocal(ticket) {

//     const tickets = cargarTicketsLocal();

//     tickets.push(ticket);

//     actualizarTicketsLocal(tickets);

// }
// function modificarTicketsLocal(ticketEditado) {

//     const tickets = cargarTicketsLocal();

//     const ticket = tickets.find(
//         t => t.id === ticketEditado.id
//     );

//     if (ticket === undefined) {
//         return;
//     }

//     ticket.idDispositivo = ticketEditado.idDispositivo;
//     ticket.nombreAlumno = ticketEditado.nombreAlumno;
//     ticket.nombreDispositivo = ticketEditado.nombreDispositivo;
//     ticket.motivoTicket = ticketEditado.motivoTicket;
//     ticket.numAula = ticketEditado.numAula;
//     ticket.tipoAula = ticketEditado.tipoAula;
//     ticket.fecha = ticketEditado.fecha;

//     actualizarTicketsLocal(tickets);
// }
// function eliminarTicketsLocal(idTicket) {

//     const tickets = cargarTicketsLocal();

//     const ticketsActualizados = tickets.filter(
//         t => t.id !== Number(idTicket)
//     );

//     actualizarTicketsLocal(ticketsActualizados);
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
// function generarIdTicket() {
//     let ultimoIdTicket = Number(localStorage.getItem("ultimoIdTicket")) || 0;

//     ultimoIdTicket++;

//     localStorage.setItem("ultimoIdTicket", ultimoIdTicket);

//     return ultimoIdTicket;
// }

// function obtenerDatosFormularioTicket() {

//     let idTicket;

//     if (filaEditandoTicket === null) {
//         idTicket = generarIdTicket();
//     } else {
//         idTicket = idEditandoTicket;
//     }

//     return {
//         id: idTicket,
//         idDispositivo: entradaIdDispositivo.value,
//         nombreAlumno: entradaNombreAlumno.value,
//         nombreDispositivo: entradaNombreDispositivo.value,
//         motivoTicket: entradaMotivoTicket.value,
//         numAula: entradaNumAula.value,
//         tipoAula: entradaTipoAula.value,
//         fecha: entradaFecha.value
//     };

// }
// formularioTicket.addEventListener("submit", function (event) {

//     event.preventDefault();

//     const ticket = obtenerDatosFormularioTicket();

//     if (filaEditandoTicket === null) {

//         guardarTicketsLocal(ticket);

//     } else {

//         modificarTicketsLocal(ticket);

//         filaEditandoTicket = null;
//         idEditandoTicket = null;
//     }

//     actualizarTablaTicketModal();
//     formularioTicket.reset();
// });
function abrirEditarRegistro(eventoModificar) {
    const btnEditar = eventoModificar.target.closest(".btnEditar");
    if (btnEditar === null) return;

    const fila = btnEditar.closest("tr");

    formulario.reset();
    filaEditando = fila;
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarModificarRegistro.php";

    entradaIdRegistro.value = btnEditar.dataset.id;

    entradaFecha.value     = fila.cells[0].textContent.trim(); // fecha
    entradaTipoAula.value  = fila.cells[1].textContent.trim(); // nombreLaboratorio
    entradaNumAula.value   = fila.cells[2].textContent.trim(); // numLaboratorio
    entradaHoraEntrada.value = fila.cells[3].textContent.trim(); // horaEntrada
    entradaHoraSalida.value  = fila.cells[4].textContent.trim(); // horaFin
    entradaTurno.value     = fila.cells[5].textContent.trim(); // turno
    entradaGrupo.value     = fila.cells[6].textContent.trim(); // grupo
    entradaAsignatura.value = fila.cells[7].textContent.trim(); // asignatura

    modal.showModal();
}

function renderizarTicketsModal(tickets) {

    tablaTicketModal.innerHTML = "";

    if (tickets.length === 0) {
        const filaVacia = document.createElement("tr");
        const celdaVacia = document.createElement("td");
        celdaVacia.colSpan = 3;
        celdaVacia.textContent = "No hay tickets cargados para este registro.";
        filaVacia.appendChild(celdaVacia);
        tablaTicketModal.appendChild(filaVacia);
        return;
    }

    tickets.forEach(function (ticket) {
        const fila = document.createElement("tr");

        const celdaTicket = document.createElement("td");
        celdaTicket.textContent = "Ticket " + ticket.ID_T;

        const celdaProblema = document.createElement("td");
        celdaProblema.textContent = ticket.descripcionT;

        const celdaAcciones = document.createElement("td");

        const formularioBajaTicket = document.createElement("form");
        formularioBajaTicket.method = "post";
        formularioBajaTicket.action = "/proyecto/public/usuarioSolicitante/procesarBajaTicket.php";
        formularioBajaTicket.className = "formularioBajaTicket";

        const entradaIdRegistroBaja = document.createElement("input");
        entradaIdRegistroBaja.type = "hidden";
        entradaIdRegistroBaja.name = "ID_R";
        entradaIdRegistroBaja.value = ticket.ID_R;

        const entradaIdTicketBaja = document.createElement("input");
        entradaIdTicketBaja.type = "hidden";
        entradaIdTicketBaja.name = "ID_T";
        entradaIdTicketBaja.value = ticket.ID_T;

        const btnEliminarTicket = document.createElement("button");
        btnEliminarTicket.type = "submit";
        btnEliminarTicket.className = "btnEliminar";
        btnEliminarTicket.textContent = "Eliminar";

        formularioBajaTicket.appendChild(entradaIdRegistroBaja);
        formularioBajaTicket.appendChild(entradaIdTicketBaja);
        formularioBajaTicket.appendChild(btnEliminarTicket);
        celdaAcciones.appendChild(formularioBajaTicket);

        fila.appendChild(celdaTicket);
        fila.appendChild(celdaProblema);
        fila.appendChild(celdaAcciones);

        tablaTicketModal.appendChild(fila);
    });
}

function abrirAltaTicket(eventoTicket) {
    const btnTicket = eventoTicket.target.closest(".btnAgregarTicket");
    if (btnTicket === null) return;

    filaEditandoTicket = null;
    idEditandoTicket = null;
    formularioTicket.reset();
    entradaIdRegistroTicket.value = btnTicket.dataset.id;

    renderizarTicketsModal(JSON.parse(btnTicket.dataset.tickets));

    modalTicket.showModal();
}

tabla.addEventListener("click", abrirEditarRegistro);
tabla.addEventListener("click", abrirAltaTicket);

btnAgregarRegistro.addEventListener("click", abrirAltaRegistro);
 btnCerrarRegistro.addEventListener("click", cerrarAltaRegistro);

btnCerrarTicket.addEventListener("click", cerrarTicket);

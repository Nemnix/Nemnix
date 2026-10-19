//Dirección de la API de registros
const API_REGISTROS = "../api/registros.php";

const modal = document.getElementById("RegistroSolicitante");
const formulario = document.getElementById("FormularioRegistroDiario");

const modalTicket = document.getElementById("TicketSolicitante");
const formularioTicket = document.getElementById("FormularioTickets");

const tabla = document.getElementById("registroDiario");
//Constante para trabajar con el cuerpo de la tabla de registros
const cuerpoTablaRegistros = document.getElementById("tablaRegistroDiario");
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
const entradaPrioridadTicket = document.getElementById("prioridadTicket");

//Dirección de la API de tickets
const API_TICKETS = "../api/tickets.php";


let filaEditandoTicket = null
let idEditandoTicket = null;
let filaEditando = null;
let idEditando = null;


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

    const respuesta = await fetch(API_REGISTROS, opciones);
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
 * TABLA DE REGISTROS
 */

//Crea la fila de un registro. Usa textContent para que los datos no se interpreten como HTML
function crearFila(registro) {
    const fila = document.createElement("tr");

    for (const valor of [registro.fecha, registro.nombreLaboratorio, registro.numLaboratorio, registro.horaEntrada, registro.horaFin, registro.turno, registro.grupo, registro.asignatura]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    const btnEditar = document.createElement("button");
    btnEditar.type = "button";
    btnEditar.className = "btnEditar";
    btnEditar.textContent = "Editar";
    btnEditar.dataset.id = registro.ID_R;

    const btnTicket = document.createElement("button");
    btnTicket.type = "button";
    btnTicket.className = "btnAgregarTicket";
    btnTicket.textContent = "Ticket";
    btnTicket.dataset.id = registro.ID_R;
    //Los tickets de cada registro los agrega cargarRegistros() desde la API de tickets
    btnTicket.dataset.tickets = JSON.stringify(registro.tickets ?? []);

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = registro.ID_R;

    celdaAcciones.append(btnEditar, btnTicket, btnEliminar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Envía la petición a la API de tickets con el token CSRF y devuelve los datos de la respuesta
async function llamarApiTickets(metodo, datos = null) {
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

//Pide los registros y los tickets a la API y dibuja la tabla
async function cargarRegistros() {
    try {
        const registros = await llamarApi("GET");
        const tickets = await llamarApiTickets("GET");

        //Cada registro guarda sus tickets, que el botón "Ticket" muestra en el modal
        registros.forEach(function (registro) {
            registro.tickets = tickets.filter(ticket => ticket.ID_R === registro.ID_R);
        });

        cuerpoTablaRegistros.replaceChildren(...registros.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

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
    modal.showModal();

}

function cerrarAltaRegistro() {

    filaEditando = null;
    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    filaEditando = null;
    formulario.reset();

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

        //El ticket se identifica por su registro y su número dentro del registro
        const btnEliminarTicket = document.createElement("button");
        btnEliminarTicket.type = "button";
        btnEliminarTicket.className = "btnEliminar btnEliminarTicket";
        btnEliminarTicket.textContent = "Eliminar";
        btnEliminarTicket.dataset.idRegistro = ticket.ID_R;
        btnEliminarTicket.dataset.idTicket = ticket.ID_T;

        celdaAcciones.appendChild(btnEliminarTicket);

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

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja el registro
async function eliminarRegistro(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar el registro?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_R: btnEliminar.dataset.id });
        window.alert(respuesta.mensaje);
        cargarRegistros();
    } catch (error) {
        window.alert(error.message);
    }
}

//Envía el formulario a la API: POST para el alta, PUT para la modificación
async function gestionarRegistro(evento) {
    evento.preventDefault();

    const datos = {
        ID_R: entradaIdRegistro.value,
        nombreLaboratorio: entradaTipoAula.value,
        numLaboratorio: entradaNumAula.value.trim(),
        fecha: entradaFecha.value,
        asignatura: entradaAsignatura.value.trim(),
        horaEntrada: entradaHoraEntrada.value,
        horaSalida: entradaHoraSalida.value,
        grupo: entradaGrupo.value.trim(),
        turno: entradaTurno.value
    };

    try {
        const metodo = filaEditando === null ? "POST" : "PUT";
        const respuesta = await llamarApi(metodo, datos);
        window.alert(respuesta.mensaje);
        cerrarAltaRegistro();
        cargarRegistros();
    } catch (error) {
        window.alert(error.message);
    }
}

tabla.addEventListener("click", abrirEditarRegistro);
tabla.addEventListener("click", abrirAltaTicket);

//Baja de registros desde el botón eliminar de cada fila
tabla.addEventListener("click", eliminarRegistro);

formulario.addEventListener("submit", gestionarRegistro);

btnAgregarRegistro.addEventListener("click", abrirAltaRegistro);
 btnCerrarRegistro.addEventListener("click", cerrarAltaRegistro);

btnCerrarTicket.addEventListener("click", cerrarTicket);

//Envía el ticket nuevo a la API con POST
async function registrarTicket(evento) {
    evento.preventDefault();

    const datos = {
        ID_R: entradaIdRegistroTicket.value,
        tipoDispositivo: entradaIdDispositivo.value.trim(),
        nombreDispositivo: entradaNombreDispositivo.value.trim(),
        nombreAlumno: entradaNombreAlumno.value.trim(),
        descripcion: entradaMotivoTicket.value.trim(),
        prioridad: entradaPrioridadTicket.value
    };

    try {
        const respuesta = await llamarApiTickets("POST", datos);
        window.alert(respuesta.mensaje);
        cerrarTicket();
        cargarRegistros();
    } catch (error) {
        window.alert(error.message);
    }
}

//Función que escucha los clicks de la tabla del modal, si es proveniente del botón eliminar da de baja el ticket
async function eliminarTicket(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminarTicket");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar el ticket?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApiTickets("DELETE", { ID_R: btnEliminar.dataset.idRegistro, ID_T: btnEliminar.dataset.idTicket });
        window.alert(respuesta.mensaje);
        cerrarTicket();
        cargarRegistros();
    } catch (error) {
        window.alert(error.message);
    }
}

formularioTicket.addEventListener("submit", registrarTicket);

//Baja de tickets desde el botón eliminar de cada fila del modal
tablaTicketModal.addEventListener("click", eliminarTicket);

//Carga inicial de la tabla
cargarRegistros();

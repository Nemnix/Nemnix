const modal = document.getElementById("RegistroyTicket");

const formulario = document.getElementById("FormularioRegistroyTicket");

const btnCerrarTicket = document.getElementById("CerrarTicket");

const tabla = document.getElementById("estadoTickets");

const filtroPrioridad = document.getElementById("IDprioridad");
const filtroEstado = document.getElementById("IDestado");
const filtroTiempo = document.getElementById("IDtiempo");

//Datos del registro y del ticket que se muestran como texto
const datoTipoAula = document.getElementById("datoTipoAula");
const datoNumAula = document.getElementById("datoNumAula");
const datoAsignatura = document.getElementById("datoAsignatura");
const datoHoraEntrada = document.getElementById("datoHoraEntrada");
const datoHoraSalida = document.getElementById("datoHoraSalida");
const datoGrupo = document.getElementById("datoGrupo");
const datoTurno = document.getElementById("datoTurno");
const datoTipoDispositivo = document.getElementById("datoTipoDispositivo");
const datoNombreAlumno = document.getElementById("datoNombreAlumno");
const datoNombreDispositivo = document.getElementById("datoNombreDispositivo");
const datoMotivo = document.getElementById("datoMotivo");

const entradaPrioridad = document.getElementById("prioridadTicket");
const entradaEstado = document.getElementById("estadoTicket");
const entradaModificarIdT = document.getElementById("modificarTicketID_T");
const entradaModificarIdR = document.getElementById("modificarTicketID_R");

let ticketEditando = null;

//Dirección de la API de tickets
const API_TICKETS = "../index.php?ruta=tickets";

//Constante para trabajar con el cuerpo de la tabla de tickets
const cuerpoTablaTickets = tabla.tBodies[0];

//Tickets traídos de la API, para filtrarlos sin volver a pedirlos
let todosLosTickets = [];

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


async function cargarTickets() {
    try {
        todosLosTickets = await llamarApi("GET");
        aplicarFiltros();
    } catch (error) {
        window.alert(error.message);
    }
}

//Dibuja la tabla con los tickets que cumplen los filtros, en el orden elegido
function aplicarFiltros() {
    const prioridad = filtroPrioridad.value;
    const estado = filtroEstado.value;

    const filtrados = todosLosTickets
        .filter(ticket => prioridad === "Todos" || ticket.prioridad === prioridad)
        .filter(ticket => estado === "Todos" || ticket.estado === estado)
        .sort((a, b) => filtroTiempo.value === "Más recientes"
            ? b.fechaRegistro.localeCompare(a.fechaRegistro)
            : a.fechaRegistro.localeCompare(b.fechaRegistro));

    cuerpoTablaTickets.replaceChildren(...filtrados.map(crearFila));
}

filtroPrioridad.addEventListener("change", aplicarFiltros);
filtroEstado.addEventListener("change", aplicarFiltros);
filtroTiempo.addEventListener("change", aplicarFiltros);


function cerrarVisualizacionTicket() {

    ticketEditando = null;
    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    ticketEditando = null;
    formulario.reset();

});

function visualizar(ticket) {

    ticketEditando = ticket;
    window.ticketEnVisualizacion = ticket;

    //Datos del registro y del ticket: solo se muestran, no se modifican
    datoTipoAula.textContent = ticket.nombreLaboratorio ?? "-";
    datoNumAula.textContent = ticket.numLaboratorio ?? "-";
    datoAsignatura.textContent = ticket.asignatura ?? "-";
    datoHoraEntrada.textContent = ticket.horaEntrada ?? "-";
    datoHoraSalida.textContent = ticket.horaFin ?? "-";
    datoGrupo.textContent = ticket.grupo ?? "-";
    datoTurno.textContent = ticket.turno ?? "-";
    datoTipoDispositivo.textContent = ticket.tipoDispositivo || "-";
    datoNombreAlumno.textContent = ticket.nombreAlumno ?? "-";
    datoNombreDispositivo.textContent = ticket.nombreEquipo ?? "-";
    datoMotivo.textContent = ticket.descripcionT ?? "-";

    //Prioridad y estado siguen siendo editables
    entradaPrioridad.value = ticket.prioridad ?? "Sin definir";
    entradaEstado.value = ticket.estado ?? "Pendiente";

    entradaModificarIdT.value = ticket.ID_T;
    entradaModificarIdR.value = ticket.ID_R;

    modal.showModal();

}

function abrirVisualizacionTicket(eventoVisualizar) {
    const btnVisualizar = eventoVisualizar.target.closest(".btnVisualizar");
    if (btnVisualizar === null) return;

    visualizar(JSON.parse(btnVisualizar.dataset.ticket));
}


//Envía los cambios del ticket a la API con PUT
async function gestionarTicket(evento) {
    evento.preventDefault();

    const datos = {
        ID_R: entradaModificarIdR.value,
        ID_T: entradaModificarIdT.value,
        estado: entradaEstado.value,
        tipoDispositivo: ticketEditando.tipoDispositivo ?? "",
        nombreEquipo: ticketEditando.nombreEquipo ?? "",
        nombreAlumno: ticketEditando.nombreAlumno ?? "",
        descripcion: ticketEditando.descripcionT ?? "",
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







const modal = document.getElementById("RegistroSolicitante");
const formulario = document.getElementById("FormularioRegistroDiario");

const modalTicket = document.getElementById("TicketSolicitante");
const formularioTicket = document.getElementById("FormularioTickets");

const tabla = document.getElementById("registroDiario");

const entradaNumAula = document.getElementById("numLabTaller");
const entradaTipoAula = document.getElementById("IDLaboratorio");
const entradaFecha = document.getElementById("fechaRegistroDiario");
const entradaAsignatura = document.getElementById("asignaturaRegistroDiario");
const entradaHoraSalida = document.getElementById("horaSalida");
const entradaHoraEntrada = document.getElementById("horaEntrada");
const entradaGrupo = document.getElementById("grupoRegistroDiario");
const entradaTurno = document.getElementById("turnoRegistroDiario");

let filaEditando = null;
let idEditando = null;

function cargarRegistrosLocal() {

    const registrosGuardados = localStorage.getItem("registros");

    if (registrosGuardados === null) {
        return [];
    }

    return JSON.parse(registrosGuardados);

}

function actualizarRegistrosLocal(registros) {

    localStorage.setItem(
        "registros",
        JSON.stringify(registros)
    );

}

function guardarRegistroLocal(registro) {

    const registros = cargarRegistrosLocal();

    registros.push(registro);

    actualizarRegistrosLocal(registros);

}
function modificarRegistroLocal(registroEditado) {

    const registros = cargarRegistrosLocal();

    const registro = registros.find(
        r => r.id === registroEditado.id
    );

    if (registro === undefined) {
        return;
    }

    registro.fecha = registroEditado.fecha;
    registro.numAula = registroEditado.numAula;
    registro.asignatura = registroEditado.asignatura;
    registro.entrada = registroEditado.entrada;
    registro.salida = registroEditado.salida;
    registro.grupo = registroEditado.grupo;
    registro.turno = registroEditado.turno;

    actualizarRegistrosLocal(registros);
}
function eliminarRegistroLocal(id) {

    const registros = cargarRegistrosLocal();

    const registrosActualizados = registros.filter(
        r => r.id !== Number(id)
    );

    actualizarRegistrosLocal(registrosActualizados);
}
function actualizarTabla() {

    while (tabla.rows.length > 1) {
        tabla.deleteRow(1);
    }

    const registros = cargarRegistrosLocal();

    for (const registro of registros) {
        agregarRegistro(registro);
    }
}
function generarIdRegistro() {
    let ultimoId = Number(localStorage.getItem("ultimoIdRegistro")) || 0;

    ultimoId++;

    localStorage.setItem("ultimoIdRegistro", ultimoId);

    return ultimoId;
}
function obtenerDatosFormulario() {

    let id;

    if (filaEditando === null) {
        id = generarIdRegistro();
    } else {
        id = idEditando;
    }

    return {
        id: id,
        tipoAula: entradaTipoAula.value,
        fecha: entradaFecha.value,
        numAula: entradaNumAula.value,
        asignatura: entradaAsignatura.value,
        entrada: entradaHoraEntrada.value,
        salida: entradaHoraSalida.value,
        grupo: entradaGrupo.value,
        turno: entradaTurno.value
    };

}



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

    filaEditando = null;
    formularioTicket.reset();
    modalTicket.showModal();

}

function cerrarTicket() {

    filaEditando = null;
    formularioTicket.reset();
    modalTicket.close();

}

modalTicket.addEventListener("cancel", function () {

    filaEditando = null;
    formularioTicket.reset();

});

function agregarRegistro(registro) {

    let fila = document.createElement("tr");

    let tdId = document.createElement("td");
    tdId.innerText = registro.id;

    let tdFecha = document.createElement("td");
    tdFecha.innerText = registro.fecha;

    let tdTipoAula = document.createElement("td");
    tdTipoAula.innerText = registro.tipoAula;

    let tdNumAula = document.createElement("td");
    tdNumAula.innerText = registro.numAula;

    let tdEntrada = document.createElement("td");
    tdEntrada.innerText = registro.entrada;

    let tdSalida = document.createElement("td");
    tdSalida.innerText = registro.salida;

    let tdTurno = document.createElement("td");
    tdTurno.innerText = registro.turno;

    let tdGrupo = document.createElement("td");
    tdGrupo.innerText = registro.grupo;

    let tdAcciones = document.createElement("td");

    let btnModificar = document.createElement("button");
    btnModificar.innerText = "Modificar";
    btnModificar.onclick = function () {
        modificar(this);
    };

    let btnEliminar = document.createElement("button");
    btnEliminar.innerText = "Eliminar";
    btnEliminar.onclick = function () {
        borrar(this);
    };

    tdAcciones.appendChild(btnModificar);
    tdAcciones.appendChild(btnEliminar);

    fila.appendChild(tdId);
    fila.appendChild(tdFecha);
    fila.appendChild(tdTipoAula);
    fila.appendChild(tdNumAula);
    fila.appendChild(tdEntrada);
    fila.appendChild(tdSalida);
    fila.appendChild(tdTurno);
    fila.appendChild(tdGrupo);
    fila.appendChild(tdAcciones);   

    tabla.appendChild(fila);

}

    function borrar(boton) {

    const fila = boton.parentNode.parentNode;

    const id = Number(fila.children[0].innerText);

    eliminarRegistroLocal(id);

    actualizarTabla();
}

    function modificar(boton) {

    filaEditando = boton.parentNode.parentNode;

    const celdas = filaEditando.getElementsByTagName("td");

    idEditando = Number(celdas[0].innerText);

    entradaFecha.value = celdas[1].innerText;
    entradaTipoAula.value = celdas[2].innerText;
    entradaNumAula.value = celdas[3].innerText;
    entradaHoraEntrada.value = celdas[4].innerText;
    entradaHoraSalida.value = celdas[5].innerText;
    entradaTurno.value = celdas[6].innerText;
    entradaGrupo.value = celdas[7].innerText;

    modal.showModal();
}

formulario.addEventListener("submit", function (event) {

    event.preventDefault();

    const registro = obtenerDatosFormulario();

    if (filaEditando === null) {

        guardarRegistroLocal(registro);

    } else {

        modificarRegistroLocal(registro);

        filaEditando = null;
    }

    actualizarTabla();

    modal.close();
});

actualizarTabla();


btnAgregarRegistro.addEventListener("click", abrirAltaRegistro);
btnCerrar.addEventListener("click", cerrarAltaRegistro);

AgregarTickets.addEventListener("click", abrirTicket);
btnCerrarTicket.addEventListener("click", cerrarTicket);





const modal = document.getElementById("VizualizacionDiagnostico");
const formulario = document.getElementById("formularioDiagnostico");
const btnCerrarDiagnostico = document.getElementById("CerrarDiagnostico");
const tabla = document.getElementById("estadoTickets");

const entradaEstado = document.getElementById("IDestadoDiagnostico");
const entradaTicket = document.getElementById("IDTicketAsociado");
const entradaFecha = document.getElementById("IDfechaDiagnostico");
const entradaDescripcion = document.getElementById("IDdescripcionDiagnostico");

const filtroEstado = document.getElementById("idDiagnosticoEstado");
const filtroTiempo = document.getElementById("IDtiempoDiagnostico");

let filaEditando = null;

/*
function cargarDiagnosticosLocal() {

    const diagnosticosGuardados = localStorage.getItem("diagnosticos");

    if (diagnosticosGuardados === null) {
        return [];
    }

    return JSON.parse(diagnosticosGuardados);

}

function actualizarDiagnosticosLocal(diagnosticos) {

    localStorage.setItem(
        "diagnosticos",
        JSON.stringify(diagnosticos)
    );

}

function modificarDiagnosticoLocal(diagnosticoEditado) {

    const diagnosticos = cargarDiagnosticosLocal();

    const diagnostico = diagnosticos.find(
        d => d.id === diagnosticoEditado.id
    );

    if (diagnostico === undefined) {
        return;
    }

    diagnostico.estado = diagnosticoEditado.estado;
    diagnostico.ticket = diagnosticoEditado.ticket;
    diagnostico.fecha = diagnosticoEditado.fecha;
    diagnostico.descripcion = diagnosticoEditado.descripcion;

    actualizarDiagnosticosLocal(diagnosticos);
}

function eliminarDiagnosticoLocal(id) {

    const diagnosticos = cargarDiagnosticosLocal();

    const diagnosticosActualizados = diagnosticos.filter(
        d => d.id !== id
    );

    actualizarDiagnosticosLocal(diagnosticosActualizados);
}

function obtenerDiagnosticosFiltrados() {

    let diagnosticos = cargarDiagnosticosLocal();

    const estadoSeleccionado = filtroEstado.value;

    if (estadoSeleccionado !== "Todos") {
        diagnosticos = diagnosticos.filter(
            d => d.estado === estadoSeleccionado
        );
    }

    const tiempoSeleccionado = filtroTiempo.value;

    diagnosticos.sort(function (a, b) {

        const fechaA = new Date(a.fecha);
        const fechaB = new Date(b.fecha);

        if (tiempoSeleccionado === "Mas antiguos") {
            return fechaA - fechaB;
        }

        return fechaB - fechaA;

    });

    return diagnosticos;

}
*/

// function obtenerDatosFormulario() {

//     return {
//         estado: entradaEstado.value,
//         ticket: entradaTicket.value,
//         fecha: entradaFecha.value,
//         descripcion: entradaDescripcion.value
//     };

// }

function cerrarVisualizacionDiagnostico() {

    filaEditando = null;
    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    filaEditando = null;
    formulario.reset();

});

/*
function agregarDiagnostico(diagnostico) {

    let fila = document.createElement("tr");

    let tdDescripcion = document.createElement("td");
    tdDescripcion.innerText = diagnostico.descripcion;

    let tdEstado = document.createElement("td");
    tdEstado.innerText = diagnostico.estado;

    let tdTicket = document.createElement("td");
    tdTicket.innerText = diagnostico.ticket;

    let tdFecha = document.createElement("td");
    tdFecha.innerText = diagnostico.fecha;

    let tdAcciones = document.createElement("td");

    let btnModificar = document.createElement("button");
    btnModificar.innerText = "Modificar";
    btnModificar.onclick = function () {
        modificar(diagnostico);
    };

    let btnEliminar = document.createElement("button");
    btnEliminar.innerText = "Eliminar";
    btnEliminar.onclick = function () {
        borrar(diagnostico);
    };

    tdAcciones.appendChild(btnModificar);
    tdAcciones.appendChild(btnEliminar);

    fila.appendChild(tdDescripcion);
    fila.appendChild(tdEstado);
    fila.appendChild(tdTicket);
    fila.appendChild(tdFecha);
    fila.appendChild(tdAcciones);

    tabla.querySelector("tbody").appendChild(fila);

}

function actualizarTabla() {

    tabla.querySelector("tbody").replaceChildren();

    const diagnosticos = obtenerDiagnosticosFiltrados();

    for (const diagnostico of diagnosticos) {
        agregarDiagnostico(diagnostico);
    }

}

function borrar(diagnostico) {

    eliminarDiagnosticoLocal(diagnostico.id);

    actualizarTabla();

}
*/

function modificar(diagnostico) {

    filaEditando = diagnostico;

    entradaEstado.value = diagnostico.estado;
    entradaTicket.value = diagnostico.ticket;
    entradaFecha.value = diagnostico.fecha;
    entradaDescripcion.value = diagnostico.descripcion;

    modal.showModal();

}

function validarFormulario(diagnostico) {

    if (!diagnostico.descripcion) {
        alert("Complete la descripción del diagnóstico");
        return false;
    }

    return true;

}

btnCerrarDiagnostico.addEventListener("click", cerrarVisualizacionDiagnostico);

/*
filtroEstado.addEventListener("change", actualizarTabla);

filtroTiempo.addEventListener("change", actualizarTabla);
*/

formulario.addEventListener("submit", function (event) {

    event.preventDefault();

    const diagnostico = obtenerDatosFormulario();

    if (!validarFormulario(diagnostico)) {
        return;
    }

    if (filaEditando !== null) {

        diagnostico.id = filaEditando.id;

        /* modificarDiagnosticoLocal(diagnostico); */

        filaEditando = null;
    }

    /* actualizarTabla(); */

    modal.close();
});

/* actualizarTabla(); */
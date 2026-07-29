
const modalDiagnostico = document.getElementById("RegistroDiagnostico");
const formularioDiagnostico = document.getElementById("FormularioDiagnostico");

const entradaEstadoDiagnostico = document.getElementById("estadoDiagnostico");
const entradaTicketAsociado = document.getElementById("TicketAsociado");
const entradaFechaDiagnostico = document.getElementById("fechaDiagnostico");
const entradaDescripcionDiagnostico = document.getElementById("descripcionDiagnostico");

const btnDiagnostico = document.getElementById("btnDiagnostico");
const btnCerrarDiagnostico = document.getElementById("btnCerrarDiagnostico")

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

function guardarDiagnosticoLocal(diagnostico) {

    const diagnosticos = cargarDiagnosticosLocal();
    diagnosticos.push(diagnostico);
    actualizarDiagnosticosLocal(diagnosticos);
}

function generarIdDiagnostico() {

    let ultimoId = Number(localStorage.getItem("ultimoIdDiagnostico")) || 0;
    ultimoId++;
    localStorage.setItem("ultimoIdDiagnostico", ultimoId); 
    return ultimoId;

}

function obtenerDatosFormularioDiagnostico() {

    return {
        id: generarIdDiagnostico(),
        estado: entradaEstadoDiagnostico.value,
        ticketAsociado: entradaTicketAsociado.value,
        fecha: entradaFechaDiagnostico.value,
        descripcion: entradaDescripcionDiagnostico.value
    };

}


function abrirDiagnostico() {

    formularioDiagnostico.reset();
    const ticket = window.ticketEnVisualizacion;
    if (ticket !== undefined && ticket !== null) {
        entradaTicketAsociado.value = ticket.id;
        entradaTicketAsociado.readOnly = true;
    }
    modalDiagnostico.showModal();
}

function cerrarDiagnostico() {
    formulario.reset();
    modal.close();
}

function cerrarDiagnostico() {

    formularioDiagnostico.reset();
    modalDiagnostico.close();
 
}
 
modalDiagnostico.addEventListener("cancel", function () {
 
    formularioDiagnostico.reset();
 
});
 
 
/**
 * EVENTOS
 */
 
btnDiagnostico.addEventListener("click", abrirDiagnostico);
btnCerrarDiagnostico.addEventListener("click", cerrarDiagnostico);
 
formularioDiagnostico.addEventListener("submit", function (event) {
 
    event.preventDefault();
 
    const diagnostico = obtenerDatosFormularioDiagnostico();
 
    guardarDiagnosticoLocal(diagnostico);
 
    modalDiagnostico.close();
});

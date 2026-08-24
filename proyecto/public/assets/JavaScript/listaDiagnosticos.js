const modal = document.getElementById("VizualizacionDiagnostico");
const formulario = document.getElementById("formularioDiagnostico");
const btnCerrarDiagnostico = document.getElementById("CerrarDiagnostico");
const tabla = document.getElementById("estadoTickets");

const entradaIdDiagnostico = document.getElementById("IDDiagnostico");
const entradaEstado = document.getElementById("IDestadoDiagnostico");
const entradaTicket = document.getElementById("IDTicketAsociado");
const entradaFecha = document.getElementById("IDfechaDiagnostico");
const entradaDescripcion = document.getElementById("IDdescripcionDiagnostico");

function abrirVisualizacionDiagnostico(diagnostico) {

    entradaIdDiagnostico.value = diagnostico.ID_D;
    entradaEstado.value = diagnostico.estadoD;
    entradaTicket.value = diagnostico.ID_T;
    entradaFecha.value = diagnostico.fechaD;
    entradaDescripcion.value = diagnostico.descripcionD;

    modal.showModal();

}

function cerrarVisualizacionDiagnostico() {

    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    formulario.reset();

});

function abrirDiagnostico(eventoAbrir) {
    const btnAbrir = eventoAbrir.target.closest(".btnAbrirDiagnostico");
    if (btnAbrir === null) return;

    abrirVisualizacionDiagnostico(JSON.parse(btnAbrir.dataset.diagnostico));
}

tabla.addEventListener("click", abrirDiagnostico);
btnCerrarDiagnostico.addEventListener("click", cerrarVisualizacionDiagnostico);

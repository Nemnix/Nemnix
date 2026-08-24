
const modalDiagnostico = document.getElementById("RegistroDiagnostico");
const formularioDiagnostico = document.getElementById("FormularioDiagnostico");

const entradaEstadoDiagnostico = document.getElementById("estadoDiagnostico");
const entradaTicketAsociado = document.getElementById("TicketAsociado");
const entradaFechaDiagnostico = document.getElementById("fechaDiagnostico");
const entradaDescripcionDiagnostico = document.getElementById("descripcionDiagnostico");

const btnDiagnostico = document.getElementById("btnDiagnostico");
const btnCerrarDiagnostico = document.getElementById("btnCerrarDiagnostico")


function abrirDiagnostico() {

    formularioDiagnostico.reset();
    const ticket = window.ticketEnVisualizacion;
    if (ticket !== undefined && ticket !== null) {
        entradaTicketAsociado.value = ticket.ID_T;
        entradaTicketAsociado.readOnly = true;
    }
    modalDiagnostico.showModal();
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

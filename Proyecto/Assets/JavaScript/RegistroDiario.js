






const modal = document.getElementById("RegistroSolicitante");

const formulario = document.getElementById("FormularioRegistroDiario");

const modalTicket = document.getElementById("TicketSolicitante");

const formularioTicket = document.getElementById("FormularioTickets");

let filaEditando = null;


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
    formulario.reset();
    modal.close();

}

modalTicket.addEventListener("cancel", function () {

    filaEditando = null;
    formularioTicket.reset();

});

btnAgregarRegistro.addEventListener("click", abrirAltaRegistro);
btnCerrar.addEventListener("click", cerrarAltaRegistro);

AgregarTickets.addEventListener("click", abrirTicket);

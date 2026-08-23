const modal = document.getElementById("SolicitudesL");

const formulario = document.getElementById("FormularioSolicitudes");

const btnSolicitudes = document.getElementById("btnSolicitudes");

const tabla = document.getElementById("registroSolicitudes");

const entradaTipoLaboratorio = document.getElementById("IDLaboratorio");
const entradaNumLaboratorio = document.getElementById("numLabTaller");
const entradaFecha = document.getElementById("fechaLaboratorio");
const entradaDescripcion = document.getElementById("motivoSolicitudes");
const entradaMail = document.getElementById("mailDocente");
const entradaID = document.getElementById("idSolicitud");

let modoFormulario = ""

function abrirAltaSolicitud() {

    formulario.reset();
    modoFormulario = "alta";
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarAltaSolicitudes.php";
    entradaNumLaboratorio.readOnly = false;
    modal.showModal();

}

function abrirEditarPrestamo(eventoModificar) {
    const btnEditar = eventoModificar.target.closest(".btnEditar");
    if (btnEditar === null) return;

    const fila = btnEditar.closest("tr");

    formulario.reset();
    entradaNumLaboratorio.readOnly = false;
    modoFormulario = "modificar";
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarModificarSolicitud.php";

    entradaID.value = btnEditar.dataset.id;

    entradaTipoLaboratorio.value = fila.cells[0].textContent.trim(); // nombreLaboratorio
    entradaNumLaboratorio.value  = fila.cells[1].textContent.trim(); // numLaboratorio
    entradaFecha.value           = fila.cells[2].textContent.trim(); // fecha
    entradaDescripcion.value     = fila.cells[3].textContent.trim(); // descripcionSolicitud
    entradaMail.value            = fila.cells[4].textContent.trim(); // gmail

    modal.showModal();
}

btnSolicitudes.addEventListener("click", abrirAltaSolicitud);

tabla.addEventListener("click", abrirEditarPrestamo);
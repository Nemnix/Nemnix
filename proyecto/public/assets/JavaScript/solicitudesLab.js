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

    //Si el botón no se presionó, se sale de la función
    if (btnEditar === null) {
        return;
    }

    //Captura la fila en la que se encuentra el botón
    const fila = btnEditar.closest("tr");

    formulario.reset();
    entradaNumLaboratorio.readOnly = false;
    modoFormulario = "modificar";
    formulario.action = "/proyecto/public/usuarioSolicitante/procesarModificarSolicitud.php";

    //Captura el ID de la solicitud desde el atributo data-id del botón
    entradaID.value = btnEditar.dataset.id;

    //Escribe los datos de la solicitud provenientes de la fila en el formulario
    entradaTipoLaboratorio.value = fila.cells[0].textContent.trim();
    entradaNumLaboratorio.value = fila.cells[1].textContent.trim();
    entradaFecha.value = fila.cells[2].textContent.trim();
    entradaDescripcion.value = fila.cells[3].textContent.trim();
    entradaMail.value = fila.cells[4].textContent.trim();

    //Muestra el cuadro de diálogo o "modal"
    modal.showModal();
}

btnSolicitudes.addEventListener("click", abrirAltaSolicitud);

tabla.addEventListener("click", abrirEditarPrestamo);
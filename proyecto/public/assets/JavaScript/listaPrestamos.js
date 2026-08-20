const modal = document.getElementById("RegistroEquipoP");

const formulario = document.getElementById("FormularioInventarioEquipos");

const entradaIdPrestamo = document.getElementById("ID_P");
const entradaIdEquipo = document.getElementById("ID_EP");
const entradaModelo = document.getElementById("modeloPrestamo");
const entradaEstado = document.getElementById("estadoEquipos");
const entradaCedula = document.getElementById("ciAlumno");
const entradaNombre = document.getElementById("nombreAlumnoPrestamo");
const entradaApellido = document.getElementById("apellidoAlumnoPrestamo");
const entradaFechaInicio = document.getElementById("fechaPrestamo");
const entradaFechaFin = document.getElementById("fechaDevolucion");

const btnAgregarEquipo = document.getElementById("btnAgregarEquipo");
const btnCerrarRegistroEquipo = document.getElementById("btnCerrarInventario");

const tabla = document.getElementById("registroInventarioPRestamo");

let modoFormulario = ""
let filaEditando = null;

function abrirRegistroEquipo() {
    filaEditando = null;
    modoFormulario = "alta";
    formulario.action = "/proyecto/public/usuarioTecnico/procesarAltaPrestamos.php";
    entradaIdPrestamo.value = "";
    entradaIdEquipo.readOnly = false;
    entradaCedula.readOnly = false;
    formulario.reset();
    modal.showModal();

}

function cerrarRegistroEquipo() {
    filaEditando = null;
    formulario.reset();
    modal.close();
}

modal.addEventListener("cancel", function () {
    filaEditando = null;
    formulario.reset();
});
function abrirEditarPrestamo(eventoModificar) {
    const btnEditar = eventoModificar.target.closest(".btnEditar");

    //Si el botón no se presionó, se sale de la función
    if (btnEditar === null) {
        return;
    }

    //Captura la fila en la que se encuentra el botón
    const fila = btnEditar.closest("tr");

    entradaIdEquipo.readOnly = true;
    entradaCedula.readOnly = false;
    formulario.reset();
    modoFormulario = "modificar";
    formulario.action = "/proyecto/public/usuarioTecnico/procesarModificarPrestamos.php";

    //Escribe los datos del empleado provenientes de la fila en el formulario
    entradaIdPrestamo.value = btnEditar.dataset.id;
    entradaIdEquipo.value = fila.cells[0].textContent.trim();
    entradaCedula.value = fila.dataset.ciAlumno;
    entradaModelo.value = fila.cells[1].textContent.trim();
    entradaEstado.value = fila.cells[2].textContent.trim();
    entradaNombre.value = fila.cells[3].textContent.trim();
    entradaApellido.value = fila.cells[4].textContent.trim();
    entradaFechaInicio.value = fila.cells[5].textContent.trim();
    entradaFechaFin.value = fila.cells[6].textContent.trim();

    //Muestra el cuadro de diálogo o "modal"
    modal.showModal();
}

// function borrar(boton) {
//     const fila = boton.parentNode.parentNode;
//     const idEquipo = fila.children[0].innerText;

//     eliminarPrestamoLocal(idEquipo);
//     actualizarTabla();
// }

 
 

// function modificar(boton) {
 
//     filaEditando = boton.parentNode.parentNode;
 
//     const celdas = filaEditando.getElementsByTagName("td");
 
//     entradaIdEquipo.value = celdas[0].innerText;
//     entradaIdEquipo.readOnly = true;
//     entradaModelo.value = celdas[1].innerText;
//     entradaEstado.value = celdas[2].innerText;
//     entradaNombre.value = celdas[3].innerText;
//     entradaApellido.value = celdas[4].innerText;
//     entradaFechaInicio.value = celdas[5].innerText;
//     entradaFechaFin.value = celdas[6].innerText;
 
//     modal.showModal();
 
// }


// function validarFormulario(prestamo) {
//     if (!prestamo.idEquipo || !prestamo.modelo || !prestamo.estado) {
//         alert("Complete los campos obligatorios (ID, Modelo del equipo y estado del equipo)");
//         return false;
//     }

//     if (prestamo.cedula && !/^\d+$/.test(prestamo.cedula)) {
//         alert("La cédula debe contener solamente números");
//         return false;
//     }

//     if (prestamo.cedula &&!/^\d{8}$/.test(prestamo.cedula)) {
//         alert("La cédula debe tener 8 números");
//         return false;
//     }

//     return true;
// }

tabla.addEventListener("click", abrirEditarPrestamo);

btnAgregarEquipo.addEventListener("click", abrirRegistroEquipo);
btnCerrarRegistroEquipo.addEventListener("click", cerrarRegistroEquipo);



// Se deja que el formulario se envíe normalmente por POST a procesarAltaPrestamos.php.
// Ya NO se llama a event.preventDefault(), por lo que el navegador
// realiza el submit real y el PHP procesa e inserta los datos en la base.
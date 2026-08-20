const modal = document.getElementById("RegistroEquipoP");

const formulario = document.getElementById("FormularioInventarioEquipos");

const btnAgregarEquipo = document.getElementById("btnAgregarEquipo");
const btnCerrarRegistroEquipo = document.getElementById("btnCerrarInventario");

const tabla = document.getElementById("registroInventarioPRestamo");


let filaEditando = null;

function abrirRegistroEquipo() {
    filaEditando = null;
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

btnAgregarEquipo.addEventListener("click", abrirRegistroEquipo);
btnCerrarRegistroEquipo.addEventListener("click", cerrarRegistroEquipo);

// Se deja que el formulario se envíe normalmente por POST a procesarAltaPrestamos.php.
// Ya NO se llama a event.preventDefault(), por lo que el navegador
// realiza el submit real y el PHP procesa e inserta los datos en la base.
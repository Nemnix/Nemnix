//Dirección de la API de préstamos
const API_PRESTAMOS = "../index.php?ruta=prestamos";

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


/**
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API con el token CSRF y devuelve los datos de la respuesta
async function llamarApi(metodo, datos = null) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

    if (metodo !== "GET") {
        opciones.headers["X-CSRF-Token"] = sessionStorage.getItem("csrfToken") ?? "";
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(API_PRESTAMOS, opciones);
    const json = await respuesta.json();

    //Si la sesión no está iniciada o venció, vuelve al login
    if (respuesta.status === 401) {
        window.location.replace("./index.html");
    }
    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "Error en la solicitud.");
    }

    return json.datos;
}


/**
 * TABLA DE PRÉSTAMOS
 */

//Crea la fila de un préstamo. Usa textContent para que los datos no se interpreten como HTML
function crearFila(prestamo) {
    const fila = document.createElement("tr");
    //La cédula del alumno no se muestra en la tabla, se guarda en la fila para precargarla al editar
    fila.dataset.ciAlumno = prestamo.ciAlumno;

    for (const valor of [prestamo.ID_EP, prestamo.modelo, prestamo.estadoPrestamo, prestamo.nombreAlumno, prestamo.apellidoAlumno, prestamo.fechaInicio, prestamo.fechaLimite]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    const btnEditar = document.createElement("button");
    btnEditar.type = "button";
    btnEditar.className = "btnEditar";
    btnEditar.textContent = "Editar";
    btnEditar.dataset.id = prestamo.ID_P;

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = prestamo.ID_P;

    celdaAcciones.append(btnEditar, btnEliminar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide los préstamos a la API y dibuja la tabla
async function cargarPrestamos() {
    try {
        const prestamos = await llamarApi("GET");
        tabla.replaceChildren(...prestamos.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

function abrirRegistroEquipo() {
    filaEditando = null;
    modoFormulario = "alta";
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

    //Al modificar no se cambian el equipo ni el alumno del préstamo
    entradaIdEquipo.readOnly = true;
    entradaCedula.readOnly = true;
    formulario.reset();
    modoFormulario = "modificar";

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

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja el préstamo
async function eliminarPrestamo(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar el préstamo?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_P: btnEliminar.dataset.id });
        window.alert(respuesta.mensaje);
        cargarPrestamos();
    } catch (error) {
        window.alert(error.message);
    }
}

//Envía el formulario a la API: POST para el alta, PUT para la modificación
async function gestionarPrestamo(evento) {
    evento.preventDefault();

    const datos = {
        ID_P: entradaIdPrestamo.value,
        ID_EP: entradaIdEquipo.value.trim(),
        modelo: entradaModelo.value.trim(),
        estado: entradaEstado.value,
        ciAlumno: entradaCedula.value.trim(),
        nombreAlumno: entradaNombre.value.trim(),
        apellidoAlumno: entradaApellido.value.trim(),
        fechaInicio: entradaFechaInicio.value,
        fechaLimite: entradaFechaFin.value
    };

    try {
        const metodo = modoFormulario === "alta" ? "POST" : "PUT";
        const respuesta = await llamarApi(metodo, datos);
        window.alert(respuesta.mensaje);
        cerrarRegistroEquipo();
        cargarPrestamos();
    } catch (error) {
        window.alert(error.message);
    }
}

tabla.addEventListener("click", abrirEditarPrestamo);

btnAgregarEquipo.addEventListener("click", abrirRegistroEquipo);
btnCerrarRegistroEquipo.addEventListener("click", cerrarRegistroEquipo);

//Baja de préstamos desde el botón eliminar de cada fila
tabla.addEventListener("click", eliminarPrestamo);

formulario.addEventListener("submit", gestionarPrestamo);

//Carga inicial de la tabla
cargarPrestamos();



const filtroEstado = document.getElementById("idPrestamoEstado");

//Dibuja la tabla solo con los préstamos que tienen el estado elegido
async function aplicarFiltros() {
    const estado = filtroEstado.value;

    try {
        const prestamos = await llamarApi("GET");
        const filtrados = prestamos.filter(prestamo => estado === "Todos" || prestamo.estadoPrestamo === estado);
        tabla.replaceChildren(...filtrados.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

filtroEstado.addEventListener("change", aplicarFiltros);
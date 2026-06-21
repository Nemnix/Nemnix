const modal = document.getElementById("RegistroEquipoP");

const formulario = document.getElementById("FormularioInventarioEquipos");

const btnAgregarEquipo = document.getElementById("btnAgregarEquipo");
const btnCerrarRegistroEquipo = document.getElementById("btnCerrarInventario");

const tabla = document.getElementById("registroInventarioPRestamo");

const entradaIdEquipo = document.getElementById("IDequipo");
const entradaModelo = document.getElementById("modeloPrestamo");
const entradaEstado = document.getElementById("estadoEquipos");
const entradaNombre = document.getElementById("nombreAlumnoPrestamo");
const entradaApellido = document.getElementById("apellidoAlumnoPrestamo");
const entradaCedula = document.getElementById("CI");
const entradaFechaInicio = document.getElementById("fechaPrestamo");
const entradaFechaFin = document.getElementById("fechaDevolucion");

let filaEditando = null;

function cargarPrestamosLocal() {
    const prestamosGuardados = localStorage.getItem("prestamos");

    if (prestamosGuardados === null) {
        return [];
    }

    return JSON.parse(prestamosGuardados);
}

function actualizarPrestamosLocal(prestamos) {
    localStorage.setItem("prestamos", JSON.stringify(prestamos));
}

function guardarPrestamoLocal(prestamo) {
    const prestamos = cargarPrestamosLocal();
    prestamos.push(prestamo);
    actualizarPrestamosLocal(prestamos);
}

function actualizarTabla() {
    tabla.replaceChildren();

    const prestamos = cargarPrestamosLocal();

    for (const prestamo of prestamos) {
        agregarEquipo(prestamo);
    }
}

function modificarPrestamoLocal(prestamoEditado) {
    const prestamos = cargarPrestamosLocal();

    const prestamo = prestamos.find(
        p => p.idEquipo === prestamoEditado.idEquipo
    );

    if (prestamo === undefined) {
        return;
    }

    prestamo.modelo = prestamoEditado.modelo;
    prestamo.estado = prestamoEditado.estado;
    prestamo.nombre = prestamoEditado.nombre;
    prestamo.apellido = prestamoEditado.apellido;
    prestamo.cedula = prestamoEditado.cedula;
    prestamo.fechaInicio = prestamoEditado.fechaInicio;
    prestamo.fechaFin = prestamoEditado.fechaFin;

    actualizarPrestamosLocal(prestamos);
}

function eliminarPrestamoLocal(idEquipo) {
    const prestamos = cargarPrestamosLocal();

    const prestamosActualizados = prestamos.filter(
        p => p.idEquipo !== idEquipo
    );

    actualizarPrestamosLocal(prestamosActualizados);
}

function obtenerSiguienteIdEquipo() {
    const prestamos = cargarPrestamosLocal();
    if (prestamos.length === 0) {
        return 1;
    }
const ids = prestamos.map(p => parseInt(p.idEquipo, 10) || 0);
    const idMaximo = Math.max(...ids);
    return idMaximo + 1;
}


function obtenerDatosFormulario() {
    return {
        idEquipo: entradaIdEquipo.value,
        modelo: entradaModelo.value,
        estado: entradaEstado.value,
        nombre: entradaNombre.value,
        apellido: entradaApellido.value,
        cedula: entradaCedula.value,
        fechaInicio: entradaFechaInicio.value,
        fechaFin: entradaFechaFin.value
    };
}

function abrirRegistroEquipo() {
    filaEditando = null;
    formulario.reset();
    entradaIdEquipo.value = obtenerSiguienteIdEquipo();
    entradaIdEquipo.readOnly = true;
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

function agregarEquipo(prestamo) {
    let fila = document.createElement("tr");

    let tdIdEquipo = document.createElement("td");
    tdIdEquipo.innerText = prestamo.idEquipo;

    let tdModelo = document.createElement("td");
    tdModelo.innerText = prestamo.modelo;

    let tdEstado = document.createElement("td");
    tdEstado.innerText = prestamo.estado;

    let tdNombre = document.createElement("td");
    tdNombre.innerText = prestamo.nombre;

    let tdApellido = document.createElement("td");
    tdApellido.innerText = prestamo.apellido;

    let tdFechaInicio = document.createElement("td");
    tdFechaInicio.innerText = prestamo.fechaInicio;

    let tdFechaFin = document.createElement("td");
    tdFechaFin.innerText = prestamo.fechaFin;

    let tdAcciones = document.createElement("td");

    let btnModificar = document.createElement("button");
    btnModificar.innerText = "Modificar";
    btnModificar.onclick = function () {
        modificar(this);
    };

    let btnEliminar = document.createElement("button");
    btnEliminar.innerText = "Eliminar";
    btnEliminar.onclick = function () {
        borrar(this);
    };

    tdAcciones.appendChild(btnModificar);
    tdAcciones.appendChild(btnEliminar);

    fila.appendChild(tdIdEquipo);
    fila.appendChild(tdModelo);
    fila.appendChild(tdEstado);
    fila.appendChild(tdNombre);
    fila.appendChild(tdApellido);
    fila.appendChild(tdFechaInicio);
    fila.appendChild(tdFechaFin);
    fila.appendChild(tdAcciones);

    tabla.appendChild(fila);
}

function borrar(boton) {
    const fila = boton.parentNode.parentNode;
    const idEquipo = fila.children[0].innerText;

    eliminarPrestamoLocal(idEquipo);
    actualizarTabla();
}

function modificar(boton) {
 
    filaEditando = boton.parentNode.parentNode;
 
    const celdas = filaEditando.getElementsByTagName("td");
 
    entradaIdEquipo.value = celdas[0].innerText;
    entradaIdEquipo.readOnly = true;
    entradaModelo.value = celdas[1].innerText;
    entradaEstado.value = celdas[2].innerText;
    entradaNombre.value = celdas[3].innerText;
    entradaApellido.value = celdas[4].innerText;
    entradaFechaInicio.value = celdas[5].innerText;
    entradaFechaFin.value = celdas[6].innerText;
 
    modal.showModal();
 
}


function validarFormulario(prestamo) {
    if (!prestamo.idEquipo || !prestamo.modelo || !prestamo.estado) {
        alert("Complete los campos obligatorios (ID, Modelo del equipo y estado del equipo)");
        return false;
    }

    if (prestamo.cedula && !/^\d+$/.test(prestamo.cedula)) {
        alert("La cédula debe contener solamente números");
        return false;
    }

    if (prestamo.cedula &&!/^\d{8}$/.test(prestamo.cedula)) {
        alert("La cédula debe tener 8 números");
        return false;
    }

    return true;
}

btnAgregarEquipo.addEventListener("click", abrirRegistroEquipo);
btnCerrarRegistroEquipo.addEventListener("click", cerrarRegistroEquipo);

formulario.addEventListener("submit", function (event) {
    event.preventDefault();

    const prestamo = obtenerDatosFormulario();

    if (!validarFormulario(prestamo)) {
        return;
    }

    if (filaEditando === null) {
        guardarPrestamoLocal(prestamo);
    } else {
        modificarPrestamoLocal(prestamo);
        filaEditando = null;
    }

    actualizarTabla();
    modal.close();
});

actualizarTabla();
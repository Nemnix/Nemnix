//Dirección de la API de solicitudes
const API_SOLICITUDES = "../index.php?ruta=solicitudes";

const modal = document.getElementById("SolicitudesL");

const formulario = document.getElementById("FormularioSolicitudes");

const btnSolicitudes = document.getElementById("btnSolicitudes");
const btnCerrarSolicitud = document.getElementById("btnCerrarSolicitud");

const tabla = document.getElementById("registroSolicitudes");
//Constante para trabajar con el cuerpo de la tabla de solicitudes
const cuerpoTablaSolicitudes = document.getElementById("tablaOpciones");

const entradaTipoLaboratorio = document.getElementById("IDLaboratorio");
const entradaNumLaboratorio = document.getElementById("numLabTaller");
const entradaFecha = document.getElementById("fechaLaboratorio");
const entradaHora = document.getElementById("horaLaboratorio");
const entradaDescripcion = document.getElementById("motivoSolicitudes");
const entradaMail = document.getElementById("mailDocente");
const entradaID = document.getElementById("idSolicitud");
const entradaFechaActual = document.getElementById("fechaActual");
const entradaHoraActual = document.getElementById("horaActual");

let modoFormulario = ""


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

    const respuesta = await fetch(API_SOLICITUDES, opciones);
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
 * TABLA DE SOLICITUDES
 */

//Crea la fila de una solicitud. Usa textContent para que los datos no se interpreten como HTML
function crearFila(solicitud) {
    const fila = document.createElement("tr");

    //La hora llega como HH:MM:SS, se muestra como HH:MM
    const hora = (solicitud.hora ?? "").slice(0, 5);

    for (const valor of [solicitud.nombreLaboratorio, solicitud.numLaboratorio, solicitud.fecha, hora, solicitud.descripcionSolicitud, solicitud.gmail]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    const btnEditar = document.createElement("button");
    btnEditar.type = "button";
    btnEditar.className = "btnEditar";
    btnEditar.textContent = "Editar";
    btnEditar.dataset.id = solicitud.ID_L;
    btnEditar.dataset.fecha = solicitud.fecha;
    btnEditar.dataset.hora = hora;

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = solicitud.ID_L;
    btnEliminar.dataset.fecha = solicitud.fecha;
    btnEliminar.dataset.hora = hora;

    celdaAcciones.append(btnEditar, btnEliminar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide las solicitudes a la API y dibuja la tabla
async function cargarSolicitudes() {
    try {
        const solicitudes = await llamarApi("GET");
        cuerpoTablaSolicitudes.replaceChildren(...solicitudes.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}


/**
 * GESTION DEL ESTADO DEL FORMULARIO/MODAL
 */

function abrirAltaSolicitud() {

    formulario.reset();
    modoFormulario = "alta";
    entradaNumLaboratorio.readOnly = false;
    modal.showModal();

}

function cerrarSolicitud() {

    formulario.reset();
    modoFormulario = "";
    modal.close();

}

function abrirEditarSolicitud(eventoModificar) {
    const btnEditar = eventoModificar.target.closest(".btnEditar");
    if (btnEditar === null) return;

    const fila = btnEditar.closest("tr");

    formulario.reset();
    entradaNumLaboratorio.readOnly = false;
    modoFormulario = "modificar";

    entradaID.value = btnEditar.dataset.id;
    entradaFechaActual.value = btnEditar.dataset.fecha;
    entradaHoraActual.value = btnEditar.dataset.hora;

    entradaTipoLaboratorio.value = fila.cells[0].textContent.trim(); // nombreLaboratorio
    entradaNumLaboratorio.value  = fila.cells[1].textContent.trim(); // numLaboratorio
    entradaFecha.value           = fila.cells[2].textContent.trim(); // fecha
    entradaHora.value            = fila.cells[3].textContent.trim(); // hora
    entradaDescripcion.value     = fila.cells[4].textContent.trim(); // descripcionSolicitud
    entradaMail.value            = fila.cells[5].textContent.trim(); // gmail

    modal.showModal();
}

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja la solicitud
async function eliminarSolicitud(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar la solicitud?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_L: btnEliminar.dataset.id, fecha: btnEliminar.dataset.fecha, hora: btnEliminar.dataset.hora });
        window.alert(respuesta.mensaje);
        cargarSolicitudes();
    } catch (error) {
        window.alert(error.message);
    }
}

//Envía el formulario a la API: POST para el alta, PUT para la modificación
async function gestionarSolicitud(evento) {
    evento.preventDefault();

    const datos = {
        ID_L: entradaID.value,
        fechaActual: entradaFechaActual.value,
        horaActual: entradaHoraActual.value,
        nombreLaboratorio: entradaTipoLaboratorio.value,
        numLaboratorio: entradaNumLaboratorio.value.trim(),
        fecha: entradaFecha.value,
        hora: entradaHora.value,
        descripcionSolicitud: entradaDescripcion.value.trim(),
        gmail: entradaMail.value.trim()
    };

    try {
        const metodo = modoFormulario === "alta" ? "POST" : "PUT";
        const respuesta = await llamarApi(metodo, datos);
        window.alert(respuesta.mensaje);
        cerrarSolicitud();
        cargarSolicitudes();
    } catch (error) {
        window.alert(error.message);
    }
}

btnSolicitudes.addEventListener("click", abrirAltaSolicitud);
btnCerrarSolicitud.addEventListener("click", cerrarSolicitud);

tabla.addEventListener("click", abrirEditarSolicitud);

//Baja de solicitudes desde el botón eliminar de cada fila
tabla.addEventListener("click", eliminarSolicitud);

formulario.addEventListener("submit", gestionarSolicitud);

//Para que al apretar escape también se limpie el formulario
modal.addEventListener("cancel", cerrarSolicitud);

//Carga inicial de la tabla
cargarSolicitudes();

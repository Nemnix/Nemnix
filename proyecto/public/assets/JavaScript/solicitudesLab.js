
const modal = document.getElementById("SolicitudesL");


const formulario = document.getElementById("FormularioSolicitudes");


const btnSolicitudes = document.getElementById("btnSolicitudes");


const tabla = document.getElementById("registroSolicitudes");


const entradaTipoLaboratorio = document.getElementById("IDLaboratorio");
const entradaNumLaboratorio = document.getElementById("numLabTaller");
const entradaFecha = document.getElementById("fechaLaboratorio");
const entradaDescripcion = document.getElementById("motivoSolicitudes");
const entradaMail = document.getElementById("mailDocente");


// function cargarSolicitudesLocal() {

//     const solicitudesGuardadas = localStorage.getItem("solicitudes");

//     if (solicitudesGuardadas === null) {
//         return [];
//     }

//     return JSON.parse(solicitudesGuardadas);

// }

// function actualizarSolicitudesLocal(solicitudes) {

//     localStorage.setItem(
//         "solicitudes",
//         JSON.stringify(solicitudes)
//     );

// }

// function guardarSolicitudLocal(solicitud) {

//     const solicitudes = cargarSolicitudesLocal();

//     solicitudes.push(solicitud);

//     actualizarSolicitudesLocal(solicitudes);

// }

// function actualizarTabla() {

//     while (tabla.rows.length > 1) {
//         tabla.deleteRow(1);
//     }

//     const solicitudes = cargarSolicitudesLocal();

//     for (const solicitud of solicitudes) {
//         agregarSolicitud(solicitud);
//     }

// }

// function obtenerDatosFormulario() {

//     return {
//         tipoLaboratorio: entradaTipoLaboratorio.value,
//         numLaboratorio: entradaNumLaboratorio.value,
//         fecha: entradaFecha.value,
//         descripcion: entradaDescripcion.value,
//         mail: entradaMail.value
//     };

// }


function abrirAltaSolicitud() {

    formulario.reset();
    modal.showModal();

}

// modal.addEventListener("cancel", function () {

//     formulario.reset();

// });

// function agregarSolicitud(solicitud) {

//     let fila = document.createElement("tr");

//     let tdTipoLaboratorio = document.createElement("td");
//     tdTipoLaboratorio.innerText = solicitud.tipoLaboratorio;

//     let tdNumLaboratorio = document.createElement("td");
//     tdNumLaboratorio.innerText = solicitud.numLaboratorio;

//     let tdFecha = document.createElement("td");
//     tdFecha.innerText = solicitud.fecha;

//     let tdDescripcion = document.createElement("td");
//     tdDescripcion.innerText = solicitud.descripcion;

//     let tdMail = document.createElement("td");
//     tdMail.innerText = solicitud.mail;

//     fila.appendChild(tdTipoLaboratorio);
//     fila.appendChild(tdNumLaboratorio);
//     fila.appendChild(tdFecha);
//     fila.appendChild(tdDescripcion);
//     fila.appendChild(tdMail);

//     tabla.appendChild(fila);

// }

// btnSolicitudes.addEventListener("click", abrirAltaSolicitud);

// formulario.addEventListener("submit", function (event) {

//     event.preventDefault();

//     const solicitud = obtenerDatosFormulario();

//     guardarSolicitudLocal(solicitud);

//     actualizarTabla();

//     modal.close();
// });

// actualizarTabla();
//Dirección de la API de diagnósticos
const API_DIAGNOSTICOS = "../api/diagnosticos.php";

const modalDiagnostico = document.getElementById("RegistroDiagnostico");
const formularioDiagnostico = document.getElementById("FormularioDiagnostico");

const entradaEstadoDiagnostico = document.getElementById("estadoDiagnostico");
const entradaTicketAsociado = document.getElementById("TicketAsociado");
const entradaFechaDiagnostico = document.getElementById("fechaDiagnostico");
const entradaDescripcionDiagnostico = document.getElementById("descripcionDiagnostico");
const entradaRegistroAsociado = document.getElementById("registroAsociado");

const btnDiagnostico = document.getElementById("btnDiagnostico");
const btnCerrarDiagnostico = document.getElementById("btnCerrarDiagnostico")


function abrirDiagnostico() {

    formularioDiagnostico.reset();
    const ticket = window.ticketEnVisualizacion;
    if (ticket !== undefined && ticket !== null) {
        entradaTicketAsociado.value = ticket.ID_T;
        entradaTicketAsociado.readOnly = true;
        entradaRegistroAsociado.value = ticket.ID_R;
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
 * COMUNICACIÓN CON LA API
 */

//Envía la petición a la API de diagnósticos con el token CSRF y devuelve los datos de la respuesta.
//Tiene otro nombre que llamarApi porque esta página también carga ticketsEntrantes.js
async function llamarApiDiagnosticos(metodo, datos = null) {
    const opciones = {
        method: metodo,
        headers: { "Content-Type": "application/json" }
    };

    if (metodo !== "GET") {
        opciones.headers["X-CSRF-Token"] = sessionStorage.getItem("csrfToken") ?? "";
        opciones.body = JSON.stringify(datos);
    }

    const respuesta = await fetch(API_DIAGNOSTICOS, opciones);
    const json = await respuesta.json();

    //Si la sesión no está iniciada o venció, vuelve al login
    if (respuesta.status === 401) {
        window.location.replace("./login.html");
    }
    if (!respuesta.ok) {
        throw new Error(json.mensaje ?? "Error en la solicitud.");
    }

    return json.datos;
}

//Envía el diagnóstico del ticket a la API con POST
async function registrarDiagnostico(evento) {
    evento.preventDefault();

    const datos = {
        ID_R: entradaRegistroAsociado.value,
        ID_T: entradaTicketAsociado.value,
        estado: entradaEstadoDiagnostico.value,
        fecha: entradaFechaDiagnostico.value,
        descripcion: entradaDescripcionDiagnostico.value.trim()
    };

    try {
        const respuesta = await llamarApiDiagnosticos("POST", datos);
        window.alert(respuesta.mensaje);
        cerrarDiagnostico();
    } catch (error) {
        window.alert(error.message);
    }
}


/**
 * EVENTOS
 */

btnDiagnostico.addEventListener("click", abrirDiagnostico);
btnCerrarDiagnostico.addEventListener("click", cerrarDiagnostico);

formularioDiagnostico.addEventListener("submit", registrarDiagnostico);

//Dirección de la API de diagnósticos
const API_DIAGNOSTICOS = "../api/diagnosticos.php";

const modal = document.getElementById("VizualizacionDiagnostico");
const formulario = document.getElementById("formularioDiagnostico");
const btnCerrarDiagnostico = document.getElementById("CerrarDiagnostico");
const tabla = document.getElementById("estadoTickets");
//Constante para trabajar con el cuerpo de la tabla de diagnósticos
const cuerpoTablaDiagnosticos = tabla.tBodies[0];

const filtroEstado = document.getElementById("idDiagnosticoEstado");
const filtroTiempo = document.getElementById("IDtiempoDiagnostico");

//Diagnósticos traídos de la API, para filtrarlos sin volver a pedirlos
let todosLosDiagnosticos = [];

const entradaIdDiagnostico = document.getElementById("IDDiagnostico");
const entradaEstado = document.getElementById("IDestadoDiagnostico");
const entradaTicket = document.getElementById("IDTicketAsociado");
const entradaFecha = document.getElementById("IDfechaDiagnostico");
const entradaDescripcion = document.getElementById("IDdescripcionDiagnostico");


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


/**
 * TABLA DE DIAGNÓSTICOS
 */

//Crea la fila de un diagnóstico. Usa textContent para que los datos no se interpreten como HTML
function crearFila(diagnostico) {
    const fila = document.createElement("tr");

    for (const valor of [diagnostico.ID_T, diagnostico.fechaD, diagnostico.estadoD, diagnostico.descripcionD]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");
    const cajaOperaciones = document.createElement("div");
    cajaOperaciones.className = "cajaOperaciones";

    //El botón Abrir guarda el diagnóstico completo para cargarlo en el modal
    const btnAbrir = document.createElement("button");
    btnAbrir.type = "button";
    btnAbrir.className = "btnOperacion btnAbrirDiagnostico";
    btnAbrir.textContent = "Abrir";
    btnAbrir.dataset.diagnostico = JSON.stringify(diagnostico);

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = diagnostico.ID_D;

    cajaOperaciones.append(btnAbrir, btnEliminar);
    celdaAcciones.appendChild(cajaOperaciones);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide los diagnósticos a la API y dibuja la tabla
async function cargarDiagnosticos() {
    try {
        todosLosDiagnosticos = await llamarApi("GET");
        aplicarFiltros();
    } catch (error) {
        window.alert(error.message);
    }
}

//Dibuja la tabla con los diagnósticos que cumplen los filtros, en el orden elegido
function aplicarFiltros() {
    const estado = filtroEstado.value;

    const filtrados = todosLosDiagnosticos
        .filter(diagnostico => estado === "Todos" || diagnostico.estadoD === estado)
        .sort((a, b) => filtroTiempo.value === "Más recientes"
            ? b.fechaD.localeCompare(a.fechaD)
            : a.fechaD.localeCompare(b.fechaD));

    cuerpoTablaDiagnosticos.replaceChildren(...filtrados.map(crearFila));
}

filtroEstado.addEventListener("change", aplicarFiltros);
filtroTiempo.addEventListener("change", aplicarFiltros);


/**
 * GESTION DEL ESTADO DEL FORMULARIO/MODAL
 */

function abrirVisualizacionDiagnostico(diagnostico) {

    entradaIdDiagnostico.value = diagnostico.ID_D;
    entradaEstado.value = diagnostico.estadoD;
    entradaTicket.value = diagnostico.ID_T;
    entradaFecha.value = diagnostico.fechaD;
    entradaDescripcion.value = diagnostico.descripcionD;

    modal.showModal();

}

function cerrarVisualizacionDiagnostico() {

    formulario.reset();
    modal.close();

}

modal.addEventListener("cancel", function () {

    formulario.reset();

});

function abrirDiagnostico(eventoAbrir) {
    const btnAbrir = eventoAbrir.target.closest(".btnAbrirDiagnostico");
    if (btnAbrir === null) return;

    abrirVisualizacionDiagnostico(JSON.parse(btnAbrir.dataset.diagnostico));
}

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja el diagnóstico
async function eliminarDiagnostico(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar el diagnóstico?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_D: btnEliminar.dataset.id });
        window.alert(respuesta.mensaje);
        cargarDiagnosticos();
    } catch (error) {
        window.alert(error.message);
    }
}

//Envía los cambios del diagnóstico a la API con PUT
async function gestionarDiagnostico(evento) {
    evento.preventDefault();

    const datos = {
        ID_D: entradaIdDiagnostico.value,
        estado: entradaEstado.value,
        fecha: entradaFecha.value,
        descripcion: entradaDescripcion.value.trim()
    };

    try {
        const respuesta = await llamarApi("PUT", datos);
        window.alert(respuesta.mensaje);
        cerrarVisualizacionDiagnostico();
        cargarDiagnosticos();
    } catch (error) {
        window.alert(error.message);
    }
}

tabla.addEventListener("click", abrirDiagnostico);
btnCerrarDiagnostico.addEventListener("click", cerrarVisualizacionDiagnostico);

//Baja de diagnósticos desde el botón eliminar de cada fila
tabla.addEventListener("click", eliminarDiagnostico);

formulario.addEventListener("submit", gestionarDiagnostico);

//Carga inicial de la tabla
cargarDiagnosticos();

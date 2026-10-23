//Dirección de la API de laboratorios
const API_LABORATORIOS = "../index.php?ruta=laboratorios";

const modal = document.getElementById("AgregarLaboratorio");

const formulario = document.getElementById("FormularioAgregarLaboratorio");

const btnAgregarLaboratorio = document.getElementById("btnAgregarLaboratorio");
const btnCerrarRegistroLaboratorio = document.getElementById("btnCerrarRegistroLaboratorio");
const tablaInventario = document.getElementById("tablaInventario");
const btnEditarInventario = document.querySelectorAll(".btnEditar");

const tabla = document.getElementById("Inventario");
const entradaTipoLaboratorio = document.getElementById("registroLaboratorio");
const entradaNumAula = document.getElementById("registroNumLabTaller");
const entradaCantMonitores = document.getElementById("cantMonitores");
const entradaCantTeclados = document.getElementById("cantTeclados");
const entradaCantMouses = document.getElementById("cantMouses");
const entradaCantComputadoras = document.getElementById("cantComputadoras");
const entradaIdLaboratorio = document.getElementById("idLaboratorio");

let modoFormulario = "";
let filaEditando = null;
let idEditando = null;


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

    const respuesta = await fetch(API_LABORATORIOS, opciones);
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
 * TABLA DE INVENTARIO
 */

//Crea la fila de un laboratorio. Usa textContent para que los datos no se interpreten como HTML
function crearFila(laboratorio) {
    const fila = document.createElement("tr");

    for (const valor of [laboratorio.nombreLaboratorio, laboratorio.numLaboratorio, laboratorio.cantMonitores, laboratorio.cantTeclados, laboratorio.cantMouses, laboratorio.cantComputadoras]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");

    const btnEditar = document.createElement("button");
    btnEditar.type = "button";
    btnEditar.className = "btnEditar";
    btnEditar.textContent = "Editar";
    btnEditar.dataset.id = laboratorio.ID_L;

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.id = laboratorio.ID_L;

    celdaAcciones.append(btnEditar, btnEliminar);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide los laboratorios a la API y dibuja la tabla
async function cargarLaboratorios() {
    try {
        const laboratorios = await llamarApi("GET");
        tablaInventario.replaceChildren(...laboratorios.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}

btnAgregarLaboratorio.addEventListener("click", abrirRegistroLaboratorios);
btnCerrarRegistroLaboratorio.addEventListener("click", cerrarRegistroLaboratorio);

function abrirRegistroLaboratorios() {
    filaEditando = null;
    modoFormulario = "alta";
    formulario.reset();
    modal.showModal();
}

function cerrarRegistroLaboratorio() {
    filaEditando = null;
    formulario.reset();
    modal.close();
}
function abrirEditarLaboratorio(eventoModificar) {
    const btnEditar = eventoModificar.target.closest(".btnEditar");

    //Si el botón no se presionó, se sale de la función
    if (btnEditar === null) {
        return;
    }

    //Captura la fila en la que se encuentra el botón
    const fila = btnEditar.closest("tr");

    formulario.reset();
    modoFormulario = "modificar";

    //Escribe los datos del empleado provenientes de la fila en el formulario
    entradaIdLaboratorio.value = btnEditar.dataset.id;
    entradaTipoLaboratorio.value = fila.cells[0].textContent.trim();
    entradaNumAula.value = fila.cells[1].textContent.trim();
    entradaCantMonitores.value = fila.cells[2].textContent.trim();
    entradaCantTeclados.value = fila.cells[3].textContent.trim();
    entradaCantMouses.value = fila.cells[4].textContent.trim();
    entradaCantComputadoras.value = fila.cells[5].textContent.trim();

    //Muestra el cuadro de diálogo o "modal"
    modal.showModal();
}

modal.addEventListener("cancel", function () {
    filaEditando = null;
    formulario.reset();
});

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja el laboratorio
async function eliminarLaboratorio(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    const confirmacion = confirm("¿Está seguro de eliminar el laboratorio?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { ID_L: btnEliminar.dataset.id });
        window.alert(respuesta.mensaje);
        cargarLaboratorios();
    } catch (error) {
        window.alert(error.message);
    }
}

//Envía el formulario a la API: POST para el alta, PUT para la modificación
async function gestionarLaboratorio(evento) {
    evento.preventDefault();

    const datos = {
        ID_L: entradaIdLaboratorio.value,
        nombreLaboratorio: entradaTipoLaboratorio.value,
        numLaboratorio: entradaNumAula.value.trim(),
        cantMonitores: entradaCantMonitores.value,
        cantTeclados: entradaCantTeclados.value,
        cantMouses: entradaCantMouses.value,
        cantComputadoras: entradaCantComputadoras.value
    };

    try {
        const metodo = modoFormulario === "alta" ? "POST" : "PUT";
        const respuesta = await llamarApi(metodo, datos);
        window.alert(respuesta.mensaje);
        cerrarRegistroLaboratorio();
        cargarLaboratorios();
    } catch (error) {
        window.alert(error.message);
    }
}

tablaInventario.addEventListener("click", abrirEditarLaboratorio);
btnAgregarLaboratorio.addEventListener("click", abrirRegistroLaboratorios);
btnCerrarRegistroLaboratorio.addEventListener("click", cerrarRegistroLaboratorio);

//Baja de laboratorios desde el botón eliminar de cada fila
tablaInventario.addEventListener("click", eliminarLaboratorio);

formulario.addEventListener("submit", gestionarLaboratorio);

//Carga inicial de la tabla
cargarLaboratorios();
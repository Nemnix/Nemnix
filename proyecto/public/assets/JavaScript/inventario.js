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

    for (const valor of [laboratorio.ID_L, laboratorio.nombreLaboratorio, laboratorio.numLaboratorio, laboratorio.cantMonitores, laboratorio.cantTeclados, laboratorio.cantMouses, laboratorio.cantComputadoras]) {
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

// function cargarLaboratoriosLocal() {
    // const laboratoriosGuardados = localStorage.getItem("laboratorios");

    // if (laboratoriosGuardados === null) {
        // return [];
    // }

    // return JSON.parse(laboratoriosGuardados);
// }

// function actualizarLaboratoriosLocal(laboratorios) {
    // localStorage.setItem("laboratorios", JSON.stringify(laboratorios));
// }

// function guardarLaboratoriosLocal(laboratorio) {
    // const laboratorios = cargarLaboratoriosLocal();
    // laboratorios.push(laboratorio);
    // actualizarLaboratoriosLocal(laboratorios);
// }

// function actualizarTabla() {

    // while (tabla.rows.length > 1) {
        // tabla.deleteRow(1);
    // }

    // const laboratorios = cargarLaboratoriosLocal();

    // for (const laboratorio of laboratorios) {
        // agregarLaboratorio(laboratorio);
    // }
// }

// function modificarLaboratoriosLocal(laboratorioEditado) {
    // const laboratorios = cargarLaboratoriosLocal();

    // const laboratorio = laboratorios.find(
        // l => l.idLaboratorio === laboratorioEditado.idLaboratorio
    // );

    // if (laboratorio === undefined) {
        // return;
    // }

    // laboratorio.idLaboratorio = laboratorioEditado.idLaboratorio;
    // laboratorio.tipoLaboratorio = laboratorioEditado.tipoLaboratorio;
    // laboratorio.numeroAula = laboratorioEditado.numeroAula;
    // laboratorio.monitores = laboratorioEditado.monitores;
    // laboratorio.teclados = laboratorioEditado.teclados;
    // laboratorio.computadoras = laboratorioEditado.computadoras;
    // laboratorio.mouses = laboratorioEditado.mouses;

    // actualizarLaboratoriosLocal(laboratorios);
// }

// function eliminarLaboratorioLocal(idLaboratorio) {

    // const laboratorios = cargarLaboratoriosLocal();

    // const laboratoriosActualizados = laboratorios.filter(
        // l => l.idLaboratorio !== Number(idLaboratorio)
    // );

    // actualizarLaboratoriosLocal(laboratoriosActualizados);
// }

// function obtenerSiguienteIdLaboratorio() {
    // const laboratorios = cargarLaboratoriosLocal();
    // if (laboratorios.length === 0) {
        // return 1;
    // }
// const ids = laboratorios.map(l => parseInt(l.idLaboratorio, 10) || 0);
    // const idMaximo = Math.max(...ids);
    // return idMaximo + 1;
// }


// function obtenerDatosFormulario() {

    // let id;

    // if (filaEditando === null) {
        // id = obtenerSiguienteIdLaboratorio();
    // } else {
        // id = idEditando;
    // }

    // return {
        // idLaboratorio: id,
        // tipoLaboratorio: entradaTipoLaboratorio.value,
        // numeroAula: entradaNumAula.value,
        // monitores: entradaCantMonitores.value,
        // teclados: entradaCantTeclados.value,
        // computadoras: entradaCantComputadoras.value,
        // mouses: entradaCantMouses.value
    // };
// }

function abrirRegistroLaboratorios() {
    filaEditando = null;
    modoFormulario = "alta";
    formulario.reset();
    modal.showModal();
}

// function abrirRegistroEquipo() {
//     filaEditando = null;
//     formulario.reset();
//     entradaIdEquipo.value = obtenerSiguienteIdEquipo();
//     entradaIdEquipo.readOnly = true;
//     modal.showModal();
// }

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
    entradaTipoLaboratorio.value = fila.cells[1].textContent.trim();
    entradaNumAula.value = fila.cells[2].textContent.trim();
    entradaCantMonitores.value = fila.cells[3].textContent.trim();
    entradaCantTeclados.value = fila.cells[4].textContent.trim();
    entradaCantMouses.value = fila.cells[5].textContent.trim();
    entradaCantComputadoras.value = fila.cells[6].textContent.trim();

    //Muestra el cuadro de diálogo o "modal"
    modal.showModal();
}

modal.addEventListener("cancel", function () {
    filaEditando = null;
    formulario.reset();
});

// function agregarLaboratorio(laboratorio) {
    // let fila = document.createElement("tr");

    // let tdIdLaboratorio = document.createElement("td");
    // tdIdLaboratorio.innerText = laboratorio.idLaboratorio;

    // let tdTipoAula = document.createElement("td");
    // tdTipoAula.innerText = laboratorio.tipoLaboratorio;

    // let tdNumeroAula = document.createElement("td");
    // tdNumeroAula.innerText = laboratorio.numeroAula;

    // let tdMonitores = document.createElement("td");
    // tdMonitores.innerText = laboratorio.monitores;

    // let tdTeclados = document.createElement("td");
    // tdTeclados.innerText = laboratorio.teclados;

    // let tdComputadoras = document.createElement("td");
    // tdComputadoras.innerText = laboratorio.computadoras;

    // let tdMouses = document.createElement("td");
    // tdMouses.innerText = laboratorio.mouses;

    // let tdAcciones = document.createElement("td");

    // let btnModificar = document.createElement("button");
    // btnModificar.innerText = "Modificar";
    // btnModificar.onclick = function () {
        // modificar(this);
    // };

    // let btnEliminar = document.createElement("button");
    // btnEliminar.innerText = "Eliminar";
    // btnEliminar.onclick = function () {
        // borrar(this);
    // };

    // tdAcciones.appendChild(btnModificar);
    // tdAcciones.appendChild(btnEliminar);

    // fila.appendChild(tdIdLaboratorio);
    // fila.appendChild(tdTipoAula);
    // fila.appendChild(tdNumeroAula);
    // fila.appendChild(tdMonitores);
    // fila.appendChild(tdTeclados);
    // fila.appendChild(tdComputadoras);
    // fila.appendChild(tdMouses);    
    // fila.appendChild(tdAcciones);

    // tabla.appendChild(fila);
// }

// function borrar(boton) {

    // const fila = boton.parentNode.parentNode;

    // const obtenerSiguienteIdLaboratorio = Number(fila.children[0].innerText);

    // eliminarLaboratorioLocal(obtenerSiguienteIdLaboratorio);

    // actualizarTabla();
// }

// function modificar(boton) {

    // filaEditando = boton.parentNode.parentNode;

    // const celdas = filaEditando.getElementsByTagName("td");

    // idEditando = Number(celdas[0].innerText);

    // entradaTipoLaboratorio.value = celdas[1].innerText;
    // entradaNumAula.value = celdas[2].innerText;
    // entradaCantMonitores.value = celdas[3].innerText;
    // entradaCantTeclados.value = celdas[4].innerText;
    // entradaCantComputadoras.value = celdas[5].innerText;
    // entradaCantMouses.value = celdas[6].innerText;

    // modal.showModal();
// }

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



// formulario.addEventListener("submit", function (event) {
    // event.preventDefault();

    // const laboratorio = obtenerDatosFormulario();

    // if (filaEditando === null) {
        // guardarLaboratoriosLocal(laboratorio);
    // } else {
        // modificarLaboratoriosLocal(laboratorio);
        // filaEditando = null;
    // }

    // actualizarTabla();
    // modal.close();
// });

// actualizarTabla();
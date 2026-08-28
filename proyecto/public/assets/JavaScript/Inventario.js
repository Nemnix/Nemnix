const modal = document.getElementById("AgregarLaboratorio");

const formulario = document.getElementById("FormularioAgregarLaboratorio");

const btnAgregarLaboratorio = document.getElementById("btnAgregarLaboratorio");
const btnCerrarRegistroLaboratorio = document.getElementById("btnCerrarRegistroLaboratorio");

const tabla = document.getElementById("Inventario");
const entradaTipoLaboratorio = document.getElementById("registroLaboratorio");
const entradaNumAula = document.getElementById("registroNumLabTaller");
const entradaCantMonitores = document.getElementById("cantMonitores");
const entradaCantTeclados = document.getElementById("cantTeclados");
const entradaCantMouses = document.getElementById("cantMouses");
const entradaCantComputadoras = document.getElementById("cantComputadoras");


let filaEditando = null;
let idEditando = null;

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

btnAgregarLaboratorio.addEventListener("click", abrirRegistroLaboratorios);
btnCerrarRegistroLaboratorio.addEventListener("click", cerrarRegistroLaboratorio);

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
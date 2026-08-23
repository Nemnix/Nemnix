const modal = document.getElementById("PanelAdministradores");

const formulario = document.getElementById("FormularioPanelAdministradores");

const btnAgregarNuevoUsuario = document.getElementById("AgregarNuevoUsuario");
const btnCerrarPanel = document.getElementById("cerrarPanelAdministrador")

const tabla = document.getElementById("panelAdministradores");

const entradaCedula = document.getElementById("CIUsuario");
const entradaNombre = document.getElementById("nombreUsuario");
const entradaApellido = document.getElementById("apellidoUsuario");
const entradaCargo = document.getElementById("cargoUsuario");
const entradaContrasenia = document.getElementById("contraseniaUsuario");

let filaEditando = null;



// function cargarUsuariosLocal() {

//     const usuariosGuardados = localStorage.getItem("usuarios");

//     if (usuariosGuardados === null) {
//         return [];
//     }

//     return JSON.parse(usuariosGuardados);

// }

// function actualizarUsuariosLocal(usuarios) {

//     localStorage.setItem(
//         "usuarios",
//         JSON.stringify(usuarios)
//     );

// }

// function guardarUsuarioLocal(usuario) {

//     const usuarios = cargarUsuariosLocal();

//     usuarios.push(usuario);

//     actualizarUsuariosLocal(usuarios);

// }

// function actualizarTabla() {
//     tabla.querySelector("tbody").replaceChildren();
//     const usuarios = cargarUsuariosLocal();
//     for (const usuario of usuarios) {
//         agregarUsuario(usuario);
//     }
// }


// function modificarUsuarioLocal(usuarioEditado) {

//     const usuarios = cargarUsuariosLocal();

//     const usuario = usuarios.find(
//         u => u.cedula === usuarioEditado.cedula
//     );

//     if (usuario === undefined) {
//         return;
//     }

//     usuario.nombre = usuarioEditado.nombre;
//     usuario.apellido = usuarioEditado.apellido;
//     usuario.cargo = usuarioEditado.cargo;
//     usuario.contrasenia = usuarioEditado.contrasenia;

//     actualizarUsuariosLocal(usuarios);
// }

// function eliminarUsuarioLocal(cedula) {

//     const usuarios = cargarUsuariosLocal();

//     const usuariosActualizados = usuarios.filter(
//         u => u.cedula !== cedula
//     );

//     actualizarUsuariosLocal(usuariosActualizados);
// }

// function obtenerContraseniaPorCedula(cedula) {

//     const usuarios = cargarUsuariosLocal();

//     const usuario = usuarios.find(
//         u => u.cedula === cedula
//     );

//     if (usuario === undefined) {
//         return "";
//     }

//     return usuario.contrasenia;

// }

// function obtenerDatosFormulario() {

//     return {
//         cedula: entradaCedula.value,
//         nombre: entradaNombre.value,
//         apellido: entradaApellido.value,
//         cargo: entradaCargo.value,
//         contrasenia: entradaContrasenia.value
//     };

// }

function abrirAltaUsuario() {

    filaEditando = null;
    formulario.reset();
    entradaCedula.readOnly = false;
    modal.showModal();

}

function cerrarAltausuario() {

    filaEditando = null;
    formulario.reset();
    modal.close();

}


modal.addEventListener("cancel", function () {

    filaEditando = null;
    formulario.reset();

});

// function agregarUsuario(usuario) {
 
//     let fila = document.createElement("tr");
 
//     let tdCedula = document.createElement("td");
//     tdCedula.innerText = usuario.cedula;
 
//     let tdNombre = document.createElement("td");
//     tdNombre.innerText = usuario.nombre;
 
//     let tdApellido = document.createElement("td");
//     tdApellido.innerText = usuario.apellido;
 
//     let tdCargo = document.createElement("td");
//     tdCargo.innerText = usuario.cargo;
 
//     let tdAcciones = document.createElement("td");
 
//     let btnModificar = document.createElement("button");
//     btnModificar.innerText = "Modificar";
//     btnModificar.onclick = function () {
//         modificar(this);
//     };
 
//     let btnEliminar = document.createElement("button");
//     btnEliminar.innerText = "Eliminar";
//     btnEliminar.onclick = function () {
//         borrar(this);
//     };
 
//     tdAcciones.appendChild(btnModificar);
//     tdAcciones.appendChild(btnEliminar);
 
//     fila.appendChild(tdCedula);
//     fila.appendChild(tdNombre);
//     fila.appendChild(tdApellido);
//     fila.appendChild(tdCargo);
//     fila.appendChild(tdAcciones);
 
//     tabla.querySelector("tbody").appendChild(fila);
 
// }


// function borrar(boton) {

//     const fila = boton.parentNode.parentNode;

//     const cedula = fila.children[0].innerText;

//     eliminarUsuarioLocal(cedula);

//     actualizarTabla();

// }

// function modificar(boton) {

//     filaEditando = boton.parentNode.parentNode;

//     const celdas = filaEditando.getElementsByTagName("td");

//     entradaCedula.value = celdas[0].innerText;
//     entradaCedula.readOnly = true;
//     entradaNombre.value = celdas[1].innerText;
//     entradaApellido.value = celdas[2].innerText;
//     entradaCargo.value = celdas[3].innerText;
//     entradaContrasenia.value = obtenerContraseniaPorCedula(entradaCedula.value);

//     modal.showModal();

// }

// function validarFormulario(usuario) {

//     if (!usuario.cedula || !usuario.nombre || !usuario.apellido || !usuario.cargo || !usuario.contrasenia) {
//         alert("Complete todos los campos");
//         return false;
//     }

//     if (!/^\d+$/.test(usuario.cedula)) {
//         alert("La cédula debe contener solamente números");
//         return false;
//     }

//     if (!/^\d{8}$/.test(usuario.cedula)) {
//         alert("La cédula debe tener 8 números");
//         return false;
//     }

//     return true;

// }


// btnAgregarNuevoUsuario.addEventListener("click", abrirAltaUsuario);

// btnCerrarPanel.addEventListener("click", cerrarAltausuario);

// formulario.addEventListener("submit", function (event) {

//     event.preventDefault();

//     const usuario = obtenerDatosFormulario();

//     if (!validarFormulario(usuario)) {
//         return;
//     }

//     if (filaEditando === null) {

//         guardarUsuarioLocal(usuario);

//     } else {

//         modificarUsuarioLocal(usuario);

//         filaEditando = null;
//     }

//     actualizarTabla();

//     modal.close();
// });

// actualizarTabla();
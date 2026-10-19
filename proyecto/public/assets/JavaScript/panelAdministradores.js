/**
 * CONSTANTES Y VARIABLES NECESARIAS
 */

//Dirección de la API de usuarios
const API_USUARIOS = "../index.php?ruta=usuarios";

//Constantes para el cuadro de diálogo
const btnAltaEmpleado = document.getElementById("btnAltaEmpleado");
const btnCerrarGestionarEmpleado = document.getElementById("btnCerrarGestionarEmpleado");
const dialogGestionarEmpleado = document.querySelector(".dialogGestionarEmpleado");

//Constante para trabajar con la tabla de empleados
const cuerpoTablaEmpleados = document.getElementById("cuerpoTablaEmpleados");

//Constante para manipular el formulario, especialmente limpiarlo
const formularioGestionarEmpleado = document.getElementById("formularioGestionarEmpleado");

//Campos del formulario
const entradaCedula = document.getElementById("cedula");
const entradaNombre = document.getElementById("nombre");
const entradaApellido = document.getElementById("apellido");
const entradaClave = document.getElementById("clave");
const entradaConfirmarClave = document.getElementById("confirmarClave");
const entradaRol = document.getElementById("rol");
const entradaGmail = document.getElementById("gmail");
const itemGmail = document.getElementById("itemGmail");

//Estado para saber si se está modificando o dando de alta
let modoFormulario = "";


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

    const respuesta = await fetch(API_USUARIOS, opciones);
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
 * TABLA DE EMPLEADOS
 */

//Crea la fila de un usuario. Usa textContent para que los datos no se interpreten como HTML
function crearFila(usuario) {
    const fila = document.createElement("tr");
    //El gmail no se muestra en la tabla, se guarda en la fila para precargarlo al editar
    fila.dataset.gmail = usuario.gmail ?? "";

    for (const valor of [usuario.cedula, usuario.nombre, usuario.apellido, usuario.cargo]) {
        const celda = document.createElement("td");
        celda.textContent = valor;
        fila.appendChild(celda);
    }

    const celdaAcciones = document.createElement("td");
    const cajaOperaciones = document.createElement("div");
    cajaOperaciones.className = "cajaOperaciones";

    const btnModificar = document.createElement("button");
    btnModificar.type = "button";
    btnModificar.className = "btnOperacion btnModificar";
    btnModificar.textContent = "Editar";

    const btnEliminar = document.createElement("button");
    btnEliminar.type = "button";
    btnEliminar.className = "btnEliminar";
    btnEliminar.textContent = "Eliminar";
    btnEliminar.dataset.cedula = usuario.cedula;

    cajaOperaciones.append(btnModificar, btnEliminar);
    celdaAcciones.appendChild(cajaOperaciones);
    fila.appendChild(celdaAcciones);

    return fila;
}

//Pide los usuarios a la API y dibuja la tabla
async function cargarUsuarios() {
    try {
        const usuarios = await llamarApi("GET");
        cuerpoTablaEmpleados.replaceChildren(...usuarios.map(crearFila));
    } catch (error) {
        window.alert(error.message);
    }
}


/**
 * GESTION DEL ESTADO DEL FORMULARIO/MODAL
 */

//Limpia todos los campos y configuraciones seleccionadas del formulario
function limpiarEstadoGestionarEmpleado() {
    modoFormulario = "";
    entradaCedula.readOnly = false;
    formularioGestionarEmpleado.reset();
}

//Muestra el campo gmail solo si el cargo elegido es Solicitante, y en ese caso lo hace obligatorio
function actualizarCampoGmail() {
    const esSolicitante = entradaRol.value === "Solicitante";
    itemGmail.hidden = !esSolicitante;
    entradaGmail.required = esSolicitante;
}

function abrirAltaEmpleado() {
    entradaCedula.readOnly = false;
    formularioGestionarEmpleado.reset();
    modoFormulario = "alta";

    //En el alta la contraseña es obligatoria
    entradaClave.required = true;
    entradaConfirmarClave.required = true;

    //El formulario arranca sin cargo, por lo que se oculta el gmail
    actualizarCampoGmail();

    //Muestra el cuadro de diálogo o "modal"
    dialogGestionarEmpleado.showModal();
}

function cerrarGestionarEmpleado() {
    entradaCedula.readOnly = false;
    formularioGestionarEmpleado.reset();
    modoFormulario = "";
    //Cierra el modal
    dialogGestionarEmpleado.close();
}

//Función que escucha los clicks de la tabla, si es proveniente del botón eliminar da de baja al usuario
async function eliminarEmpleado(eventoEliminar) {
    const btnEliminar = eventoEliminar.target.closest(".btnEliminar");

    //Si el botón no se presionó, se sale de la función
    if (btnEliminar === null) {
        return;
    }

    //Mensaje de confirmación, se puede reemplazar por cuadros de diálogo (IMPLEMENTAR EN UN FUTURO)
    const confirmacion = confirm("¿Está seguro de eliminar el usuario?");

    if (!confirmacion) {
        return;
    }

    try {
        const respuesta = await llamarApi("DELETE", { cedula: btnEliminar.dataset.cedula });
        window.alert(respuesta.mensaje);
        cargarUsuarios();
    } catch (error) {
        window.alert(error.message);
    }
}

//Función que escucha los clicks de la tabla, si es proveniente del botón empleado abre el modal
function abrirModificarEmpleado(eventoModificar) {
    //Captura el valor del botón "Modificar"
    const btnModificar = eventoModificar.target.closest(".btnModificar");

    //Si el botón no se presionó, se sale de la función
    if (btnModificar === null) {
        return;
    }

    //Captura la fila en la que se encuentra el botón
    const fila = btnModificar.closest("tr");

    entradaCedula.readOnly = true;
    formularioGestionarEmpleado.reset();
    modoFormulario = "modificar";

    //Al modificar la contraseña es opcional: si se deja vacía, se mantiene la actual
    entradaClave.required = false;
    entradaConfirmarClave.required = false;

    //Escribe los datos del empleado provenientes de la fila en el formulario
    entradaCedula.value = fila.cells[0].textContent.trim();
    entradaNombre.value = fila.cells[1].textContent.trim();
    entradaApellido.value = fila.cells[2].textContent.trim();
    entradaRol.value = fila.cells[3].textContent.trim();
    entradaGmail.value = fila.dataset.gmail;
    actualizarCampoGmail();

    //Muestra el modal
    dialogGestionarEmpleado.showModal();
}

//Envía el formulario a la API: POST para el alta, PUT para la modificación
async function gestionarEmpleado(evento) {
    evento.preventDefault();

    const datos = {
        cedula: entradaCedula.value.trim(),
        nombre: entradaNombre.value.trim(),
        apellido: entradaApellido.value.trim(),
        rol: entradaRol.value,
        gmail: entradaGmail.value.trim(),
        clave: entradaClave.value,
        confirmarClave: entradaConfirmarClave.value
    };

    //La API no recibe confirmarClave al modificar, por eso se compara también acá
    if (datos.clave !== datos.confirmarClave) {
        window.alert("Las contraseñas no coinciden.");
        return;
    }

    try {
        const metodo = modoFormulario === "alta" ? "POST" : "PUT";
        const respuesta = await llamarApi(metodo, datos);
        window.alert(respuesta.mensaje);
        cerrarGestionarEmpleado();
        cargarUsuarios();
    } catch (error) {
        window.alert(error.message);
    }
}

//Muestra u oculta el campo gmail al cambiar el cargo
entradaRol.addEventListener("change", actualizarCampoGmail);

//Apertura y cierre del modal
btnAltaEmpleado.addEventListener("click", abrirAltaEmpleado);
cuerpoTablaEmpleados.addEventListener("click", abrirModificarEmpleado);

btnCerrarGestionarEmpleado.addEventListener("click", cerrarGestionarEmpleado);

//Para hacer que al apretar escape tambien se eliminen los cambios del formulario, se puede usar el evento 'cancel'
dialogGestionarEmpleado.addEventListener("cancel", limpiarEstadoGestionarEmpleado);

formularioGestionarEmpleado.addEventListener("submit", gestionarEmpleado);

//Baja de empleados desde el botón eliminar de cada fila
cuerpoTablaEmpleados.addEventListener("click", eliminarEmpleado);

//Carga inicial de la tabla
cargarUsuarios();

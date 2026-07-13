const formulario = document.querySelector("form");

const entradaCedula = document.getElementById("CI");
const entradaContrasenia = document.getElementById("Contraseña");

formulario.addEventListener("submit", function (event) {

    event.preventDefault();

    const cedula = entradaCedula.value;
    const contrasenia = entradaContrasenia.value;

    if (cedula === "11111111" && contrasenia === "tecnico123") {

        window.location.href = "usuarioTecnico/paginaBienvenidaTecnico.html";

    } else if (cedula === "22222222" && contrasenia === "coordinador123") {

        window.location.href = "usuarioAdministrador/paginaBienvenidaCoordinador.html";

    } else if (cedula === "33333333" && contrasenia === "solicitante123") {

        window.location.href = "usuarioSolicitante/paginaBienvenidaSolicitante.html";

    } else {

        alert("Cédula o contraseña incorrecta");

    }

}); 
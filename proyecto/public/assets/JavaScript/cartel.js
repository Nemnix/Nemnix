
const cartel = document.createElement("dialog");
const mensajeCartel = document.createElement("p");
const btnAceptarCartel = document.createElement("button");

btnAceptarCartel.type = "button";
btnAceptarCartel.textContent = "Aceptar";

cartel.classList.add("cartel");
cartel.appendChild(mensajeCartel);
cartel.appendChild(btnAceptarCartel);

document.querySelector("body").appendChild(cartel);


function mostrarCartel(texto) {
    mensajeCartel.textContent = texto;
    cartel.showModal();
}


function cerrarCartel() {
    cartel.close();
}

btnAceptarCartel.addEventListener("click", cerrarCartel);

window.alert = mostrarCartel;

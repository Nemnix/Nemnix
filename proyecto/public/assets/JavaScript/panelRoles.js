//Roles guardados por login.js al iniciar sesión
const roles = JSON.parse(sessionStorage.getItem("roles") ?? "{}");

document.getElementById("nombreUsuario").textContent = sessionStorage.getItem("nombre") ?? "";

//Solo se muestran los cargos que tiene el usuario
//(se usa style.display porque .PanelRoles tiene display:flex y pisa el atributo hidden)
document.getElementById("rolAdministrador").style.display = roles.administrador ? "" : "none";
document.getElementById("rolTecnico").style.display = roles.tecnico ? "" : "none";
document.getElementById("rolSolicitante").style.display = roles.solicitante ? "" : "none";

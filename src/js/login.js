/*
 * Validación del formulario de login en el navegador (Tema 3: DOM y eventos).
 * Solo comprueba que los campos no estén vacíos; si el usuario y la contraseña
 * son correctos lo decide el servidor (login_user.php).
 *
 * Se carga en el <head> con defer: cuando se ejecuta, el DOM ya existe.
 *
 * Autor: Juan (persona A)
 */

const formLogin = document.querySelector("#form_login");
formLogin.noValidate = true;   // usamos nuestros mensajes en lugar de los del navegador

const mensajesVacio = {
    login: "Escribe tu usuario",
    pwd: "Escribe tu contraseña",
};

/** Escribe el mensaje del campo y devuelve true si no está vacío */
function campoRelleno(campo) {
    const vacio = campo.value.trim() === "";
    document.querySelector("#error-" + campo.name).textContent = vacio ? mensajesVacio[campo.name] : "";
    campo.setAttribute("aria-invalid", vacio ? "true" : "false");
    return !vacio;
}

formLogin.addEventListener("input", (event) => {
    if (event.target.name in mensajesVacio) campoRelleno(event.target);
});

formLogin.addEventListener("submit", (event) => {
    const loginOk = campoRelleno(formLogin.login);
    const pwdOk = campoRelleno(formLogin.pwd);
    if (!loginOk || !pwdOk) {
        event.preventDefault();
        (loginOk ? formLogin.pwd : formLogin.login).focus();
    }
});

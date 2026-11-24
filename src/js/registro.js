/*
 * Validación del formulario de registro en el navegador (Tema 3: DOM y eventos).
 *
 * - Evento "input": se valida el campo mientras se escribe y se muestra el
 *   mensaje en su <span class="error-campo">.
 * - Evento "submit": se validan todos; si alguno falla, preventDefault()
 *   cancela el envío y se pone el foco en el primer campo incorrecto.
 * Las mismas reglas se comprueban otra vez en PHP (reg_user.php): la
 * validación del navegador se puede saltar, la del servidor no.
 *
 * Se carga en el <head> con defer: cuando se ejecuta, el DOM ya existe.
 *
 * Autor: Juan (persona A)
 */

const form = document.querySelector("#form_registro");

// Con JavaScript mostramos nuestros mensajes; sin JavaScript actúan los
// atributos HTML (required, pattern...). noValidate desactiva los del navegador.
form.noValidate = true;

// Reglas de cada campo: devuelven el mensaje de error o "" si es correcto
const reglas = {
    login: (valor) => {
        if (valor.trim() === "") return "Escribe un nombre de usuario";
        if (!/^[A-Za-z0-9_.\-]{7,30}$/.test(valor.trim()))
            return "Entre 7 y 30 caracteres: letras, números, _ . -";
        return "";
    },
    email: (valor) => {
        if (valor.trim() === "") return "Escribe tu correo electrónico";
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor.trim())) return "El correo no es válido";
        return "";
    },
    pwd: (valor) => {
        if (valor.length < 7) return "Mínimo 7 caracteres";
        if (!/\d/.test(valor)) return "Debe tener al menos 1 número";
        return "";
    },
    pwd2: (valor) => {
        if (valor === "") return "Repite la contraseña";
        if (valor !== form.pwd.value) return "Las contraseñas no coinciden";
        return "";
    },
};

/** Valida un campo, escribe su mensaje y devuelve true si es correcto */
function validarCampo(campo) {
    const mensaje = reglas[campo.name](campo.value);
    document.querySelector("#error-" + campo.name).textContent = mensaje;
    campo.setAttribute("aria-invalid", mensaje === "" ? "false" : "true");
    return mensaje === "";
}

// Mientras se escribe: se valida el campo que ha cambiado
form.addEventListener("input", (event) => {
    const campo = event.target;
    if (campo.name in reglas) {
        validarCampo(campo);
        // si cambia la contraseña, la repetida puede dejar de coincidir
        if (campo.name === "pwd" && form.pwd2.value !== "") validarCampo(form.pwd2);
    }
});

// Al enviar: se validan todos los campos
form.addEventListener("submit", (event) => {
    let primerError = null;
    for (const nombre in reglas) {
        const campo = form.elements[nombre];
        if (!validarCampo(campo) && primerError === null) primerError = campo;
    }
    if (primerError !== null) {
        event.preventDefault();   // no se envía el formulario
        primerError.focus();
    }
});

// Al deshacer (reset): se borran los mensajes
form.addEventListener("reset", () => {
    for (const nombre in reglas) {
        document.querySelector("#error-" + nombre).textContent = "";
        form.elements[nombre].removeAttribute("aria-invalid");
    }
});

/*
 * Personalización del portal para cada usuario (como B4, Pas 10 y Pas 11).
 *
 * - PHP escribe el usuario de la sesión en <body data-user="..."> (partials/header.php)
 *   y aquí se lee con dataset. Si está vacío (anónimo) no se personaliza nada.
 * - Las preferencias se guardan en el navegador con localStorage, con el nombre del
 *   usuario delante de cada clave (p. ej. "cliente_pruebaTema"): así cada usuario
 *   tiene su propio entorno aunque usen el mismo navegador.
 * - Se aplican en TODAS las páginas (este fichero lo carga header.php siempre):
 *     tema oscuro  -> clase "tema-oscuro" en <html> (colores en css/tema-oscuro.css)
 *     tamaño texto -> variable CSS --tamano-texto con setProperty (la usa estilo.css)
 * - En "Mi perfil" está el formulario: no se envía al servidor (preventDefault),
 *   muestra una vista previa al cambiarlo y tiene un botón para restablecer.
 *
 * Se carga en el <head> con defer: cuando se ejecuta, el DOM ya existe.
 *
 * Autor: Juan (persona A)
 */

const usuario = document.body.dataset.user;                 // "" si no hay sesión
const raiz = document.documentElement;                      // <html>
const formPersonalizar = document.querySelector("#form-personalizar");   // solo en Mi perfil
const mensajePersonalizar = document.querySelector("#mensaje-personalizar");

// Tamaños permitidos (en el formulario son las opciones del <select>)
const tamanos = ["90%", "100%", "115%", "130%"];

/** Aplica un tema ("claro" u "oscuro") y un tamaño de texto a la página */
function aplicar(tema, tamano) {
    raiz.classList.toggle("tema-oscuro", tema === "oscuro");
    if (tamanos.includes(tamano)) {
        raiz.style.setProperty("--tamano-texto", tamano);
    } else {
        raiz.style.removeProperty("--tamano-texto");   // vuelve al de estilo.css
    }
}

/** Escribe el mensaje del formulario (solo existe en Mi perfil) */
function avisar(texto) {
    if (mensajePersonalizar) mensajePersonalizar.textContent = texto;
}

/** Lee la personalización guardada del usuario y la aplica (todas las páginas) */
function cargarPersonalizacion() {
    const tema = localStorage.getItem(usuario + "Tema") ?? "claro";
    const tamano = localStorage.getItem(usuario + "TamanoTexto") ?? "100%";
    aplicar(tema, tamano);
    if (formPersonalizar) {
        // el formulario muestra lo que tiene guardado
        formPersonalizar.tema.value = tema === "oscuro" ? "oscuro" : "claro";
        formPersonalizar.tamano.value = tamanos.includes(tamano) ? tamano : "100%";
    }
}

/** Guarda lo elegido en localStorage con el usuario como prefijo */
function guardarPersonalizacion(event) {
    event.preventDefault();   // no se envía al servidor
    localStorage.setItem(usuario + "Tema", formPersonalizar.tema.value);
    localStorage.setItem(usuario + "TamanoTexto", formPersonalizar.tamano.value);
    aplicar(formPersonalizar.tema.value, formPersonalizar.tamano.value);
    avisar("Personalización guardada para " + usuario);
}

/** Vista previa mientras se cambia el formulario (sin guardar) */
function vistaPrevia() {
    aplicar(formPersonalizar.tema.value, formPersonalizar.tamano.value);
    avisar("Vista previa: pulsa Guardar para conservar los cambios");
}

/** Borra la personalización del usuario y vuelve al aspecto por defecto */
function restablecer() {
    localStorage.removeItem(usuario + "Tema");
    localStorage.removeItem(usuario + "TamanoTexto");
    cargarPersonalizacion();
    avisar("Aspecto por defecto restablecido");
}

if (usuario) {                       // los anónimos ven el portal por defecto
    cargarPersonalizacion();
    if (formPersonalizar) {
        formPersonalizar.addEventListener("submit", guardarPersonalizacion);
        formPersonalizar.addEventListener("input", vistaPrevia);
        document.querySelector("#restablecer-personalizar").addEventListener("click", restablecer);
    }
}

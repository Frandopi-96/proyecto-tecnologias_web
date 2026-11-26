/*
 * Avatar dibujado en Canvas (B4: firma en canvas y firma_foto.js) y enviado al
 * servidor con fetch + FormData (Tema 4).
 *
 * - Dibujo con eventos de ratón (MouseEvent): mousedown empieza el trazo,
 *   mousemove lo sigue con offsetX/offsetY y mouseup o mouseleave lo terminan.
 * - Limpiar: clearRect y fondo blanco otra vez.
 * - Guardar: canvas.toDataURL("image/png") -> fetch(dataURL).blob() -> FormData
 *   -> POST a api/guardar_avatar.php, que lo recibe en $_FILES.
 *   Se comprueba respuesta.ok y se muestra el avatar nuevo en el perfil y en el menú.
 *
 * Se carga en el <head> con defer: cuando se ejecuta, el DOM ya existe.
 *
 * Autor: Juan (persona A)
 */

const lienzo = document.querySelector("#lienzo-avatar");
const ctx = lienzo.getContext("2d");
const colorPincel = document.querySelector("#color-pincel");
const grosorPincel = document.querySelector("#grosor-pincel");
const estadoAvatar = document.querySelector("#estado-avatar");
const botonGuardar = document.querySelector("#guardar-avatar");

let dibujando = false;    // true mientras el botón del ratón está pulsado
let hayDibujo = false;    // para no guardar un avatar en blanco

/** Deja el lienzo en blanco (el PNG tiene fondo blanco, también en tema oscuro) */
function limpiarLienzo() {
    ctx.clearRect(0, 0, lienzo.width, lienzo.height);
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, lienzo.width, lienzo.height);
    hayDibujo = false;
}

lienzo.addEventListener("mousedown", (event) => {
    dibujando = true;
    ctx.strokeStyle = colorPincel.value;
    ctx.lineWidth = grosorPincel.value;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.beginPath();
    ctx.moveTo(event.offsetX, event.offsetY);
    // un clic sin mover también deja un punto
    ctx.lineTo(event.offsetX, event.offsetY);
    ctx.stroke();
    hayDibujo = true;
});

lienzo.addEventListener("mousemove", (event) => {
    if (!dibujando) return;
    ctx.lineTo(event.offsetX, event.offsetY);
    ctx.stroke();
});

// Al soltar el botón o salir del lienzo se acaba el trazo
lienzo.addEventListener("mouseup", () => { dibujando = false; });
lienzo.addEventListener("mouseleave", () => { dibujando = false; });

document.querySelector("#limpiar-avatar").addEventListener("click", () => {
    limpiarLienzo();
    estadoAvatar.textContent = "Lienzo borrado";
});

/** Cambia el avatar del perfil y el del menú sin recargar la página */
function mostrarAvatarNuevo(ruta) {
    const url = ruta + "?v=" + Date.now();   // evita que el navegador use la imagen vieja de la caché
    const imgPerfil = document.querySelector("#avatar-guardado");
    imgPerfil.src = url;
    imgPerfil.hidden = false;
    document.querySelector("#sin-avatar").textContent = "Avatar guardado";

    let imgMenu = document.querySelector(".bienvenida img");
    if (!imgMenu) {
        imgMenu = document.createElement("img");
        imgMenu.width = 32;
        imgMenu.height = 32;
        imgMenu.alt = "";   // decorativo: al lado ya pone el nombre del usuario
        document.querySelector(".bienvenida").prepend(imgMenu);
    }
    imgMenu.src = url;
}

botonGuardar.addEventListener("click", async () => {
    if (!hayDibujo) {
        estadoAvatar.textContent = "Dibuja algo antes de guardar el avatar";
        return;
    }
    botonGuardar.disabled = true;
    estadoAvatar.textContent = "Guardando avatar...";
    try {
        // Canvas -> DataURL -> Blob (fichero PNG) -> FormData, como firma_foto.js de B4
        const dataURL = lienzo.toDataURL("image/png");
        const blob = await (await fetch(dataURL)).blob();
        const datos = new FormData();
        datos.append("avatar", blob, "avatar.png");

        const respuesta = await fetch("api/guardar_avatar.php", { method: "POST", body: datos });
        let resultado = null;
        try {
            resultado = await respuesta.json();   // {"avatar": ...} o {"error": ...}
        } catch (e) {
            // la respuesta no era JSON
        }
        if (!respuesta.ok) {
            throw new Error(resultado && resultado.error
                ? resultado.error
                : "Error " + respuesta.status + " al guardar el avatar");
        }
        mostrarAvatarNuevo(resultado.avatar);
        estadoAvatar.textContent = resultado.mensaje;
    } catch (error) {
        // TypeError = no se ha podido conectar con el servidor
        estadoAvatar.textContent = error instanceof TypeError
            ? "No se ha podido conectar con el servidor."
            : error.message;
    } finally {
        botonGuardar.disabled = false;
    }
});

limpiarLienzo();

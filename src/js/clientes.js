/*
 * Listado asíncrono de clientes (solo gestor). Tema 4: fetch + JSON + DOM.
 *
 * Pide api/clientes.php, comprueba respuesta.ok y pinta cada cliente como una
 * fila de la tabla. Los datos se escriben con textContent (nunca innerHTML),
 * así un login o un correo con HTML no se interpreta como código.
 * Mensajes: lista vacía, sin permiso (403) o error de red / servidor.
 *
 * Se carga en el <head> con defer: cuando se ejecuta, el DOM ya existe.
 *
 * Autor: Juan (persona A)
 */

const tablaClientes = document.querySelector("#tabla-clientes");
const cuerpoTabla = tablaClientes.querySelector("tbody");
const estadoClientes = document.querySelector("#estado-clientes");
const botonActualizar = document.querySelector("#actualizar-clientes");

/** Crea una celda <td> con el texto indicado */
function crearCelda(texto) {
    const celda = document.createElement("td");
    celda.textContent = texto;
    return celda;
}

/** Vacía la tabla y la rellena con la lista de clientes */
function pintarClientes(clientes) {
    cuerpoTabla.replaceChildren();   // borra las filas de una carga anterior

    if (clientes.length === 0) {
        tablaClientes.hidden = true;
        estadoClientes.textContent = "Todavía no hay clientes registrados.";
        return;
    }

    for (const cliente of clientes) {
        const fila = document.createElement("tr");
        fila.append(
            crearCelda(cliente.login),
            crearCelda(cliente.email),
            crearCelda(cliente.rol === "gestor" ? "Gestor" : "Cliente"),
            crearCelda(cliente.suscrito ? "Sí" : "No")
        );
        cuerpoTabla.append(fila);
    }
    tablaClientes.hidden = false;
    estadoClientes.textContent = clientes.length === 1
        ? "1 cliente registrado."
        : clientes.length + " clientes registrados.";
}

/** Pide la lista al servidor y la muestra; si algo falla, enseña el motivo */
async function cargarClientes() {
    estadoClientes.textContent = "Cargando clientes...";
    botonActualizar.disabled = true;
    try {
        const respuesta = await fetch("api/clientes.php");
        if (!respuesta.ok) {
            // La API responde {"error": "..."} con 403 o 500
            let mensaje = "Error " + respuesta.status + " al cargar los clientes";
            try {
                const datos = await respuesta.json();
                if (datos.error) mensaje = datos.error;
            } catch (e) {
                // la respuesta no era JSON: se deja el mensaje con el código
            }
            throw new Error(mensaje);
        }
        const clientes = await respuesta.json();
        pintarClientes(clientes);
    } catch (error) {
        cuerpoTabla.replaceChildren();
        tablaClientes.hidden = true;
        // TypeError = no se ha podido conectar con el servidor
        estadoClientes.textContent = error instanceof TypeError
            ? "No se ha podido conectar con el servidor."
            : error.message;
    } finally {
        botonActualizar.disabled = false;
    }
}

botonActualizar.addEventListener("click", cargarClientes);
cargarClientes();

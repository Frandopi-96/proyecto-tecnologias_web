<?php
/**
 * Controlador principal del portal de actos culturales (como portal.php de B2 y B3).
 *
 * Esquema de la URL: portal.php?action=xxx
 *   - Sin action se muestra la página principal (home).
 *   - Cada action elige el fichero de la parte central ($central) y, si hace falta,
 *     los scripts ($js) y estilos ($css) propios de esa página.
 *
 * Para añadir una página nueva:
 *   1. Un "case" en el switch con su $central (partial en partials/).
 *   2. Si es solo para usuarios con sesión o para el gestor, una línea en $permisos.
 *   3. Si lleva JavaScript: array_push($js, "js/<parte>.js"); se carga en el <head> con defer.
 *
 * Errores: se acumulan en $error_msg y se muestran en partials/error.php antes del <main>.
 *
 * @author Juan (persona A)
 */

ini_set('display_errors', 1); // modo depuración: muestra los errores no controlados en el navegador

$error_msg = [];   // mensajes de error que se muestran antes del <main>
$exito_msg = "";   // mensaje de éxito (p. ej. "Acto guardado con éxito")
$js  = [];         // scripts de la página: "js/xxx.js"
$css = [];         // hojas de estilo propias de la página: "css/xxx.css"

// Sesión y conexión: antes de imprimir nada (session_start envía una cookie)
require_once(dirname(__FILE__) . "/includedb/sesion.php");
require_once(dirname(__FILE__) . "/includedb/conexion.php");

$action = (array_key_exists('action', $_REQUEST)) ? $_REQUEST["action"] : "home";

// B3 Pas_2: las acciones solo se aceptan si la petición viene de una página
// de nuestro propio servidor (el host del referer es el del servidor)
if (array_key_exists('action', $_REQUEST)) {
    $referer_host = isset($_SERVER['HTTP_REFERER'])
        ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)
        : null;
    if ($referer_host !== $_SERVER['SERVER_NAME']) {
        array_push($error_msg, "Acceso no permitido");
        $action = "home";
    }
}

// Control de acceso por rol (Tema 2: autorización RBAC; B3 Pas_10 y Pas_13).
// Ocultar la opción del menú no basta: aquí se comprueba en el servidor.
//   "logueado" -> cualquier usuario con sesión (client o gestor)
//   "gestor"   -> solo el gestor
// El gestor también puede hacer todo lo de "logueado" (B3: el gestor ve las opciones del client).
$permisos = [
    // "form_compra" => "logueado",
    // "form_acto"   => "gestor",
];
if (array_key_exists($action, $permisos)) {
    $permitido = ($permisos[$action] == "gestor") ? es_gestor() : esta_logueado();
    if (!$permitido) {
        array_push($error_msg, "Autorización no permitida");
        $action = "home";
    }
}

switch ($action) {
    case "home":
        $central = "/partials/home.php";
        break;
    default:
        array_push($error_msg, "Opción no correcta");
        $central = "/partials/home.php";
        break;
}

// Carga modular de la página
require_once(dirname(__FILE__) . "/partials/header.php");
require_once(dirname(__FILE__) . "/partials/menu.php");

if (count($error_msg) > 0) {
    require_once(dirname(__FILE__) . "/partials/error.php");
}
if ($exito_msg !== "") {
    print('<aside class="exito" role="status">' . htmlspecialchars($exito_msg) . '</aside>');
}

print("<main>\n");
require_once(dirname(__FILE__) . $central);
print("</main>\n");

require_once(dirname(__FILE__) . "/partials/footer.php");

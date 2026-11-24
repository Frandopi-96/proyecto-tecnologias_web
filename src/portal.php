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
// Niveles de tiene_acceso() (includedb/sesion.php): "anonimo", "logueado" o "gestor".
// Las acciones que no están en la tabla son para todos.
$permisos = [
    // Parte A: usuarios
    "form_user"  => "anonimo",
    "reg_user"   => "anonimo",
    "login_user" => "anonimo",
    "exit"       => "logueado",
    "listado_clientes" => "gestor",
    // Cada parte añade aquí sus acciones, p. ej.:
    // "form_compra" => "logueado",
    // "form_acto"   => "gestor",
];
if (array_key_exists($action, $permisos) && !tiene_acceso($permisos[$action])) {
    if ($permisos[$action] == "anonimo") {
        array_push($error_msg, "Ya has iniciado sesión");
        $action = "home";
    } elseif (!esta_logueado()) {
        // Sin sesión: se ofrece el formulario de login en lugar de la página pedida
        array_push($error_msg, "Debes iniciar sesión para acceder a esa página");
        $action = "pedir_login";
    } else {
        array_push($error_msg, "Autorización no permitida");
        $action = "home";
    }
}

switch ($action) {
    case "home":
        $central = "/partials/home.php";
        break;
    case "form_user":   // formulario de registro de clientes (persona A)
    case "reg_user":    // procesa el registro (POST)
        if ($action == "reg_user" && $_SERVER["REQUEST_METHOD"] === "POST") {
            require_once(dirname(__FILE__) . "/reg_user.php");   // decide $central
        } else {
            $central = "/partials/form_user.php";
            array_push($js, "js/registro.js");
            array_push($css, "css/usuarios.css");
        }
        break;
    case "login_user":  // GET (enlace del menú): formulario; POST (envío): se procesa
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            require_once(dirname(__FILE__) . "/login_user.php");   // decide $central
            break;
        }
        // GET: sigue en "pedir_login" para mostrar el formulario
    case "pedir_login": // formulario de login (también cuando falta la sesión)
        $central = "/partials/form_login.php";
        array_push($js, "js/login.js");
        array_push($css, "css/usuarios.css");
        break;
    case "exit":        // cerrar sesión (B3 Pas_11)
        $_SESSION = [];                          // vacía las variables: el menú ya sale sin usuario
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 3600,   // caducada: el navegador borra la cookie
                  $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        session_destroy();                       // borra la sesión del servidor
        $exito_msg = "Has cerrado la sesión";
        $central = "/partials/home.php";
        break;
    case "listado_clientes":   // solo gestor; la tabla la rellena js/clientes.js con fetch
        $central = "/partials/listado_clientes.php";
        array_push($js, "js/clientes.js");
        array_push($css, "css/usuarios.css");
        break;
    default:
        array_push($error_msg, "Opción no correcta");
        $central = "/partials/home.php";
        break;
}

// La página principal sale desde varias acciones (home, exit, errores...):
// su hoja de estilos se añade aquí una sola vez
if ($central == "/partials/home.php") {
    array_push($css, "css/home.css");
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

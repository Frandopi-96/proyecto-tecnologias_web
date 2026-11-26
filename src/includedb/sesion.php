<?php
/**
 * Sesión del portal y funciones de rol (Tema 2, apartado 8, y B3 Pas_7 y Pas_10).
 *
 * - Nombre de sesión propio (PORTALCULTURAL) para no mezclarla con otras
 *   aplicaciones del mismo servidor.
 * - La sesión caduca tras 20 minutos sin actividad.
 * - Datos que guarda el login: $_SESSION["user"], $_SESSION["rol"] ("client"
 *   o "gestor"), $_SESSION["id"] (id en portal_clientes) y $_SESSION["avatar"]
 *   (ruta del PNG o null).
 *
 * Se incluye ANTES de imprimir nada: session_start() envía una cookie en la
 * cabecera HTTP. Lo incluyen portal.php y todos los ficheros de api/.
 *
 * @author Juan (persona A)
 */

$tiempo_max = 20 * 60;                         // 20 minutos
ini_set('session.gc_maxlifetime', $tiempo_max); // el servidor guarda la sesión al menos 20 min
session_name("PORTALCULTURAL");
session_start();

// Caducidad por inactividad: si han pasado más de 20 min desde la última
// acción, se borra todo y se empieza una sesión vacía (usuario no registrado)
if (isset($_SESSION["ultima_accion"]) && time() - $_SESSION["ultima_accion"] >= $tiempo_max) {
    session_unset();
    session_destroy();
    session_start();
    if (isset($error_msg)) {
        array_push($error_msg, "Tu sesión ha caducado por inactividad");
    }
}
$_SESSION["ultima_accion"] = time();

/** ¿Hay un usuario autenticado? (rol client o gestor) */
function esta_logueado()
{
    return isset($_SESSION["user"]);
}

/** ¿El usuario autenticado es gestor? */
function es_gestor()
{
    return esta_logueado() && $_SESSION["rol"] == "gestor";
}

/**
 * URL del avatar del usuario con sesión, o null si no tiene (lo usan el menú y el perfil).
 * Se le añade ?v=<fecha del fichero> para que, al cambiar el dibujo, el navegador
 * no siga mostrando el de la caché.
 */
function url_avatar()
{
    if (!esta_logueado() || empty($_SESSION["avatar"])) {
        return null;
    }
    $fichero = dirname(__FILE__) . "/../" . $_SESSION["avatar"];
    if (!is_file($fichero)) {
        return null;   // la ruta está en la BD pero el PNG se ha borrado
    }
    return $_SESSION["avatar"] . "?v=" . filemtime($fichero);
}

/**
 * Control de acceso por niveles (Tema 2: autorización basada en roles, RBAC).
 * Lo usan el menú, $permisos de portal.php y los ficheros de api/:
 *   "todos"    -> cualquiera
 *   "anonimo"  -> solo sin sesión (registrarse, iniciar sesión)
 *   "logueado" -> con sesión: client o gestor (el gestor puede todo lo del client, B3 Pas_10)
 *   "gestor"   -> solo el gestor
 */
function tiene_acceso($nivel)
{
    switch ($nivel) {
        case "todos":    return true;
        case "anonimo":  return !esta_logueado();
        case "logueado": return esta_logueado();
        case "gestor":   return es_gestor();
        default:         return false;   // nivel mal escrito: mejor denegar
    }
}

/**
 * Para los ficheros de api/: si el usuario no tiene el nivel pedido, responde
 * 403 con {"error": "..."} en JSON y termina. El JS lo detecta con response.ok (Tema 4).
 */
function api_exigir_acceso($nivel)
{
    if (!tiene_acceso($nivel)) {
        http_response_code(403);
        header("Content-Type: application/json; charset=UTF-8");
        $mensaje = esta_logueado() ? "No tienes permiso para esta consulta" : "Debes iniciar sesión";
        echo json_encode(["error" => $mensaje]);
        exit;
    }
}

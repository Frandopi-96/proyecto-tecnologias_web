<?php
/**
 * Sesión del portal y funciones de rol (Tema 2, apartado 8, y B3 Pas_7 y Pas_10).
 *
 * - Nombre de sesión propio (PORTALCULTURAL) para no mezclarla con otras
 *   aplicaciones del mismo servidor.
 * - La sesión caduca tras 20 minutos sin actividad.
 * - Datos que guarda el login: $_SESSION["user"], $_SESSION["rol"] ("client"
 *   o "gestor") y $_SESSION["id"] (id en portal_clientes).
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

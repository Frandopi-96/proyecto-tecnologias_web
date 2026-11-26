<?php
/**
 * API: guarda el avatar dibujado en el Canvas del perfil (como foto_save.php de B4).
 *
 * Petición: POST con FormData (la hace js/perfil.js con fetch). El dibujo llega
 * como fichero PNG en el campo "avatar", así que PHP lo recibe en $_FILES.
 * Respuesta 200: {"avatar": "media/avatares/<id>.png", "mensaje": "..."}
 * Errores (con {"error": "..."}):
 *   403 sin sesión (api_exigir_acceso)
 *   405 si no es POST
 *   400 si no llega un PNG válido
 *   500 si no se puede guardar el fichero o la ruta en la BD
 *
 * El fichero se llama con el id del cliente: cada uno solo tiene un avatar y,
 * al guardar otro, se sustituye. La ruta se guarda en portal_clientes.avatar
 * y en $_SESSION["avatar"] (para el menú).
 *
 * @author Juan (persona A)
 */

$es_api = true;   // si falla la conexión, conexion.php también responde en JSON
require_once(dirname(__FILE__) . "/../includedb/sesion.php");
require_once(dirname(__FILE__) . "/../includedb/conexion.php");

api_exigir_acceso("logueado");   // client o gestor; sin sesión: 403 y termina

header("Content-Type: application/json; charset=UTF-8");

/** Responde con un código de error y {"error": $mensaje} y termina */
function responder_error($codigo, $mensaje)
{
    http_response_code($codigo);
    echo json_encode(["error" => $mensaje]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder_error(405, "El avatar se envía con POST");
}

// 1. Comprobar el fichero (Tema 2: subida de ficheros, como B3 y B4)
if (!isset($_FILES["avatar"])) {
    responder_error(400, "No ha llegado ningún avatar");
}
$fichero = $_FILES["avatar"];
if ($fichero["error"] !== UPLOAD_ERR_OK) {
    responder_error(400, "Error al subir el avatar (código " . $fichero["error"] . ")");
}
if ($fichero["size"] > 500 * 1024) {
    responder_error(400, "El avatar ocupa demasiado (máximo 500 KB)");
}
// No nos fiamos del nombre ni del tipo que manda el navegador: getimagesize
// lee el propio fichero y dice si de verdad es una imagen PNG
$info = getimagesize($fichero["tmp_name"]);
if ($info === false || $info[2] !== IMAGETYPE_PNG) {
    responder_error(400, "El avatar tiene que ser una imagen PNG");
}

// 2. Guardarlo como media/avatares/<id>.png (sustituye al anterior)
$id = (int) $_SESSION["id"];
$ruta = "media/avatares/" . $id . ".png";               // la que se guarda en la BD
$carpeta = dirname(__FILE__) . "/../media/avatares";
if (!is_dir($carpeta) && !mkdir($carpeta, 0777, true)) {
    responder_error(500, "No se ha podido crear la carpeta de avatares");
}
if (!move_uploaded_file($fichero["tmp_name"], $carpeta . "/" . $id . ".png")) {
    responder_error(500, "No se ha podido guardar el avatar");
}

// 3. Guardar la ruta en la BD y en la sesión
try {
    $consulta = $pdo->prepare("UPDATE portal_clientes SET avatar = ? WHERE id = ?");
    $consulta->execute([$ruta, $id]);
} catch (PDOException $e) {
    responder_error(500, "No se ha podido guardar el avatar en tu cuenta");
}
$_SESSION["avatar"] = $ruta;

echo json_encode(["avatar" => $ruta, "mensaje" => "Avatar guardado"]);

<?php
/**
 * API: listado de clientes del portal (solo gestor). Tema 4: petición asíncrona.
 *
 * Petición: GET api/clientes.php (la hace js/clientes.js con fetch).
 * Respuesta 200: [{"login": "...", "email": "...", "rol": "client", "suscrito": true}, ...]
 * Errores (con {"error": "..."}):
 *   403 si no hay sesión o el usuario no es gestor (api_exigir_acceso)
 *   500 si falla la base de datos
 *
 * La contraseña (hash) no se envía nunca: solo las columnas que se muestran.
 *
 * @author Juan (persona A)
 */

$es_api = true;   // si falla la conexión, conexion.php también responde en JSON
require_once(dirname(__FILE__) . "/../includedb/sesion.php");
require_once(dirname(__FILE__) . "/../includedb/conexion.php");

api_exigir_acceso("gestor");   // sin permiso: 403 + {"error": ...} y termina

header("Content-Type: application/json; charset=UTF-8");

try {
    $consulta = $pdo->prepare("SELECT login, email, rol, suscrito FROM portal_clientes ORDER BY login");
    $consulta->execute();
    $clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "No se ha podido leer la lista de clientes"]);
    exit;
}

// suscrito llega de MySQL como "0"/"1": se envía como true/false
foreach ($clientes as $i => $cliente) {
    $clientes[$i]["suscrito"] = ($cliente["suscrito"] == 1);
}

echo json_encode($clientes);

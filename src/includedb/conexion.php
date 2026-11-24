<?php
/**
 * Conexión a la base de datos con PDO (Tema 2, apartado 9).
 *
 * Crea el objeto $pdo conectado a la BD de WordPress (wp_alxxxx), donde están
 * nuestras tablas portal_... Los datos de conexión están en gestion_BD.php
 * (cada uno el suyo, no se sube a git; plantilla en gestion_BD.example.php).
 *
 * Uso: require_once(dirname(__FILE__) . "/includedb/conexion.php");
 * Los ficheros de api/ ponen antes $es_api = true; para que el error se
 * devuelva en JSON en lugar de en HTML.
 *
 * @author Juan (persona A)
 */

require_once(dirname(__FILE__) . "/gestion_BD.php");

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                   DB_USER, DB_PASSWORD);
    // Con ERRMODE_EXCEPTION cualquier fallo de SQL lanza una PDOException
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // No mostramos $e->getMessage() al usuario: puede contener datos de la BD
    http_response_code(500);
    if (isset($es_api) && $es_api) {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["error" => "Error al acceder a la base de datos"]);
    } else {
        echo "<p>Error al acceder a la base de datos.</p>";
    }
    exit;
}

<?php
/**
 * Procesa el inicio de sesión (POST a portal.php?action=login_user), como B3 Pas_9.
 *
 * Se incluye desde portal.php (ya existen $pdo, $error_msg, $exito_msg):
 *   - Busca el login en portal_clientes (consulta preparada) y comprueba la
 *     contraseña con password_verify() contra el hash guardado en el registro.
 *   - Correcto: guarda en la sesión user, rol e id, y muestra la página principal
 *     (el menú ya sale con las opciones de su rol).
 *   - Incorrecto: "Autentificación no correcta" y se vuelve a mostrar el formulario.
 *     El mensaje es el mismo si el usuario no existe o si falla la contraseña,
 *     para no dar pistas de qué usuarios hay.
 *
 * @author Juan (persona A)
 */

// Si se abre este fichero directamente por URL, se manda al portal (Tema 2: redirección)
if (!isset($error_msg)) {
    header("Location: portal.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$pwd   = $_POST["pwd"] ?? "";

$cliente = false;
try {
    $consulta = $pdo->prepare("SELECT id, login, password, rol FROM portal_clientes WHERE login = ?");
    $consulta->execute([$login]);
    $cliente = $consulta->fetch(PDO::FETCH_ASSOC);   // false si no existe
} catch (PDOException $e) {
    array_push($error_msg, "No se ha podido iniciar sesión. Inténtalo más tarde");
}

if ($cliente !== false && password_verify($pwd, $cliente["password"])) {
    // Nuevo identificador de sesión al autenticarse: si alguien conocía el
    // anterior (fijación de sesión), ya no le sirve
    session_regenerate_id(true);
    $_SESSION["user"] = $cliente["login"];
    $_SESSION["rol"]  = $cliente["rol"];
    $_SESSION["id"]   = $cliente["id"];
    $exito_msg = "Bienvenido, " . $cliente["login"];
    $central = "/partials/home.php";
} else {
    if (count($error_msg) == 0) {
        array_push($error_msg, "Autentificación no correcta");
    }
    $central = "/partials/form_login.php";
    array_push($js, "js/login.js");
    array_push($css, "css/usuarios.css");
}

<?php
/**
 * Procesa el registro de clientes (portal.php?action=reg_user), como B3 Pas_8.
 *
 * Se incluye desde portal.php (ya existen $pdo, $error_msg, $exito_msg):
 *   - Valida en el servidor: login 7-30 caracteres (letras, números, _ . -) y no
 *     repetido; email válido; contraseña de 7 o más con al menos 1 número; las
 *     dos contraseñas iguales.
 *   - Guarda en portal_clientes con la contraseña cifrada (password_hash) y rol "client".
 *   - Éxito: mensaje y página principal. Error: mensajes en $error_msg y se
 *     vuelve a mostrar el formulario con los datos escritos.
 *
 * @author Juan (persona A)
 */

// Si se abre este fichero directamente por URL, se manda al portal (Tema 2: redirección)
if (!isset($error_msg)) {
    header("Location: portal.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$email = trim($_POST["email"] ?? "");
$pwd   = $_POST["pwd"] ?? "";    // las contraseñas no se recortan: los espacios cuentan
$pwd2  = $_POST["pwd2"] ?? "";

// Validación en el servidor (aunque ya la haga el navegador)
if (!preg_match("/^[A-Za-z0-9_.\-]{7,30}$/", $login)) {
    array_push($error_msg, "El usuario debe tener entre 7 y 30 caracteres (letras, números, _ . -)");
}
if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    array_push($error_msg, "El correo electrónico no es válido");
}
if (strlen($pwd) < 7 || !preg_match("/\d/", $pwd)) {
    array_push($error_msg, "La contraseña debe tener al menos 7 caracteres y 1 número");
}
if ($pwd !== $pwd2) {
    array_push($error_msg, "Las contraseñas no coinciden");
}

try {
    // Login único: se busca en la tabla (consulta preparada, evita la inyección SQL)
    if (count($error_msg) == 0) {
        $consulta = $pdo->prepare("SELECT COUNT(*) FROM portal_clientes WHERE login = ?");
        $consulta->execute([$login]);
        if ($consulta->fetchColumn() > 0) {
            array_push($error_msg, "El usuario \"$login\" ya existe");
        }
    }

    if (count($error_msg) == 0) {
        $consulta = $pdo->prepare(
            "INSERT INTO portal_clientes (login, email, password, rol) VALUES (?, ?, ?, 'client')");
        $consulta->execute([$login, $email, password_hash($pwd, PASSWORD_DEFAULT)]);
    }
} catch (PDOException $e) {
    array_push($error_msg, "No se ha podido completar el registro. Inténtalo más tarde");
}

if (count($error_msg) == 0) {
    $exito_msg = "Registro completado, $login. Ya puedes iniciar sesión";
    // Como en B3: tras registrarse se muestra el login con el usuario ya escrito
    $_POST = ["login" => $login];
    $central = "/partials/form_login.php";
    array_push($js, "js/login.js");
    array_push($css, "css/usuarios.css");
} else {
    $central = "/partials/form_user.php";
    array_push($js, "js/registro.js");
    array_push($css, "css/usuarios.css");
}

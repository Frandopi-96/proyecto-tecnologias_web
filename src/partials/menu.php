<?php
/**
 * Menú de navegación según el rol (B3 Pas_10).
 *
 * Cada opción indica para quién se muestra con los niveles de tiene_acceso()
 * (includedb/sesion.php): "todos", "anonimo", "logueado" (client y gestor) o "gestor".
 * Para añadir vuestra página: una línea en el bloque que toque. Si no es para
 * "todos", poned el mismo nivel en $permisos de portal.php: ocultar el enlace
 * no basta, el servidor también lo comprueba.
 *
 * La opción de la página actual lleva aria-current="page" (accesibilidad).
 * Con sesión se muestra el avatar del usuario (si tiene) junto a su nombre; es
 * decorativo (alt vacío) porque al lado ya pone quién es.
 *
 * @author Juan (persona A)
 */

$opciones_menu = [
    // Todos
    ["action" => "home",       "texto" => "Inicio",          "nivel" => "todos"],

    // Sin sesión
    ["action" => "form_user",  "texto" => "Registrarse",     "nivel" => "anonimo"],
    ["action" => "login_user", "texto" => "Iniciar sesión",  "nivel" => "anonimo"],

    // Cliente (y gestor)

    // Solo gestor
    ["action" => "listado_clientes", "texto" => "Clientes", "nivel" => "gestor"],

    // Con sesión, al final
    ["action" => "perfil",     "texto" => "Mi perfil",       "nivel" => "logueado"],
    ["action" => "exit",       "texto" => "Salir",           "nivel" => "logueado"],
];
?>
<nav aria-label="Menú principal">
    <ul>
<?php foreach ($opciones_menu as $opcion): ?>
<?php if (tiene_acceso($opcion["nivel"])): ?>
        <li><a href="portal.php?action=<?php print($opcion["action"]); ?>"<?php
            if ($opcion["action"] == $action) print(' aria-current="page"'); ?>><?php
            print(htmlspecialchars($opcion["texto"])); ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
<?php if (esta_logueado()): ?>
<?php $avatar_menu = url_avatar(); ?>
        <li class="bienvenida"><?php if ($avatar_menu !== null): ?><img src="<?php
            print(htmlspecialchars($avatar_menu)); ?>" width="32" height="32" alt=""> <?php
            endif; ?>Bienvenido <strong><?php print(htmlspecialchars($_SESSION["user"])); ?></strong>
            (<?php print($_SESSION["rol"] == "gestor" ? "gestor" : "cliente"); ?>)</li>
<?php endif; ?>
    </ul>
</nav>

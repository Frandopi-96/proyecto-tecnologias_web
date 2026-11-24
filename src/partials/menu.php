<?php
/**
 * Menú de navegación según el rol (B3 Pas_10).
 *   - Todos: opciones comunes.
 *   - Con sesión (client o gestor): opciones de cliente y saludo.
 *   - Gestor: además, las opciones de gestión.
 * Cada persona añade aquí el enlace de su página en el bloque que toque.
 * Los permisos se comprueban también en portal.php ($permisos).
 *
 * @author Juan (persona A)
 */
?>
<nav aria-label="Menú principal">
    <ul>
        <!-- Todos los usuarios -->
        <li><a href="portal.php?action=home">Inicio</a></li>

<?php if (!esta_logueado()): ?>
        <!-- Solo sin sesión -->
        <li><a href="portal.php?action=form_user">Registrarse</a></li>
        <li><a href="portal.php?action=login_user">Iniciar sesión</a></li>
<?php endif; ?>

<?php if (esta_logueado()): ?>
        <!-- Cliente (y gestor) -->
<?php endif; ?>

<?php if (es_gestor()): ?>
        <!-- Solo gestor -->
<?php endif; ?>

<?php if (esta_logueado()): ?>
        <li><a href="portal.php?action=exit">Salir</a></li>
        <li class="bienvenida">Bienvenido <?php print(htmlspecialchars($_SESSION["user"])); ?></li>
<?php endif; ?>
    </ul>
</nav>

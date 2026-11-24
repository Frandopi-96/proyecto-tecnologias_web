<?php
/**
 * Muestra los mensajes acumulados en $error_msg (B3 Pas_1).
 * portal.php lo incluye después del menú y antes del <main> si hay errores.
 * role="alert" hace que los lectores de pantalla lo anuncien.
 *
 * @author Juan (persona A)
 */
?>
<aside class="error" role="alert">
    <ul>
<?php foreach ($error_msg as $msg): ?>
        <li><?php print(htmlspecialchars($msg)); ?></li>
<?php endforeach; ?>
    </ul>
</aside>

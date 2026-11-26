<?php
/**
 * Mi perfil (usuarios con sesión): personalización y avatar.
 *
 * 1. Tema claro u oscuro: formulario POST a portal.php?action=guardar_tema,
 *    que lo guarda en la cookie "tema" con setcookie (Tema 2).
 * 2. Avatar: se dibuja en un <canvas> con el ratón (B4) y js/perfil.js lo envía
 *    a api/guardar_avatar.php con fetch + FormData (Tema 4).
 *
 * @author Juan (persona A)
 */

$tema_actual = (isset($_COOKIE["tema"]) && $_COOKIE["tema"] === "oscuro") ? "oscuro" : "claro";
$avatar = url_avatar();   // null si todavía no tiene
?>
<section class="perfil" aria-labelledby="titulo-perfil">
    <h2 id="titulo-perfil">Mi perfil</h2>

    <section class="perfil-tema" aria-labelledby="titulo-tema">
        <h3 id="titulo-tema">Apariencia del portal</h3>
        <form action="portal.php?action=guardar_tema" method="POST">
            <fieldset>
                <legend>Tema de colores</legend>
                <p>
                    <input type="radio" id="tema-claro" name="tema" value="claro"
                        <?php if ($tema_actual == "claro") print("checked"); ?>>
                    <label for="tema-claro">Claro</label>
                </p>
                <p>
                    <input type="radio" id="tema-oscuro" name="tema" value="oscuro"
                        <?php if ($tema_actual == "oscuro") print("checked"); ?>>
                    <label for="tema-oscuro">Oscuro</label>
                </p>
            </fieldset>
            <p><small>Se guarda en este navegador durante 30 días.</small></p>
            <p><input type="submit" value="Guardar tema"></p>
        </form>
    </section>

    <section class="perfil-avatar" aria-labelledby="titulo-avatar">
        <h3 id="titulo-avatar">Tu avatar</h3>
        <p>Dibuja tu avatar con el ratón: mantén pulsado el botón y mueve el ratón sobre el recuadro.</p>

        <div class="zona-avatar">
            <canvas id="lienzo-avatar" width="200" height="200"
                    aria-label="Lienzo para dibujar el avatar con el ratón">
                Tu navegador no permite dibujar en un canvas.
            </canvas>

            <div class="controles-avatar">
                <p>
                    <label for="color-pincel">Color</label>
                    <input type="color" id="color-pincel" value="#7a1f2b">
                </p>
                <p>
                    <label for="grosor-pincel">Grosor</label>
                    <input type="range" id="grosor-pincel" min="2" max="20" value="6">
                </p>
                <p class="botones">
                    <button type="button" id="limpiar-avatar">Limpiar</button>
                    <button type="button" id="guardar-avatar">Guardar avatar</button>
                </p>
                <p id="estado-avatar" role="status" aria-live="polite"></p>
            </div>

            <figure class="avatar-actual">
                <img id="avatar-guardado" width="100" height="100"
                     src="<?php print(htmlspecialchars($avatar ?? "")); ?>"
                     alt="Tu avatar guardado"<?php if ($avatar === null) print(" hidden"); ?>>
                <figcaption id="sin-avatar"><?php
                    print($avatar === null ? "Todavía no has guardado ningún avatar" : "Avatar guardado"); ?></figcaption>
            </figure>
        </div>
    </section>
</section>

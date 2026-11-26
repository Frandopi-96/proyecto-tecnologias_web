<?php
/**
 * Mi perfil (usuarios con sesión): personalización y avatar.
 *
 * 1. Apariencia (tema claro u oscuro y tamaño del texto), como B4 Pas 11: el
 *    formulario NO se envía al servidor; js/personalizar.js lo guarda en
 *    localStorage para este usuario y lo aplica en todas las páginas.
 * 2. Avatar: se dibuja en un <canvas> con el ratón (B4) y js/perfil.js lo envía
 *    a api/guardar_avatar.php con fetch + FormData (Tema 4).
 *
 * @author Juan (persona A)
 */

$avatar = url_avatar();   // null si todavía no tiene
?>
<section class="perfil" aria-labelledby="titulo-perfil">
    <h2 id="titulo-perfil">Mi perfil</h2>

    <section class="perfil-tema" aria-labelledby="titulo-tema">
        <h3 id="titulo-tema">Apariencia del portal</h3>
        <!-- Sin action ni method: lo gestiona js/personalizar.js (localStorage) -->
        <form id="form-personalizar">
            <fieldset>
                <legend>Tema de colores</legend>
                <p>
                    <input type="radio" id="tema-claro" name="tema" value="claro" checked>
                    <label for="tema-claro">Claro</label>
                </p>
                <p>
                    <input type="radio" id="tema-oscuro" name="tema" value="oscuro">
                    <label for="tema-oscuro">Oscuro</label>
                </p>
            </fieldset>
            <p>
                <label for="tamano-texto">Tamaño del texto</label>
                <select id="tamano-texto" name="tamano">
                    <option value="90%">Pequeño</option>
                    <option value="100%" selected>Normal</option>
                    <option value="115%">Grande</option>
                    <option value="130%">Muy grande</option>
                </select>
            </p>
            <p><small>Al cambiar las opciones ves cómo queda. Se guarda en este navegador
                solo para tu usuario.</small></p>
            <p class="botones">
                <input type="submit" value="Guardar">
                <button type="button" id="restablecer-personalizar">Restablecer</button>
            </p>
            <p id="mensaje-personalizar" role="status" aria-live="polite"></p>
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

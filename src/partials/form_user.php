<?php
/**
 * Formulario de registro de clientes (portal.php?action=form_user), como B3 Pas_8.
 * Envía los datos por POST a portal.php?action=reg_user.
 *
 * - Atributos HTML (required, minlength, pattern, type="email") para validar
 *   aunque no haya JavaScript (Tema 1).
 * - js/registro.js valida con eventos del DOM y escribe el mensaje en el
 *   <span class="error-campo"> de cada campo (Tema 3).
 * - Si PHP encuentra errores, se vuelve a mostrar con los valores escritos
 *   (menos las contraseñas), escapados con htmlspecialchars (Tema 2).
 *
 * @author Juan (persona A)
 */
?>
<section class="form-usuario">
    <h2>Registro de clientes</h2>
    <p>Los campos marcados con * son obligatorios.</p>

    <form id="form_registro" action="portal.php?action=reg_user" method="POST">
        <p>
            <label for="login">Usuario *</label>
            <input type="text" id="login" name="login" required minlength="7" maxlength="30"
                   pattern="[A-Za-z0-9_.\-]{7,30}" autocomplete="username"
                   aria-describedby="ayuda-login error-login"
                   value="<?php print(htmlspecialchars($_POST["login"] ?? "")); ?>">
            <small id="ayuda-login">Entre 7 y 30 caracteres: letras, números, _ . -</small>
            <span class="error-campo" id="error-login" aria-live="polite"></span>
        </p>
        <p>
            <label for="email">Correo electrónico *</label>
            <input type="email" id="email" name="email" required maxlength="100" autocomplete="email"
                   aria-describedby="error-email"
                   value="<?php print(htmlspecialchars($_POST["email"] ?? "")); ?>">
            <span class="error-campo" id="error-email" aria-live="polite"></span>
        </p>
        <p>
            <label for="pwd">Contraseña *</label>
            <input type="password" id="pwd" name="pwd" required minlength="7"
                   pattern="(?=.*\d).{7,}" autocomplete="new-password"
                   aria-describedby="ayuda-pwd error-pwd">
            <small id="ayuda-pwd">Mínimo 7 caracteres y al menos 1 número</small>
            <span class="error-campo" id="error-pwd" aria-live="polite"></span>
        </p>
        <p>
            <label for="pwd2">Repetir contraseña *</label>
            <input type="password" id="pwd2" name="pwd2" required autocomplete="new-password"
                   aria-describedby="error-pwd2">
            <span class="error-campo" id="error-pwd2" aria-live="polite"></span>
        </p>
        <p class="botones">
            <input type="submit" value="Registrarse">
            <input type="reset" value="Deshacer">
        </p>
    </form>
</section>

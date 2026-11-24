<?php
/**
 * Formulario de inicio de sesión (portal.php?action=login_user), como B3 Pas_9.
 * Se envía por POST a la misma acción; lo procesa login_user.php.
 * js/login.js comprueba en el navegador que los campos no estén vacíos.
 *
 * @author Juan (persona A)
 */
?>
<section class="form-usuario">
    <h2>Iniciar sesión</h2>

    <form id="form_login" action="portal.php?action=login_user" method="POST">
        <p>
            <label for="login">Usuario</label>
            <input type="text" id="login" name="login" required maxlength="30" autocomplete="username"
                   aria-describedby="error-login"
                   value="<?php print(htmlspecialchars($_POST["login"] ?? "")); ?>">
            <span class="error-campo" id="error-login" aria-live="polite"></span>
        </p>
        <p>
            <label for="pwd">Contraseña</label>
            <input type="password" id="pwd" name="pwd" required autocomplete="current-password"
                   aria-describedby="error-pwd">
            <span class="error-campo" id="error-pwd" aria-live="polite"></span>
        </p>
        <p class="botones">
            <input type="submit" value="Entrar">
        </p>
    </form>
    <p>¿No tienes cuenta? <a href="portal.php?action=form_user">Regístrate</a></p>
</section>

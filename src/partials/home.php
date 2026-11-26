<?php
/**
 * Página principal del portal (Tema 1, con un bloque dinámico del Tema 2).
 *
 * - Presentación del portal, próximos actos destacados y cómo comprar entradas.
 * - HTML semántico: <section> por bloque, <article> por acto, <time datetime>
 *   para las fechas y <dl> para los datos del acto.
 * - Accesible: un único <h1> (en header.php) y después <h2>/<h3> en orden;
 *   cada imagen con su texto alternativo; cada sección con aria-labelledby.
 * - Los destacados son los 3 próximos actos de portal_actos: los consulta
 *   portal.php con PDO y llegan en $destacados. El listado completo de actos,
 *   con búsqueda por fecha y lugar, es de la persona B.
 * - Estilos en css/home.css (los añade portal.php cuando se muestra esta página).
 *
 * @author Juan (persona A)
 */

$meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio",
          "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
?>
<section class="presentacion" aria-labelledby="titulo-presentacion">
    <h2 id="titulo-presentacion">Bienvenido al portal de actos culturales</h2>
    <p>Conciertos, teatro, danza y exposiciones de Castelló en un solo sitio.
        Busca los actos por fecha o por lugar, consulta el precio y las plazas
        y compra tus entradas desde casa.</p>
<?php if (!esta_logueado()): ?>
    <p class="llamada">
        <a href="portal.php?action=form_user">Crea tu cuenta</a> o
        <a href="portal.php?action=login_user">inicia sesión</a> para comprar entradas.
    </p>
<?php else: ?>
    <p class="llamada">Hola, <strong><?php print(htmlspecialchars($_SESSION["user"])); ?></strong>:
        ya puedes comprar entradas para cualquier acto.</p>
<?php endif; ?>
</section>

<section class="destacados" aria-labelledby="titulo-destacados">
    <h2 id="titulo-destacados">Próximos actos destacados</h2>
<?php if (count($destacados) == 0): ?>
    <p>Pronto anunciaremos nuevos actos.</p>
<?php else: ?>
    <div class="lista-actos">
<?php foreach ($destacados as $acto):
    $momento = strtotime($acto["fecha"]);
    // Sin imagen propia se usa una genérica
    $imagen = $acto["imagen"] ? $acto["imagen"] : "media/home/generico.svg";
?>
        <article class="acto">
            <img src="<?php print(htmlspecialchars($imagen)); ?>" width="320" height="180"
                 alt="<?php print(htmlspecialchars("Imagen del acto " . $acto["titulo"])); ?>">
            <h3><?php print(htmlspecialchars($acto["titulo"])); ?></h3>
            <dl>
                <dt>Fecha</dt>
                <dd><time datetime="<?php print(date("Y-m-d\TH:i", $momento)); ?>"><?php
                    print(date("j", $momento) . " de " . $meses[date("n", $momento) - 1]
                          . " de " . date("Y, H:i", $momento) . " h"); ?></time></dd>
                <dt>Lugar</dt>
                <dd><?php print(htmlspecialchars($acto["lugar"])); ?></dd>
                <dt>Precio</dt>
                <dd><?php print($acto["precio"] > 0
                        ? number_format($acto["precio"], 2, ",", ".") . " €"
                        : "Entrada libre"); ?></dd>
            </dl>
        </article>
<?php endforeach; ?>
    </div>
<?php endif; ?>
</section>

<section class="pasos" aria-labelledby="titulo-pasos">
    <h2 id="titulo-pasos">Cómo comprar tus entradas</h2>
    <ol>
        <li><strong>Regístrate</strong> con un nombre de usuario, tu correo y una contraseña.</li>
        <li><strong>Busca el acto</strong> que te interesa por fecha o por lugar.</li>
        <li><strong>Elige cuántas entradas</strong> quieres y firma la compra.</li>
        <li><strong>Consulta tus entradas</strong> cuando quieras desde tu cuenta.</li>
    </ol>
</section>

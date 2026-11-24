<?php
/**
 * Página principal del portal (página estática, Tema 1).
 *
 * - Presentación del portal, próximos actos destacados y cómo comprar entradas.
 * - HTML semántico: <section> por bloque, <article> por acto, <figure> con
 *   <figcaption>, <time datetime> para las fechas y <dl> para los datos del acto.
 * - Accesible: un único <h1> (en header.php) y después <h2>/<h3> en orden;
 *   cada imagen con su texto alternativo; cada sección con aria-labelledby.
 * - Los actos destacados son fijos (página estática). El listado real de actos
 *   con búsqueda por fecha y lugar es de la persona B.
 * - Estilos en css/home.css (los añade portal.php cuando se muestra esta página).
 *
 * @author Juan (persona A)
 */
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
    <div class="lista-actos">
        <article class="acto">
            <figure>
                <img src="media/home/concierto.svg" width="320" height="180"
                     alt="Ilustración de un escenario con un piano de cola y un atril bajo un foco">
                <figcaption>Música clásica</figcaption>
            </figure>
            <h3>Concierto de Navidad</h3>
            <dl>
                <dt>Fecha</dt>
                <dd><time datetime="2026-12-19T19:00">19 de diciembre de 2026, 19:00 h</time></dd>
                <dt>Lugar</dt>
                <dd>Auditori i Palau de Congressos de Castelló</dd>
                <dt>Precio</dt>
                <dd>15,00 €</dd>
            </dl>
        </article>

        <article class="acto">
            <figure>
                <img src="media/home/teatro.svg" width="320" height="180"
                     alt="Ilustración de un telón rojo abierto con las máscaras del teatro">
                <figcaption>Teatro</figcaption>
            </figure>
            <h3>La casa de Bernarda Alba</h3>
            <dl>
                <dt>Fecha</dt>
                <dd><time datetime="2027-01-16T20:30">16 de enero de 2027, 20:30 h</time></dd>
                <dt>Lugar</dt>
                <dd>Teatre Principal de Castelló</dd>
                <dt>Precio</dt>
                <dd>12,00 €</dd>
            </dl>
        </article>

        <article class="acto">
            <figure>
                <img src="media/home/exposicion.svg" width="320" height="180"
                     alt="Ilustración de una sala de exposiciones con tres cuadros colgados en la pared">
                <figcaption>Exposición</figcaption>
            </figure>
            <h3>Fotografía de la Mediterrània</h3>
            <dl>
                <dt>Fecha</dt>
                <dd><time datetime="2027-02-05T11:00">Desde el 5 de febrero de 2027</time></dd>
                <dt>Lugar</dt>
                <dd>Paranimf de la Universitat Jaume I</dd>
                <dt>Precio</dt>
                <dd>Entrada libre</dd>
            </dl>
        </article>
    </div>
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

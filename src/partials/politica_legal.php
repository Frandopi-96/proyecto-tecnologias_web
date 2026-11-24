<?php
/**
 * Página de política legal: aviso legal y protección de datos (página estática, Tema 1).
 * Como lgpd.php de B2, pero sin librerías externas y con los datos que guarda ESTE portal
 * (tablas de docs/BASE_DE_DATOS.md).
 *
 * - HTML semántico: <section> por bloque con aria-labelledby, índice en <nav>,
 *   <dl> para los datos del titular, <table> con <caption> y scope para los
 *   tratamientos de datos, <address> para el contacto y <time datetime> para la fecha.
 * - Accesible: un único <h1> (en header.php) y después <h2>/<h3> en orden.
 * - Sin CSS propio: solo css/estilo.css.
 *
 * @author Iker (persona C)
 */
?>
<section class="politica-legal" aria-labelledby="titulo-politica">
    <h2 id="titulo-politica">Política legal</h2>
    <p>Este portal es un <strong>proyecto académico</strong> de la asignatura EI1042 Tecnologías y
        Aplicaciones Web de la Universitat Jaume I. No tiene ánimo de lucro: los actos, las entradas
        y los pagos son ficticios y sirven solo para practicar lo visto en la asignatura.</p>
    <p>Última actualización: <time datetime="2026-11-24">24 de noviembre de 2026</time>.</p>

    <nav aria-label="Índice de la política legal">
        <h3>Índice</h3>
        <ol>
            <li><a href="#aviso-legal">Aviso legal</a></li>
            <li><a href="#proteccion-datos">Protección de datos</a></li>
            <li><a href="#tus-derechos">Tus derechos</a></li>
            <li><a href="#cookies">Cookies</a></li>
        </ol>
    </nav>
</section>

<section aria-labelledby="aviso-legal">
    <h2 id="aviso-legal">1. Aviso legal</h2>

    <h3>Titular del portal</h3>
    <dl>
        <dt>Titular</dt>
        <dd>Grupo del portal de actos culturales, EI1042 (Universitat Jaume I)</dd>
        <dt>Naturaleza</dt>
        <dd>Proyecto académico, sin actividad comercial</dd>
        <dt>Contacto</dt>
        <dd>
            <address>
                <!-- PENDIENTE: poner el correo de contacto que decida el grupo -->
                <a href="mailto:contacto@portalcultural.example">contacto@portalcultural.example</a>
            </address>
        </dd>
    </dl>

    <h3>Condiciones de uso</h3>
    <p>El portal permite consultar actos culturales, buscarlos por fecha o lugar, comprar entradas
        (ficticias) y participar en un juego con ranking. Al usarlo te comprometes a no introducir
        datos falsos de otras personas ni contenidos ofensivos, y a no intentar acceder a las zonas
        reservadas al gestor.</p>

    <h3>Propiedad intelectual</h3>
    <p>Los textos, el diseño y el código del portal son obra del grupo. Las noticias y las imágenes
        de la galería proceden del WordPress del propio proyecto. No se permite reutilizarlos fuera
        del ámbito de la asignatura sin permiso.</p>

    <h3>Enlaces externos</h3>
    <p>El portal puede enlazar a páginas de terceros. No somos responsables de su contenido ni de
        sus políticas de privacidad.</p>

    <h3>Legislación aplicable</h3>
    <p>Este aviso sigue como referencia la Ley 34/2002 de Servicios de la Sociedad de la Información
        (LSSI), el Reglamento General de Protección de Datos (RGPD, Reglamento UE 2016/679) y la
        Ley Orgánica 3/2018 de Protección de Datos (LOPDGDD).</p>
</section>

<section aria-labelledby="proteccion-datos">
    <h2 id="proteccion-datos">2. Protección de datos</h2>

    <h3>Responsable del tratamiento</h3>
    <p>El responsable de tus datos es el titular del portal (ver <a href="#aviso-legal">Aviso legal</a>).
        Puedes escribirnos al correo de contacto para cualquier duda sobre tus datos.</p>

    <h3>Qué datos guardamos y para qué</h3>
    <table>
        <caption>Datos personales que guarda el portal</caption>
        <thead>
            <tr>
                <th scope="col">Cuándo</th>
                <th scope="col">Datos</th>
                <th scope="col">Para qué</th>
                <th scope="col">Base legal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">Registro</th>
                <td>Nombre de usuario, correo y contraseña (se guarda cifrada, nunca en claro)</td>
                <td>Crear tu cuenta e iniciar sesión</td>
                <td>Tu consentimiento (art. 6.1.a RGPD)</td>
            </tr>
            <tr>
                <th scope="row">Compra de entradas</th>
                <td>Acto, número de entradas, fecha de compra y tu firma (imagen)</td>
                <td>Gestionar tus entradas y comprobar el aforo</td>
                <td>Ejecución de la compra (art. 6.1.b RGPD)</td>
            </tr>
            <tr>
                <th scope="row">Suscripción</th>
                <td>Si quieres recibir información por correo</td>
                <td>Enviarte novedades de los actos</td>
                <td>Tu consentimiento; puedes darte de baja cuando quieras</td>
            </tr>
            <tr>
                <th scope="row">Perfil</th>
                <td>Avatar que dibujas (imagen)</td>
                <td>Personalizar tu cuenta</td>
                <td>Tu consentimiento</td>
            </tr>
            <tr>
                <th scope="row">Juego</th>
                <td>Nombre que eliges y puntuación</td>
                <td>Mostrar el ranking</td>
                <td>Tu consentimiento (puedes usar un alias)</td>
            </tr>
        </tbody>
    </table>

    <h3>Cuánto tiempo los guardamos</h3>
    <p>Los datos de tu cuenta se guardan mientras no la borres. Como es un proyecto académico,
        al terminar la asignatura se borra toda la base de datos.</p>

    <h3>A quién se los damos</h3>
    <p>A nadie. Los datos se guardan en el servidor del propio proyecto y no se ceden ni se venden
        a terceros.</p>

    <h3>Cómo los protegemos</h3>
    <p>Las contraseñas se guardan cifradas, las consultas a la base de datos están preparadas para
        evitar inyecciones de código y la sesión se cierra sola tras 20 minutos sin actividad.</p>
</section>

<section aria-labelledby="tus-derechos">
    <h2 id="tus-derechos">3. Tus derechos</h2>
    <p>Puedes ejercer en cualquier momento estos derechos escribiendo al correo de contacto:</p>
    <ul>
        <li><strong>Acceso</strong>: saber qué datos tuyos tenemos.</li>
        <li><strong>Rectificación</strong>: corregir los que estén mal.</li>
        <li><strong>Supresión</strong>: pedir que los borremos.</li>
        <li><strong>Oposición y limitación</strong>: pedir que dejemos de usarlos o que los usemos solo para algunas cosas.</li>
        <li><strong>Portabilidad</strong>: recibirlos en un formato que puedas llevar a otro servicio.</li>
    </ul>
    <p>Si crees que no hemos tratado bien tus datos, puedes reclamar ante la
        <a href="https://www.aepd.es">Agencia Española de Protección de Datos</a>.</p>
</section>

<section aria-labelledby="cookies">
    <h2 id="cookies">4. Cookies</h2>
    <p>El portal usa una cookie de sesión (<code>PORTALCULTURAL</code>) para saber que has iniciado
        sesión y una cookie para recordar tus preferencias. Toda la información está en la
        <!-- PENDIENTE: confirmar con D el nombre de la action de su página de cookies -->
        <a href="portal.php?action=cookies">política de cookies</a>.</p>
</section>

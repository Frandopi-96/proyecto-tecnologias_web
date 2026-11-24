<?php
/**
 * Listado de clientes (solo gestor). La tabla llega vacía y la rellena
 * js/clientes.js con los datos de api/clientes.php (Tema 4: fetch + DOM).
 *
 * - El párrafo #estado-clientes (aria-live) anuncia "Cargando...", la lista
 *   vacía o el error, también a los lectores de pantalla.
 * - La tabla está oculta (hidden) hasta que hay datos que mostrar.
 *
 * @author Juan (persona A)
 */
?>
<section class="listado-clientes" aria-labelledby="titulo-clientes">
    <h2 id="titulo-clientes">Clientes registrados</h2>

    <p><button type="button" id="actualizar-clientes">Actualizar lista</button></p>
    <p id="estado-clientes" role="status" aria-live="polite">Cargando clientes...</p>

    <table id="tabla-clientes" hidden>
        <caption>Clientes del portal ordenados por nombre de usuario</caption>
        <thead>
            <tr>
                <th scope="col">Usuario</th>
                <th scope="col">Correo electrónico</th>
                <th scope="col">Rol</th>
                <th scope="col">Suscrito</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</section>

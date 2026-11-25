<?php
/**
 * Instalación del portal: crea las tablas portal_... con PDO e inserta actos de ejemplo
 * (como crearTabla de gestion_BD.php, Tema 2 apartado 9).
 *
 * Se abre una vez en el navegador: src/instalar.php
 *   1. Crea las 4 tablas en la BD de WordPress (wp_alxxxx) con CREATE TABLE IF NOT EXISTS:
 *      portal_clientes, portal_actos, portal_entradas y portal_puntuaciones
 *      (esquema en docs/BASE_DE_DATOS.md, apartado 3).
 *   2. Si portal_actos está vacía, inserta actos de ejemplo.
 *   Ejecutarlo otra vez no da error ni duplica nada: las tablas que ya existen no se
 *   tocan y los actos solo se insertan si no hay ninguno.
 *
 * Permisos: la primera vez lo puede abrir cualquiera (todavía no existe portal_clientes,
 * así que no puede haber ningún gestor). Una vez instalado, solo el gestor.
 *
 * La BD no se crea aquí: es la de WordPress y se configura en includedb/gestion_BD.php.
 *
 * @author Jeremy (persona B)
 */

$error_msg = [];   // mensajes de error (los muestra partials/error.php)
$exito_msg = "";   // mensaje de éxito
$pasos = [];       // qué ha hecho la instalación, para mostrarlo en la página
$css = [];         // header.php los recorre: esta página no añade hojas ni scripts propios
$js  = [];

// Sesión y conexión antes de imprimir nada (igual que portal.php)
require_once(dirname(__FILE__) . "/includedb/sesion.php");
require_once(dirname(__FILE__) . "/includedb/conexion.php");

/**
 * ¿Existe la tabla $nombre en la BD a la que estamos conectados?
 * Consulta information_schema con prepare + execute.
 */
function existe_tabla($pdo, $nombre)
{
    $consulta = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables
                               WHERE table_schema = DATABASE() AND table_name = ?");
    $consulta->execute([$nombre]);
    return $consulta->fetchColumn() > 0;
}

// Tablas en orden: portal_entradas apunta a portal_actos y portal_clientes
// (claves foráneas), así que esas dos tienen que crearse antes.
$tablas = [
    "portal_clientes" => "CREATE TABLE IF NOT EXISTS portal_clientes (
        id       INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        login    VARCHAR(30)  NOT NULL UNIQUE,
        email    VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,              -- resultado de password_hash()
        rol      VARCHAR(10)  NOT NULL DEFAULT 'client',  -- 'client' o 'gestor'
        suscrito TINYINT(1)   NOT NULL DEFAULT 0,    -- suscripción al correo (C)
        avatar   VARCHAR(255) NULL                   -- media/avatares/<id>.png (A)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "portal_actos" => "CREATE TABLE IF NOT EXISTS portal_actos (
        id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        titulo      VARCHAR(150) NOT NULL,
        descripcion TEXT,
        fecha       DATETIME     NOT NULL,
        lugar       VARCHAR(150) NOT NULL,
        precio      DECIMAL(6,2) NOT NULL DEFAULT 0,
        aforo       INT UNSIGNED NOT NULL,
        imagen      VARCHAR(255) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // ON DELETE RESTRICT: MySQL no deja borrar un acto (ni un cliente) que tenga
    // entradas vendidas; el borrado de actos lo detecta con la PDOException
    "portal_entradas" => "CREATE TABLE IF NOT EXISTS portal_entradas (
        id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        acto_id      INT UNSIGNED NOT NULL,
        cliente_id   INT UNSIGNED NOT NULL,
        cantidad     INT UNSIGNED NOT NULL,
        fecha_compra DATETIME     NOT NULL,
        firma        VARCHAR(255) NULL,              -- media/firmas/<id>.png (C)
        FOREIGN KEY (acto_id)    REFERENCES portal_actos (id)    ON DELETE RESTRICT,
        FOREIGN KEY (cliente_id) REFERENCES portal_clientes (id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "portal_puntuaciones" => "CREATE TABLE IF NOT EXISTS portal_puntuaciones (
        id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT UNSIGNED NULL,                -- NULL si juega alguien no registrado
        nombre     VARCHAR(30)  NOT NULL,
        puntos     INT          NOT NULL,
        fecha      DATETIME     NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

// Actos de ejemplo: titulo, descripcion, fecha, lugar, precio, aforo, imagen.
// Los tres primeros son los de la portada. Hay uno ya pasado (para probar el filtro
// de fecha), varios en el mismo lugar (filtro de lugar), gratuitos (precio 0) y uno
// con aforo pequeño (para probar la compra con el aforo completo).
$actos_ejemplo = [
    ["Concierto de Navidad",
     "La orquesta sinfónica interpreta villancicos y obras clásicas de temporada.",
     "2026-12-19 19:00:00", "Auditori i Palau de Congressos de Castelló", "15.00", 400,
     "media/home/concierto.svg"],
    ["La casa de Bernarda Alba",
     "Montaje de la obra de Federico García Lorca a cargo de una compañía valenciana.",
     "2027-01-16 20:30:00", "Teatre Principal de Castelló", "12.00", 300,
     "media/home/teatro.svg"],
    ["Fotografía de la Mediterrània",
     "Exposición de fotografía sobre el paisaje y la vida en la costa mediterránea.",
     "2027-02-05 11:00:00", "Paranimf de la Universitat Jaume I", "0.00", 150,
     "media/home/exposicion.svg"],
    ["Jazz en el Raval",
     "Concierto íntimo de un trío de jazz con temas propios y estándares.",
     "2026-12-05 21:00:00", "Teatre del Raval de Castelló", "8.00", 10, null],
    ["Cuentacuentos de invierno",
     "Sesión de cuentos para público infantil a partir de 4 años.",
     "2026-12-27 12:00:00", "Biblioteca Pública de Castelló", "0.00", 40, null],
    ["Ciclo de cine clásico: Casablanca",
     "Proyección en versión original subtitulada con presentación previa.",
     "2027-01-22 19:00:00", "Paranimf de la Universitat Jaume I", "4.00", 200, null],
    ["Danza contemporánea: Marea",
     "Espectáculo de danza inspirado en el mar y sus cambios.",
     "2027-03-12 20:00:00", "Teatre Principal de Castelló", "18.50", 300, null],
    ["Recital de poesía",
     "Lectura de poemas de autores castellonenses (acto ya celebrado).",
     "2026-10-17 19:30:00", "Biblioteca Pública de Castelló", "0.00", 60, null],
];

try {
    // Control de acceso (Tema 2: autorización por rol): si ya está instalado, solo el gestor
    if (existe_tabla($pdo, "portal_clientes") && !es_gestor()) {
        http_response_code(403);
        array_push($error_msg, "El portal ya está instalado: solo el gestor puede volver a ejecutar la instalación");
    } else {
        // 1. Tablas: se apunta si se ha creado o si ya existía
        foreach ($tablas as $nombre => $sql) {
            $ya_existia = existe_tabla($pdo, $nombre);
            $pdo->exec($sql);   // SQL fijo, sin datos del usuario: no hace falta prepare
            array_push($pasos, $ya_existia ? "Tabla $nombre: ya existía, no se ha modificado"
                                           : "Tabla $nombre: creada");
        }

        // 2. Actos de ejemplo, solo si la tabla está vacía (así no se duplican)
        $consulta = $pdo->prepare("SELECT COUNT(*) FROM portal_actos");
        $consulta->execute();
        $num_actos = $consulta->fetchColumn();

        if ($num_actos == 0) {
            $consulta = $pdo->prepare("INSERT INTO portal_actos
                (titulo, descripcion, fecha, lugar, precio, aforo, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($actos_ejemplo as $acto) {
                $consulta->execute($acto);
            }
            array_push($pasos, "Actos de ejemplo: insertados " . count($actos_ejemplo));
        } else {
            array_push($pasos, "Actos de ejemplo: no se insertan porque ya hay $num_actos actos");
        }

        $exito_msg = "Instalación completada";
    }
} catch (PDOException $e) {
    // Como en conexion.php: no se muestra $e->getMessage() (puede tener datos de la BD)
    http_response_code(500);
    array_push($error_msg, "No se ha podido completar la instalación. Revisa los datos de gestion_BD.php y que MySQL esté arrancado");
}

// Página: mismo header, mensajes y footer que el portal (sin menú)
require_once(dirname(__FILE__) . "/partials/header.php");

if (count($error_msg) > 0) {
    require_once(dirname(__FILE__) . "/partials/error.php");
}
if ($exito_msg !== "") {
    print('<aside class="exito" role="status">' . htmlspecialchars($exito_msg) . '</aside>');
}
?>
<main>
    <h2>Instalación del portal</h2>
<?php if (count($pasos) > 0): ?>
    <ul>
<?php foreach ($pasos as $paso): ?>
        <li><?php print(htmlspecialchars($paso)); ?></li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
    <p><a href="portal.php">Ir al portal</a></p>
</main>
<?php
require_once(dirname(__FILE__) . "/partials/footer.php");

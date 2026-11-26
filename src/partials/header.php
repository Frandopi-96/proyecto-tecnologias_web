<?php
/**
 * Cabecera común: <head> y <header> de todas las páginas.
 *
 * Carga css/estilo.css (común, persona C) y, después, las hojas y scripts
 * propios de la página que haya puesto portal.php en $css y $js.
 * Los scripts van en el <head> con defer: se ejecutan cuando el DOM ya está
 * cargado (Tema 3). Nada de JavaScript dentro de atributos HTML (onclick...).
 *
 * Personalización de cada usuario (como B4, Pas 10 y Pas 11): el usuario de la
 * sesión se escribe en <body data-user="..."> (vacío si es anónimo) y
 * js/personalizar.js lo lee con dataset para aplicar lo que tenga guardado en
 * localStorage. Por eso css/tema-oscuro.css y js/personalizar.js se cargan
 * siempre (el tema oscuro solo se activa con la clase que pone el JS).
 * htmlspecialchars evita que un nombre con comillas rompa el atributo (XSS).
 *
 * @author Juan (persona A)
 */

$usuario_body = $_SESSION["user"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Grupo portal cultural EI1042">
    <title>Portal de actos culturales</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/tema-oscuro.css">
<?php foreach ($css as $hoja): ?>
    <link rel="stylesheet" href="<?php print(htmlspecialchars($hoja)); ?>">
<?php endforeach; ?>
<?php foreach ($js as $script): ?>
    <script src="<?php print(htmlspecialchars($script)); ?>" defer></script>
<?php endforeach; ?>
    <script src="js/personalizar.js" defer></script>
</head>

<body data-user="<?php print(htmlspecialchars($usuario_body)); ?>">
    <header>
        <a href="portal.php">
            <h1>Portal de actos culturales</h1>
        </a>
        <p>Encuentra actos por fecha o lugar y compra tus entradas</p>
    </header>

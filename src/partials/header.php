<?php
/**
 * Cabecera común: <head> y <header> de todas las páginas.
 *
 * Carga css/estilo.css (común, persona C) y, después, las hojas y scripts
 * propios de la página que haya puesto portal.php en $css y $js.
 * Los scripts van en el <head> con defer: se ejecutan cuando el DOM ya está
 * cargado (Tema 3). Nada de JavaScript dentro de atributos HTML (onclick...).
 *
 * Personalización (Tema 2, cookies): si la cookie "tema" vale "oscuro", se
 * carga css/tema-oscuro.css justo después de estilo.css. La cookie la puede
 * cambiar cualquiera en su navegador, así que solo se acepta ese valor exacto;
 * cualquier otro deja el tema claro.
 *
 * @author Juan (persona A)
 */

$tema_oscuro = isset($_COOKIE["tema"]) && $_COOKIE["tema"] === "oscuro";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Grupo portal cultural EI1042">
    <title>Portal de actos culturales</title>
    <link rel="stylesheet" href="css/estilo.css">
<?php if ($tema_oscuro): ?>
    <link rel="stylesheet" href="css/tema-oscuro.css">
<?php endif; ?>
<?php foreach ($css as $hoja): ?>
    <link rel="stylesheet" href="<?php print(htmlspecialchars($hoja)); ?>">
<?php endforeach; ?>
<?php foreach ($js as $script): ?>
    <script src="<?php print(htmlspecialchars($script)); ?>" defer></script>
<?php endforeach; ?>
</head>

<body>
    <header>
        <a href="portal.php">
            <h1>Portal de actos culturales</h1>
        </a>
        <p>Encuentra actos por fecha o lugar y compra tus entradas</p>
    </header>

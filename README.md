# Portal de actos culturales — EI1042 Aplicaciones Web

Portal web para gestionar actos culturales: búsqueda por fecha o lugar y compra de entradas.
Proyecto de grupo (4 personas) de la asignatura EI1042, UJI.

## Restricciones del enunciado
- Sin librerías externas (nada de Bootstrap, jQuery…): PHP, HTML, CSS y JavaScript nativos.
- Todo lo que se haga debe documentarse en la memoria (máx. 10 páginas).

## Roles de usuario
| Rol | Qué puede hacer |
|---|---|
| Anónimo | Ver y buscar actos |
| Registrado | Lo anterior + comprar entradas, suscribirse al correo, personalizar su entorno |
| Administrador | Registrar, modificar y borrar actos |

## Estructura
```
src/
  portal.php        controlador principal (?action=...)
  partials/         trozos de página (header, menu, footer, formularios, listados)
  css/  js/         estilos y scripts
  media/            imágenes (galería, logos)
  recursos/         datos JSON (usuarios, actos, entradas, puntuaciones)
docs/               reparto, normas y memoria
```

## Puesta en marcha
1. Clonar el repositorio dentro de la carpeta que sirve Apache (p. ej. `/var/www/html/`).
2. Dar permisos de escritura a `src/recursos/` para que PHP pueda guardar los JSON.
3. Abrir `http://localhost/proyecto-tecnologias_web/src/portal.php`.

## Equipo
Ver [docs/REPARTO.md](docs/REPARTO.md) y el tablero del proyecto en GitHub (pestaña Projects). Normas de trabajo en [CONTRIBUTING.md](CONTRIBUTING.md).

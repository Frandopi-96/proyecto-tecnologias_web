# Normas de trabajo

## Ramas
- `main`: solo código que funciona. **Nadie hace push directo a `main`.**
- Una rama por tarea: `nombre/descripcion-corta` (p. ej. `ana/form-compra`).

```bash
git checkout main && git pull
git checkout -b ana/form-compra
# ...trabajar...
git add . && git commit -m "Añade formulario de compra de entradas"
git push -u origin ana/form-compra
```
Después, abrir un Pull Request en GitHub.

## Pull Requests
- Otra persona del grupo lo revisa y lo aprueba antes de hacer merge.
- Antes de pedir revisión: `git pull origin main` en tu rama y comprobar que todo sigue funcionando.

## Commits
- En presente y diciendo qué hace: "Añade listado de actos", "Corrige validación de fecha".
- Commits pequeños: facilitan la revisión y la memoria.

## Código
- Ficheros y variables en minúscula con guion bajo: `form_compra.php`, `$lista_actos`.
- Nuevas páginas: añadir un `case` en `portal.php` y su `partial` en `src/partials/`.
- Comentar cada fichero con una cabecera (qué hace, autor).
- Escapar siempre la salida con `htmlspecialchars` y validar los datos en el servidor.
- Nada de librerías externas.

## Ficheros compartidos
`portal.php`, `header.php`, `menu.php`, `footer.php` y el CSS global los tocan todos:
avisad por el grupo antes de cambiarlos para evitar conflictos.

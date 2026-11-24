# Cómo trabajamos

## 1. El tablero
Repositorio → pestaña **Projects** → "Portal cultural". Cada tarjeta es una issue.

| Campo | Para qué |
|---|---|
| **Persona** | Grupo A, B, C o D (el tablero se agrupa por este campo) |
| **Assignees** | Quién la está haciendo |
| **Status** | Todo → In Progress → Done |
| **Rama** | Nombre de la rama de git de esa tarea |
| **Linked pull requests** | El PR que la cierra (se rellena solo) |

## 2. Flujo de una tarea (paso a paso)
1. **Asígnate** la issue y muévela a **In Progress**.
2. **Crea la rama desde la issue** (queda enlazada en su apartado *Development*):
   ```bash
   git checkout main && git pull
   gh issue develop 2 --name ana/form-registro --checkout   # 2 = número de la issue
   ```
3. Escribe el nombre de la rama en el campo **Rama** del tablero.
4. Trabaja y haz commits pequeños:
   ```bash
   git add . && git commit -m "Añade formulario de registro"
   git push -u origin ana/form-registro
   ```
5. **Abre el Pull Request** (`gh pr create --fill` o desde la web). En la descripción escribe **`Closes #2`**.
6. **Otra persona del grupo lo revisa** y lo aprueba. Después se hace merge.
7. Al hacer merge, la issue se cierra sola y la tarjeta pasa a **Done**.

## 3. Ramas
- `main`: solo código que funciona. **Nadie hace push directo a `main`.**
- Una rama por tarea: `nombre/descripcion-corta` (minúsculas, sin tildes ni espacios).
- Mientras trabajas, trae lo nuevo de vez en cuando: `git pull origin main`.

## 4. Commits
- En presente y diciendo qué hacen: "Añade listado de actos", "Corrige validación de fecha".

## 5. Código
- **Solo lo visto en la asignatura** (tabla en el README). Si usas algo nuevo, explícalo en la memoria.
- Ficheros y variables en minúscula con guion bajo: `form_compra.php`, `$lista_actos`.
- Nueva página: un `case` en `portal.php` + su `partial` en `src/partials/` (sin `<main>`: lo pone `portal.php`).
  Si es solo para usuarios con sesión o para el gestor, añade la acción a `$permisos` (`"logueado"` o `"gestor"`)
  y su enlace en el bloque que toque de `partials/menu.php`.
- JavaScript: en el `case`, `array_push($js, "js/<parte>.js");` y `header.php` lo carga en el `<head>` con `defer`
  (Tema 3). **Prohibido JS dentro de atributos HTML** (`onclick="..."`): todo con `addEventListener`.
  Para un CSS propio: `array_push($css, "css/<parte>.css");`.
- Nueva petición asíncrona: un fichero en `src/api/` que devuelve JSON.
- Todo formulario se valida en el navegador (eventos del DOM) **y** en PHP.
- El JS de cada uno en su propio fichero: `src/js/<parte>.js` (p. ej. `compra.js`, `juego.js`).
- Estilos comunes en `css/estilo.css` (de C); lo propio de cada parte en `css/<parte>.css`.
- Cabecera en cada fichero (qué hace y autor) y comentarios en lo importante.
- **Reglas de BD y seguridad: obligatorio leer [docs/BASE_DE_DATOS.md](docs/BASE_DE_DATOS.md)**
  (PDO con `prepare`, permisos en el servidor, `htmlspecialchars`, errores).
- Nada de librerías externas.

## 6. Lo que NUNCA se sube
`src/includedb/gestion_BD.php` (contraseña de la BD) y las imágenes generadas
(`media/firmas`, `media/avatares`). Ya están en `.gitignore`.

## 7. Ficheros compartidos
`portal.php`, `includedb/*`, `header.php`, `menu.php`, `footer.php` y `css/estilo.css` los tocan todos:
avisad por el grupo antes de cambiarlos para evitar conflictos.

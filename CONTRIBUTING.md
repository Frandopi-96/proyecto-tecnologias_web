# Cómo trabajamos

## 1. El tablero
Repositorio → pestaña **Projects** → "Portal cultural - reparto". Cada tarjeta es una issue.

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
5. **Abre el Pull Request** (`gh pr create` o desde la web). En la descripción escribe **`Closes #2`**.
6. **Otra persona del grupo lo revisa** y lo aprueba. Después se hace merge.
7. Al hacer merge, la issue se cierra sola y la tarjeta pasa a **Done**.

## 3. Ramas
- `main`: solo código que funciona. **Nadie hace push directo a `main`.**
- Una rama por tarea: `nombre/descripcion-corta` (minúsculas, sin tildes ni espacios).
- Antes de pedir revisión: `git pull origin main` en tu rama y comprobar que todo sigue funcionando.

## 4. Commits
- En presente y diciendo qué hacen: "Añade listado de actos", "Corrige validación de fecha".

## 5. Código
- Ficheros y variables en minúscula con guion bajo: `form_compra.php`, `$lista_actos`.
- Nueva página: un `case` en `portal.php` + su `partial` en `src/partials/`.
- Nueva petición asíncrona: un fichero en `src/api/` que incluye `cargar_wp.php` y devuelve JSON.
- Cabecera en cada fichero (qué hace y autor) y comentarios en lo importante.
- Reglas de base de datos y seguridad: **obligatorio leer [docs/BASE_DE_DATOS.md](docs/BASE_DE_DATOS.md)**
  (`wp_unslash`, `$wpdb->prepare`, permisos en el servidor, `htmlspecialchars`).
- Nada de librerías externas.

## 6. Lo que NUNCA se sube
`src/config.php` y `wp-config.php` (rutas y contraseñas de cada uno). Ya están en `.gitignore`.

## 7. Ficheros compartidos
`portal.php`, `cargar_wp.php`, `header.php`, `menu.php`, `footer.php` y el CSS global los tocan todos:
avisad por el grupo antes de cambiarlos para evitar conflictos.

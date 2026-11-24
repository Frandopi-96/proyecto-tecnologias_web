# Reparto del trabajo

Cada persona tiene **1 formulario + 1 listado** (requisito del enunciado) y una parte "transversal".
En la defensa, cada uno presenta su parte. Las tareas concretas están en el **tablero** de GitHub,
agrupadas por persona (campo *Persona*).

| Persona | Área | Formulario | Listado | Además |
|---|---|---|---|---|
| A — _nombre_ | Usuarios (WordPress) | Registro de usuario (`wp_insert_user`) | Usuarios, solo admin (`get_users`) | `portal.php`, `cargar_wp.php`, login/logout (`wp_signon`/`wp_logout`), roles y menú según rol, personalización (`user_meta`), comprobar que todos tienen el entorno |
| B — _nombre_ | Actos (admin) | Alta / modificación de acto | Actos con búsqueda por fecha y lugar (asíncrona, `fetch` + `src/api/`) | `instalar.php` con las tablas propias y actos de ejemplo, borrado de actos |
| C — _nombre_ | Entradas y diseño | Compra de entradas (controla el aforo) | "Mis entradas" | Suscripción al correo (`user_meta`), CSS global y paleta, páginas principal / qui som / legal / cookies |
| D — _nombre_ | Asincronía y Canvas | Guardar puntuación del juego (`fetch` POST) | Ranking de puntuaciones (asíncrono) | Aside asíncrono (tiempo, noticias…), galería asíncrona, juego en Canvas |

Tablas de cada uno: ver [BASE_DE_DATOS.md](BASE_DE_DATOS.md).

## Trabajo común (todos)
- Tener el entorno montado según [INSTALACION.md](INSTALACION.md).
- Gestión de errores, comentarios en el código y accesibilidad de su parte.
- Pruebas de su parte con los 3 roles (anónimo, suscriptor, administrador) para el capítulo de Evaluación.
- Su sección del capítulo de Implementación en la memoria.

## Dependencias y orden
1. **Semana 1:**
   - A: `cargar_wp.php`, `portal.php`, header/menu/footer y login → **los demás dependen de esto**.
   - B: `instalar.php` con las tablas → C y D dependen de las tablas.
   - C: CSS global y paleta. D: aside y galería (no dependen de nadie).
2. **Semana 2:** cada uno su formulario + listado; D el juego.
3. **Semana 3:** integración, pruebas cruzadas de roles, accesibilidad y memoria.

# Reparto del trabajo

**Objetivo: que los 4 practiquemos toda la asignatura.** Cada persona hace su parte completa
(de la página a la base de datos) siguiendo el mismo esquema, y además una pieza de Canvas y una
página estática. Así en la defensa cada uno puede explicar todas las tecnologías con su propio código.

Las tareas concretas están en el **tablero** de GitHub ("Portal cultural"), agrupadas por persona.

## Lo que hace TODO el mundo (esquema común)

| Pieza | Qué incluye | Tema |
|---|---|---|
| **1 formulario** | HTML accesible (`label`, `required`…) + su CSS | T2 |
| | Validación en el navegador con eventos del DOM (`input`, `submit`) y mensajes junto al campo | T3 |
| | Validación en PHP, permisos, guardado en la BD de WordPress (`$wpdb` o funciones de WP) y gestión de errores | T1 + WP |
| **1 listado asíncrono** | `src/api/xxx.php` devuelve JSON → `fetch` → se pinta con el DOM; mensaje si falla o está vacío | T4 + T3 |
| **1 pieza de Canvas** | Dibujo con Canvas y eventos de ratón/teclado (boletín B4) | T3 |
| **1 página estática** | HTML semántico y CSS usando las variables comunes | T2 |
| **Calidad** | Comentarios, accesibilidad, pruebas con los 3 roles, su parte de la memoria | — |

## Qué le toca a cada uno

| | Formulario | Listado asíncrono | Canvas | Página estática | Además |
|---|---|---|---|---|---|
| **A** _nombre_ | Registro de usuario (`wp_insert_user`) | Usuarios, solo admin (`api/usuarios.php`) | Avatar dibujado en el perfil (como `canvasCara` de B4) | Principal | `cargar_wp.php`, `portal.php`, header/menu/footer, login/logout, roles, personalización, entorno de todos |
| **B** _nombre_ | Alta / modificación de acto | Actos con búsqueda por fecha y lugar (`api/actos.php`) | Gráfico de entradas vendidas por acto, solo admin (`api/ventas.php`) | Qui som | `instalar.php` (tablas y actos de ejemplo), borrado de actos, preguntar a la profesora |
| **C** _nombre_ | Compra de entradas (controla el aforo) | Mis entradas (`api/mis_entradas.php`) | Firma del comprador al comprar (como `canvas_firma` de B4) | Política legal | Variables CSS comunes, suscripción al correo, **aside asíncrono** (tiempo, noticias…) |
| **D** _nombre_ | Guardar puntuación del juego | Ranking (`api/ranking.php`) | **Juego con puntuación** | Cookies | **Galería asíncrona** |

Tablas y funciones de cada uno: [BASE_DE_DATOS.md](BASE_DE_DATOS.md).

## Dependencias y orden
1. **Semana 1:**
   - A: `cargar_wp.php`, `portal.php`, header/menu/footer y login → **los demás dependen de esto**.
   - B: `instalar.php` con las tablas → C y D dependen de las tablas.
   - C: variables CSS comunes (colores, fuentes) → todos las usan.
   - Todos: entorno montado ([INSTALACION.md](INSTALACION.md)) y su página estática.
2. **Semana 2:** cada uno su formulario, su listado asíncrono y su Canvas; C el aside; D la galería.
3. **Semana 3:** integración, pruebas cruzadas de roles, accesibilidad y memoria.

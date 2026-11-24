# Reparto del trabajo

**Objetivo: que los 4 practiquemos toda la asignatura** con lo visto en teoría, seminarios y laboratorio.
Cada persona hace su parte completa (de la página a la BD) siguiendo el mismo esquema, y además una
pieza de Canvas y una página estática. Así en la defensa cada uno puede explicar todo con su propio código.

Tareas concretas: **tablero** de GitHub ("Portal cultural"), agrupadas por persona.

## Esquema común (todo el mundo)
| Pieza | Qué incluye | Temario |
|---|---|---|
| **1 formulario** | HTML accesible (`label`, `required`) + CSS | Tema 1, B2 |
| | Validación en el navegador con eventos del DOM (`input`, `submit`) | Tema 3 |
| | `case` en `portal.php`, validación en PHP, permisos por rol, guardado con PDO, errores en `$error_msg` | Tema 2, B3 |
| **1 listado asíncrono** | `src/api/xxx.php` con PDO → JSON → `fetch` → se pinta con el DOM; mensajes de error | Tema 4 |
| **1 pieza de Canvas** | Canvas 2D con eventos de ratón o teclado | B4 |
| **1 página estática** | HTML semántico, CSS de `estilo.css`, accesible | Tema 1, B2 |
| **Calidad** | Comentarios, pruebas con los 3 roles, su parte de la memoria | — |

## Qué hace cada uno

### A — Usuarios y sesiones
| | |
|---|---|
| **PHP** | `includedb/conexion.php` (PDO), `includedb/sesion.php` (sesión, caducidad, `es_gestor()`), `portal.php` (controlador); registro con `password_hash`, login con `password_verify` y `$_SESSION`, logout; personalización con `setcookie`; guardar el avatar (PNG); `api/clientes.php` |
| **HTML/CSS** | header, menu (según el rol), footer, página principal, formularios de registro, login y perfil |
| **JS** | Validación del registro (eventos del DOM), avatar dibujado en Canvas (como `canvasCara`, B4), `fetch` del listado de clientes |

### B — Actos (gestor)
| | |
|---|---|
| **PHP** | `instalar.php` (crea las 4 tablas con PDO + actos de ejemplo), alta / modificación / borrado de actos, `api/actos.php` (filtros fecha y lugar), `api/ventas.php` |
| **HTML/CSS** | Página Qui som, formulario de actos, listado y filtros, zona del gráfico |
| **JS** | Validación del formulario, confirmación antes de borrar, búsqueda con `fetch` sin recargar, gráfico de barras en Canvas con los datos de `ventas.php` |

### C — Entradas y diseño
| | |
|---|---|
| **PHP** | Compra (comprueba el aforo, inserta con PDO, guarda la firma PNG), suscripción (`portal_clientes.suscrito`), `api/mis_entradas.php` |
| **HTML/CSS** | **`css/estilo.css` común** (paleta y 2 fuentes, como B2 Pas_5), página Política legal, formulario de compra, Mis entradas, aside |
| **JS** | Validación de la compra, firma en Canvas (como `canvas_firma`, B4), `fetch` de Mis entradas, **aside asíncrono** con las noticias del WordPress (API REST) |

### D — Juego y galería
| | |
|---|---|
| **PHP** | `api/guardar_puntuacion.php` (valida e inserta con PDO), `api/ranking.php`, partial del ranking pintado con PHP al cargar (PDO) |
| **HTML/CSS** | Página Cookies con aviso, página del juego con instrucciones, formulario de puntuación, galería, ranking |
| **JS** | **Juego en Canvas** (`setInterval`, `keydown`, ratón), envío de la puntuación con `fetch` + `FormData`, refresco del ranking, **galería asíncrona** con las imágenes del WordPress (API REST), aviso de cookies |

Detalle técnico (tablas, sesión, API): [BASE_DE_DATOS.md](BASE_DE_DATOS.md).

## Dependencias y orden
1. **Semana 1:**
   - A: `conexion.php`, `sesion.php`, `portal.php`, header/menu/footer y login → **los demás dependen de esto**.
   - B: `instalar.php` con las tablas → C y D dependen de las tablas.
   - C: `estilo.css` (paleta y fuentes) → todos lo usan.
   - Todos: entorno montado ([INSTALACION.md](INSTALACION.md)) y su página estática.
2. **Semana 2:** cada uno su formulario, su listado asíncrono y su Canvas; C el aside; D la galería.
3. **Semana 3:** integración, pruebas cruzadas de roles, accesibilidad y memoria.

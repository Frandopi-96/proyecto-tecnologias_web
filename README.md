# Portal de actos culturales — EI1042 Aplicaciones Web

Portal web para gestionar actos culturales: buscarlos por fecha o lugar y comprar entradas.
Proyecto de grupo (4 personas) de la asignatura EI1042, UJI.

**Todo está hecho con lo visto en la asignatura** (temas 1-4, boletines B1-B4 y seminarios), para
poder explicarlo y defenderlo. La tabla de la sección "Qué usamos del temario" dice de dónde sale cada cosa.

## Documentación (leer en este orden)
| Documento | Para qué |
|---|---|
| [docs/INSTALACION.md](docs/INSTALACION.md) | Montar XAMPP + WordPress + el portal en tu ordenador. **Empieza aquí.** |
| [docs/REPARTO.md](docs/REPARTO.md) | Qué hace cada persona (A, B, C, D) en PHP, HTML/CSS y JavaScript |
| [docs/BASE_DE_DATOS.md](docs/BASE_DE_DATOS.md) | Conexión PDO, tablas, sesiones y reglas de código |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Cómo trabajar: tablero, issues, ramas y Pull Requests |
| [docs/memoria/INDICE.md](docs/memoria/INDICE.md) | Quién escribe cada capítulo de la memoria |

Tablero de tareas: pestaña **Projects** del repositorio → "Portal cultural".

## Restricciones del enunciado
- Sin librerías externas (nada de Bootstrap, jQuery…): PHP, HTML, CSS y JavaScript nativos.
- Todo lo que se haga debe documentarse en la memoria (máx. 10 páginas).

## Cómo funciona
- **XAMPP** (Seminario 1): Apache + MySQL/MariaDB + PHP.
- **WordPress** (Seminario 2) instalado aparte. Lo usamos de dos formas:
  1. **Su base de datos** (`wp_alxxxx`): ahí creamos **nuestras propias tablas** (`portal_...`) y nos
     conectamos con **PDO** (Tema 2, apartado 9). Ahí se guardan usuarios y contraseñas (cifradas).
  2. **Su API REST** (Tema 4): el aside muestra las entradas (noticias) del WordPress y la galería
     sus imágenes, pedidas con `fetch`.
- **Nuestro portal** es PHP propio con controlador `portal.php?action=...` y `partials` (B2, B3).
- **Login con sesiones PHP** (`$_SESSION["user"]`, `$_SESSION["rol"]`) como en B3.

## Roles de usuario (como en B3)
| Rol | `$_SESSION["rol"]` | Qué puede hacer |
|---|---|---|
| No registrado | (sin sesión) | Ver y buscar actos, jugar, ver el ranking |
| Cliente | `client` | Lo anterior + comprar entradas, suscribirse al correo, personalizar su entorno |
| Gestor | `gestor` | Registrar, modificar y borrar actos; ver clientes y gráfico de ventas |

## Qué usamos del temario
| Tecnología | Dónde se vio |
|---|---|
| HTML semántico, formularios, CSS, accesibilidad | Tema 1, B2 |
| Controlador `?action=`, `partials`, validación de formularios | B2, B3 |
| Cookies, sesiones, autenticación y autorización por rol | Tema 2 (apartados 6-8), B3 |
| PDO: `new PDO`, `prepare`, `execute`, `fetchAll`, `PDOException` | Tema 2 (apartado 9) |
| DOM y eventos (`addEventListener`, `submit`, `input`, `click`, `keydown`) | Tema 3 |
| `fetch`, Promesas, `async/await`, JSON, `FormData`, `setInterval`, gestión de errores | Tema 4 |
| API REST de WordPress (`/wp/v2/posts`, `/wp/v2/media`) | Tema 4 |
| Canvas 2D, `MouseEvent`, `toDataURL`, guardar imagen en el servidor | B4 |

## Estructura
```
src/
  portal.php                 controlador principal (?action=...)
  instalar.php               crea las tablas portal_... y actos de ejemplo (la 1.ª vez cualquiera; luego solo el gestor)
  includedb/
    gestion_BD.example.php   plantilla: copiar como gestion_BD.php (NO se sube)
    conexion.php             crea $pdo (PDO) con gestión de errores
    sesion.php               session_start, caducidad y funciones de rol
  partials/                  trozos de página (header, menu, footer, formularios, listados)
  api/                       PHP que devuelve JSON para las peticiones con fetch
  css/  js/                  estilos y scripts (un .js por parte)
  media/                     imágenes; media/firmas y media/avatares los crea el portal
docs/                        instalación, reparto, base de datos y memoria
```

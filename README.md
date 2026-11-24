# Portal de actos culturales — EI1042 Aplicaciones Web

Portal web para gestionar actos culturales: buscarlos por fecha o lugar y comprar entradas.
Proyecto de grupo (4 personas) de la asignatura EI1042, UJI.

## Documentación (leer en este orden)
| Documento | Para qué |
|---|---|
| [docs/INSTALACION.md](docs/INSTALACION.md) | Montar XAMPP + WordPress + el portal en tu ordenador. **Empieza aquí.** |
| [docs/REPARTO.md](docs/REPARTO.md) | Qué hace cada persona (A, B, C, D) |
| [docs/BASE_DE_DATOS.md](docs/BASE_DE_DATOS.md) | Usuarios en WordPress, tablas propias y funciones a usar |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Cómo trabajar: tablero, issues, ramas y Pull Requests |
| [docs/memoria/INDICE.md](docs/memoria/INDICE.md) | Quién escribe cada capítulo de la memoria |

Tablero de tareas: pestaña **Projects** del repositorio → "Portal cultural - reparto".

## Restricciones del enunciado
- Sin librerías externas (nada de Bootstrap, jQuery…): PHP, HTML, CSS y JavaScript nativos.
- Todo lo que se haga debe documentarse en la memoria (máx. 10 páginas).

## Tecnologías
- **XAMPP**: Apache + MariaDB/MySQL + PHP (Seminario 1).
- **WordPress** instalado aparte (Seminario 2). **Solo lo usamos por dentro**: su base de datos y sus
  funciones de usuarios (registro, login, roles, contraseñas cifradas). El portal **no** es un tema ni
  un plugin de WordPress: es nuestro propio PHP que carga WordPress con `wp-load.php`.
- Todos los datos (usuarios, actos, entradas, puntuaciones) están en la **base de datos de WordPress**.

## Roles de usuario
| Rol del enunciado | Rol en WordPress | Qué puede hacer |
|---|---|---|
| Anónimo | (sin sesión) | Ver y buscar actos |
| Registrado | `subscriber` | Lo anterior + comprar entradas, suscribirse al correo, personalizar su entorno |
| Administrador | `administrator` | Registrar, modificar y borrar actos; ver usuarios |

## Estructura
```
src/
  portal.php            controlador principal (?action=...)
  cargar_wp.php         carga config.php y WordPress; lo incluyen portal.php y api/*
  config.example.php    plantilla: copiar como config.php (config.php NO se sube)
  partials/             trozos de página (header, menu, footer, formularios, listados)
  api/                  ficheros PHP que devuelven JSON para las peticiones asíncronas (fetch)
  css/  js/             estilos y scripts
  media/                imágenes (galería, logos)
docs/                   instalación, reparto, base de datos y memoria
```

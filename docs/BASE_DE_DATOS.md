# Base de datos, sesiones y reglas de código

Todo sigue el **Tema 2** (cookies, sesiones, control de acceso y PDO) y el **boletín B3** (login y roles).

> Indicación de la profesora: guardar usuarios y contraseñas en la **BD de WordPress**.
> Lo hacemos así: conectamos con PDO a la BD que creamos para WordPress (`wp_alxxxx`) y creamos
> en ella **nuestras propias tablas** con prefijo `portal_` (no tocamos las tablas `wp_` de WordPress).
> Pendiente de confirmar con la profesora (tarea de B).

## 1. Conexión (persona A) — como `gestion_BD.php` + `portalBD.php` del Tema 2
`src/includedb/gestion_BD.php` (cada uno el suyo, no se sube; plantilla en `gestion_BD.example.php`):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'wp_alxxxx');
define('DB_USER', 'alxxxx');
define('DB_PASSWORD', 'uji');
```
`src/includedb/conexion.php`:
```php
<?php
require_once(dirname(__FILE__) . "/gestion_BD.php");
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                   DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Error al acceder a la base de datos.</p>";
    exit;
}
```

## 2. Sesión y roles (persona A) — como `portal.php` de B3
`src/includedb/sesion.php` (lo incluyen `portal.php` y todos los `api/*.php`):
```php
<?php
$tiempo_max = 20 * 60;
ini_set('session.gc_maxlifetime', $tiempo_max);
session_name("PORTALCULTURAL");
session_start();
// caducidad por inactividad (como B3)
if (isset($_SESSION['ultima_accion']) && time() - $_SESSION['ultima_accion'] >= $tiempo_max) {
    session_unset(); session_destroy(); session_start();
}
$_SESSION['ultima_accion'] = time();

function esta_logueado() { return isset($_SESSION["user"]); }
function es_gestor()     { return esta_logueado() && $_SESSION["rol"] == "gestor"; }
function tiene_acceso($nivel)      { /* "todos", "anonimo", "logueado" o "gestor" */ }
function api_exigir_acceso($nivel) { /* si no tiene acceso: 403 + {"error": ...} y exit */ }
```
- `tiene_acceso()` lo usan el menú (`$opciones_menu`) y `$permisos` de `portal.php`. El gestor tiene acceso a todo lo de `logueado`.
- En `portal.php`, si falta la sesión se muestra el login con "Debes iniciar sesión..."; si falta el rol, "Autorización no permitida".
- **Login** (`login_user`): buscar el login en `portal_clientes`, comprobar con
  `password_verify($pwd, $fila['password'])` y guardar `$_SESSION["user"]`, `$_SESSION["rol"]`, `$_SESSION["id"]`.
  Si falla: "Autentificación no correcta" y volver al formulario.
- **Registro** (`reg_user`): contraseña guardada con `password_hash($pwd, PASSWORD_DEFAULT)` (cifrada) y rol `client`.
  Reglas de B3: login mínimo 7 caracteres y no repetido; contraseña mínimo 7 caracteres con al menos 1 número.
- **Logout** (`exit`): `session_unset()`, `session_destroy()` y borrar la cookie de sesión con `setcookie()`.
- **Personalización** (A): preferencia del cliente en una **cookie** (`setcookie`, Tema 2), p. ej. tema claro/oscuro.

## 3. Tablas (las crea `src/instalar.php`, persona B)
Todas en la BD `wp_alxxxx`, motor InnoDB, `utf8mb4`.

**`portal_clientes`** (A)
| Campo | Tipo |
|---|---|
| id | INT UNSIGNED, PK, AUTO_INCREMENT |
| login | VARCHAR(30) NOT NULL UNIQUE |
| email | VARCHAR(100) NOT NULL |
| password | VARCHAR(255) NOT NULL — resultado de `password_hash()` |
| rol | VARCHAR(10) NOT NULL DEFAULT 'client' — `client` o `gestor` |
| suscrito | TINYINT(1) NOT NULL DEFAULT 0 — suscripción al correo (C) |
| avatar | VARCHAR(255) NULL — ruta del PNG (`media/avatares/<id>.png`) (A) |

**`portal_actos`** (B)
| Campo | Tipo |
|---|---|
| id | INT UNSIGNED, PK, AUTO_INCREMENT |
| titulo | VARCHAR(150) NOT NULL |
| descripcion | TEXT |
| fecha | DATETIME NOT NULL |
| lugar | VARCHAR(150) NOT NULL |
| precio | DECIMAL(6,2) NOT NULL DEFAULT 0 |
| aforo | INT UNSIGNED NOT NULL |
| imagen | VARCHAR(255) NULL |

**`portal_entradas`** (C)
| Campo | Tipo |
|---|---|
| id | INT UNSIGNED, PK, AUTO_INCREMENT |
| acto_id | INT UNSIGNED NOT NULL → `portal_actos.id` |
| cliente_id | INT UNSIGNED NOT NULL → `portal_clientes.id` |
| cantidad | INT UNSIGNED NOT NULL |
| fecha_compra | DATETIME NOT NULL |
| firma | VARCHAR(255) NULL — ruta del PNG (`media/firmas/<id>.png`) |

Las dos claves foráneas son `ON DELETE RESTRICT`: MySQL **no deja borrar un acto (ni un cliente) con
entradas vendidas** y lanza una `PDOException`; el borrado de actos tiene que capturarla y avisar al gestor.

**`portal_puntuaciones`** (D)
| Campo | Tipo |
|---|---|
| id | INT UNSIGNED, PK, AUTO_INCREMENT |
| cliente_id | INT UNSIGNED NULL (no registrado = NULL) |
| nombre | VARCHAR(30) NOT NULL |
| puntos | INT NOT NULL |
| fecha | DATETIME NOT NULL |

## 4. Consultas con PDO (Tema 2, apartado 9)
```php
// SELECT con datos del usuario: SIEMPRE prepare + execute (evita la inyección SQL)
$consulta = $pdo->prepare("SELECT * FROM portal_actos WHERE lugar LIKE ? AND fecha >= ? ORDER BY fecha");
$consulta->execute(["%$lugar%", $desde]);
$actos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// INSERT / UPDATE / DELETE
$consulta = $pdo->prepare("INSERT INTO portal_actos (titulo, fecha, lugar, precio, aforo) VALUES (?, ?, ?, ?, ?)");
$consulta->execute([$titulo, $fecha, $lugar, $precio, $aforo]);

// Errores
try { ... } catch (PDOException $e) { array_push($error_msg, "No se ha podido guardar el acto"); }
```

## 5. Ficheros de `src/api/` (peticiones con `fetch`, Tema 4)
Cada uno incluye `includedb/sesion.php` e `includedb/conexion.php` y responde JSON.
Antes de incluirlos se pone `$es_api = true;` para que, si falla la conexión, el error también llegue en JSON:
```php
$es_api = true;
require_once(dirname(__FILE__) . "/../includedb/sesion.php");
require_once(dirname(__FILE__) . "/../includedb/conexion.php");
api_exigir_acceso("gestor");   // o "logueado"; si no tiene acceso responde 403 y termina
header('Content-Type: application/json');
echo json_encode($datos);
```
| Fichero | Persona | Devuelve |
|---|---|---|
| `clientes.php` | A | Clientes (solo gestor) |
| `actos.php` | B | Actos, filtrados por `fecha` y `lugar` si llegan |
| `ventas.php` | B | Entradas vendidas por acto (solo gestor), para el gráfico |
| `mis_entradas.php` | C | Entradas del cliente con sesión |
| `guardar_puntuacion.php` | D | Guarda una puntuación (POST con `FormData`) |
| `ranking.php` | D | 10 mejores puntuaciones |

En JS (Tema 4): `fetch` → comprobar `respuesta.ok` → `respuesta.json()` → pintar con el DOM;
`catch` para mostrar un mensaje si falla.

## 6. API REST de WordPress (Tema 4)
| Uso | Persona | URL |
|---|---|---|
| Noticias del aside | C | `/EI1036_42/wordpress/?rest_route=/wp/v2/posts&per_page=3` |
| Imágenes de la galería | D | `/EI1036_42/wordpress/?rest_route=/wp/v2/media&media_type=image` |

## 7. Imágenes de Canvas (B4, pasos 7 y 8)
`canvas.toDataURL("image/png")` → campo oculto del formulario (o `FormData`) → PHP quita la cabecera
`data:image/png;base64,`, hace `base64_decode()` y guarda el fichero:
- Firma (C): `media/firmas/<id_entrada>.png`, ruta en `portal_entradas.firma`.
- Avatar (A): `media/avatares/<id_cliente>.png`, ruta en `portal_clientes.avatar`.

## 8. Reglas obligatorias para todos
1. **PDO siempre con `prepare` + `execute`**, nunca meter variables directamente en el SQL.
2. **Validar en el servidor** todo dato (vacío, longitud, fecha, número > 0…) y también en el navegador con eventos del DOM.
3. **Comprobar permisos en el servidor** (`esta_logueado()`, `es_gestor()`), no solo ocultar el menú.
4. **Escapar la salida** con `htmlspecialchars()`.
5. **Errores**: guardarlos en `$error_msg` y mostrarlos (como B2/B3); en `api/` responder con código HTTP y `{"error": ...}`.

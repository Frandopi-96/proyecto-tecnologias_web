# Base de datos

**Todo se guarda en la base de datos de WordPress** (indicación de la profesora para usuarios y
contraseñas; actos, entradas y puntuaciones van en la misma BD para tenerlo todo en un sitio).
> Pendiente de confirmar con la profesora que las tablas propias también pueden ir ahí.

## Cómo se carga WordPress
`src/cargar_wp.php` (tarea #1, persona A) — lo incluyen `portal.php` y todos los ficheros de `src/api/`:
```php
<?php
require_once(__DIR__ . "/config.php");   // define WP_PATH
require_once(WP_PATH . "/wp-load.php");  // funciones de WordPress + $wpdb
global $wpdb;

function es_admin()      { return current_user_can('manage_options'); } // administrator
function esta_logueado() { return is_user_logged_in(); }                // registrado o admin
```

## 1. Usuarios → tablas de WordPress (`wp_users`, `wp_usermeta`)
**No creamos tabla de usuarios.** Se usan las funciones de WordPress (cifran las contraseñas solas):

| Para… | Función | Notas |
|---|---|---|
| Registrar | `wp_insert_user(['user_login'=>..., 'user_pass'=>..., 'user_email'=>..., 'role'=>'subscriber'])` | Devuelve el id o un `WP_Error` |
| Iniciar sesión | `wp_signon(['user_login'=>..., 'user_password'=>..., 'remember'=>false])` | Llamar **antes** de imprimir nada (envía cookies). Después `wp_set_current_user($user->ID)` |
| Cerrar sesión | `wp_logout()` | |
| Usuario actual | `wp_get_current_user()`, `get_current_user_id()` | id 0 = anónimo |
| ¿Está logueado? | `is_user_logged_in()` | |
| ¿Es admin? | `current_user_can('manage_options')` | |
| Listar usuarios | `get_users()` | Solo admin |
| Preferencias | `update_user_meta($id, 'portal_tema', 'oscuro')` / `get_user_meta($id, 'portal_tema', true)` | Personalización (A) |
| Avatar | `update_user_meta($id, 'portal_avatar', $png_base64)` | Canvas de A: `canvas.toDataURL('image/png')` |
| Suscripción correo | `update_user_meta($id, 'portal_suscrito', 1)` | Persona C |
| Errores | `is_wp_error($r)` y `$r->get_error_message()` | Mostrar el mensaje al usuario |

**Nada de `$_SESSION` para el login**: WordPress gestiona la sesión con sus cookies.

## 2. Tablas propias (las crea `src/instalar.php`, persona B)
Prefijo: `$wpdb->prefix` (normalmente `wp_`).

**`wp_portal_actos`** (persona B)
| Campo | Tipo |
|---|---|
| id | BIGINT UNSIGNED, PK, AUTO_INCREMENT |
| titulo | VARCHAR(150) NOT NULL |
| descripcion | TEXT |
| fecha | DATETIME NOT NULL |
| lugar | VARCHAR(150) NOT NULL |
| precio | DECIMAL(6,2) NOT NULL DEFAULT 0 |
| aforo | INT UNSIGNED NOT NULL |
| imagen | VARCHAR(255) |

**`wp_portal_entradas`** (persona C)
| Campo | Tipo |
|---|---|
| id | BIGINT UNSIGNED, PK, AUTO_INCREMENT |
| acto_id | BIGINT UNSIGNED NOT NULL → `wp_portal_actos.id` |
| user_id | BIGINT UNSIGNED NOT NULL → `wp_users.ID` |
| cantidad | INT UNSIGNED NOT NULL |
| fecha_compra | DATETIME NOT NULL |
| firma | VARCHAR(255) NULL — ruta del PNG de la firma (`media/firmas/<id>.png`) |

**`wp_portal_puntuaciones`** (persona D)
| Campo | Tipo |
|---|---|
| id | BIGINT UNSIGNED, PK, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED NULL (anónimo = NULL) |
| nombre | VARCHAR(60) NOT NULL |
| puntos | INT NOT NULL |
| fecha | DATETIME NOT NULL |

## 3. Ficheros de `src/api/` (peticiones asíncronas)
| Fichero | Persona | Devuelve |
|---|---|---|
| `usuarios.php` | A | Usuarios (solo admin) |
| `actos.php` | B | Actos, filtrados por `fecha` y `lugar` si llegan |
| `ventas.php` | B | Entradas vendidas por acto (solo admin), para el gráfico |
| `mis_entradas.php` | C | Entradas del usuario con sesión |
| `guardar_puntuacion.php` | D | Guarda una puntuación (POST) |
| `ranking.php` | D | 10 mejores puntuaciones |

Si no hay permiso: `http_response_code(403)` y `{"error": "mensaje"}`; el JS muestra el mensaje.

## 4. Imágenes de Canvas
- **Avatar (A):** `canvas.toDataURL('image/png')` → campo oculto del formulario → `user_meta`.
- **Firma (C):** igual, pero PHP decodifica el base64 y guarda el PNG en `src/media/firmas/<id>.png`
  (como `html_grafico_guardar.php` del boletín B4); la ruta va en `wp_portal_entradas.firma`.

## 5. Consultas con `$wpdb`
```php
global $wpdb;
$tabla = $wpdb->prefix . "portal_actos";

// SELECT con datos del usuario: SIEMPRE prepare (evita inyección SQL)
$actos = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $tabla WHERE lugar LIKE %s AND fecha >= %s ORDER BY fecha",
    '%' . $wpdb->esc_like($lugar) . '%', $desde));

$wpdb->insert($tabla, ['titulo'=>$t, 'fecha'=>$f, 'lugar'=>$l, 'precio'=>$p, 'aforo'=>$a]);
$wpdb->update($tabla, ['titulo'=>$t], ['id'=>$id]);
$wpdb->delete($tabla, ['id'=>$id]);

if ($wpdb->last_error) { /* mostrar error */ }
```

## 6. Reglas obligatorias
1. **Leer `$_POST`/`$_GET` con `wp_unslash()`**: al cargar WordPress se añaden barras a las comillas.
   `$titulo = trim(wp_unslash($_POST['titulo'] ?? ''));`
2. **Validar en el servidor** todo dato (vacío, formato de fecha, número > 0…).
3. **Comprobar permisos en el servidor** (`es_admin()`, `esta_logueado()`), no solo ocultar el menú.
4. **Escapar la salida** con `htmlspecialchars()` (o `esc_html()`).
5. **Ficheros de `src/api/`**: incluyen `cargar_wp.php` y responden JSON:
   `header('Content-Type: application/json'); echo json_encode($datos);`

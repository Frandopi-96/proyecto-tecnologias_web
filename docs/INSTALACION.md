# Instalación del entorno (cada persona en su ordenador)

Basado en los Seminarios 1 y 2 y en el Tema 2 (apartado 9, PDO). Ejemplos con XAMPP en Linux
(`/opt/lampp`). En Windows la carpeta equivalente es `C:\xampp\htdocs`.

## 1. XAMPP (Seminario 1)
1. Arranca XAMPP: `sudo /opt/lampp/lampp start` (o desde el panel, "Start" en Apache y MySQL).
2. Comprueba <http://localhost> y <http://localhost/phpmyadmin>.

## 2. WordPress (Seminario 2)
1. Descomprime WordPress en `htdocs/EI1036_42/wordpress`.
2. En phpMyAdmin crea la BD `wp_alxxxx` y el usuario `alxxxx` / contraseña `uji` con todos los
   permisos sobre localhost (`alxxxx` = tu usuario de la UJI).
3. Copia `wp-config-sample.php` como `wp-config.php` y rellena `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`.
4. Abre <http://localhost/EI1036_42/wordpress/> y completa la instalación.
5. Para el aside y la galería (API REST, Tema 4), desde el escritorio de WordPress:
   - Crea 3-4 **entradas** (noticias culturales).
   - Sube 5-6 **imágenes** a Medios (galería).
   - Comprueba: <http://localhost/EI1036_42/wordpress/?rest_route=/wp/v2/posts> devuelve JSON.

## 3. El portal (este repositorio)
Apache solo sirve lo que está dentro de `htdocs`, así que clona el repo ahí:
```bash
sudo chown -R $USER /opt/lampp/htdocs/EI1036_42      # para poder escribir sin sudo
cd /opt/lampp/htdocs/EI1036_42
git clone https://github.com/Frandopi-96/proyecto-tecnologias_web.git
```
Resultado:
```
htdocs/EI1036_42/
  wordpress/                  ← WordPress (Seminario 2)
  proyecto-tecnologias_web/   ← nuestro portal
```

## 4. Datos de la base de datos (como gestion_BD.php del Tema 2)
```bash
cd /opt/lampp/htdocs/EI1036_42/proyecto-tecnologias_web/src/includedb
cp gestion_BD.example.php gestion_BD.php
```
Pon en `gestion_BD.php` **los mismos datos que en tu `wp-config.php`** (`wp_alxxxx`, `alxxxx`, `uji`).
Este fichero tiene contraseñas: **no se sube** (está en `.gitignore`).

Para que el portal pueda guardar las imágenes de Canvas (firmas y avatares):
```bash
mkdir -p ../media/firmas ../media/avatares
chmod 777 ../media/firmas ../media/avatares
```

## 5. Crear las tablas
Cuando esté hecha la tarea de B "Crear tablas (instalar.php)", abre una vez
<http://localhost/EI1036_42/proyecto-tecnologias_web/src/instalar.php>.
Crea las tablas `portal_...` dentro de `wp_alxxxx` e inserta actos de ejemplo.

## 6. Crear un gestor (como en B3)
1. Regístrate en el portal con el formulario de registro (queda con rol `client`).
2. En phpMyAdmin → `wp_alxxxx` → tabla `portal_clientes`, cambia el `rol` de ese usuario a `gestor`.
3. Ten también otro usuario `client` para probar ese rol.

## 7. Abrir el portal
<http://localhost/EI1036_42/proyecto-tecnologias_web/src/portal.php>

## Problemas típicos
| Síntoma | Causa |
|---|---|
| "Error al acceder a la base de datos" | MySQL parado o datos de `gestion_BD.php` incorrectos |
| `Table 'wp_alxxxx.portal_...' doesn't exist` | Falta ejecutar `instalar.php` |
| Página en blanco | Activa `ini_set('display_errors', 1);` y mira el error |
| El aside o la galería no cargan | WordPress sin entradas/imágenes o URL de la API REST mal escrita |
| No se guardan firmas/avatares | Faltan permisos en `media/firmas` y `media/avatares` |

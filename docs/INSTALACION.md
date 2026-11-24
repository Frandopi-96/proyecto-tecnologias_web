# Instalación del entorno (cada persona en su ordenador)

Basado en los Seminarios 1 y 2 de la asignatura. Ejemplos con XAMPP en Linux (`/opt/lampp`).
En Windows la carpeta equivalente es `C:\xampp\htdocs`.

## 1. XAMPP
1. Instala XAMPP y arráncalo: `sudo /opt/lampp/lampp start` (o desde el panel, "Start" en Apache y MySQL).
2. Comprueba que funciona: <http://localhost> y <http://localhost/phpmyadmin>.

## 2. WordPress (Seminario 2)
1. Descomprime WordPress en `htdocs/EI1036_42/wordpress`.
2. En phpMyAdmin crea la base de datos `wp_alxxxx` y el usuario `alxxxx` / contraseña `uji`
   con todos los permisos sobre localhost (`alxxxx` = tu usuario de la UJI).
3. Copia `wp-config-sample.php` como `wp-config.php` y rellena `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`.
4. Abre <http://localhost/EI1036_42/wordpress/> y completa la instalación.
   **Apunta el usuario y la contraseña del administrador**: será el administrador del portal.
5. Crea también, desde el escritorio de WordPress (Usuarios → Añadir nuevo), un usuario de prueba con
   rol **Suscriptor** para probar el rol "registrado".

> `wp-config.php` contiene la contraseña de la BD: **nunca** se sube al repositorio.

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

## 4. Configuración local
```bash
cd /opt/lampp/htdocs/EI1036_42/proyecto-tecnologias_web/src
cp config.example.php config.php
```
Edita `config.php` y pon la ruta a **tu** carpeta de WordPress (cada uno la suya; por eso no se sube).

## 5. Tablas propias
Cuando esté hecha la tarea de B "Crear tablas propias", entra como administrador y abre
<http://localhost/EI1036_42/proyecto-tecnologias_web/src/instalar.php> una vez.
Crea las tablas de [BASE_DE_DATOS.md](BASE_DE_DATOS.md) e inserta actos de ejemplo.

## 6. Abrir el portal
<http://localhost/EI1036_42/proyecto-tecnologias_web/src/portal.php>

## Problemas típicos
| Síntoma | Causa |
|---|---|
| `Failed opening ... wp-load.php` | La ruta de `config.php` está mal |
| `Error establishing a database connection` | MySQL parado o datos de `wp-config.php` incorrectos |
| Página en blanco | Activa `ini_set('display_errors', 1);` y mira el error |
| Las comillas aparecen como `\'` | WordPress añade barras a `$_POST`: usa `wp_unslash()` (ver BASE_DE_DATOS.md) |

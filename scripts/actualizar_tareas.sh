#!/usr/bin/env bash
# Actualiza las tareas (issues) del tablero al cambio a "usuarios y datos en la BD de WordPress"
# y crea el campo "Rama" y 2 tareas nuevas. Se puede ejecutar más de una vez sin duplicar nada.
# Requisitos: gh autenticado con permiso de proyectos (gh auth refresh -s project).
# Uso (desde la raíz del repo):  bash scripts/actualizar_tareas.sh
# Cuando termine bien, borrad este script del repo.

set -euo pipefail

OWNER="Frandopi-96"
REPO="$OWNER/proyecto-tecnologias_web"
TITULO="Portal cultural"
declare -A GRUPO=(
  [A]="A - Usuarios y sesiones"
  [B]="B - Actos (admin)"
  [C]="C - Entradas y diseño"
  [D]="D - Asincronía y Canvas"
)

echo "1/4 Buscando el tablero"
PROJ=$(gh project list --owner "$OWNER" --format json \
   --jq ".projects[] | select(.title==\"$TITULO\") | .number")
[ -n "$PROJ" ] || { echo "No encuentro el tablero \"$TITULO\""; exit 1; }
PROJ_ID=$(gh project view "$PROJ" --owner "$OWNER" --format json --jq .id)

echo "2/4 Campo Rama"
if gh project field-list "$PROJ" --owner "$OWNER" --format json --jq '.fields[].name' | grep -qx "Rama"; then
  echo "  ya existe"
else
  gh project field-create "$PROJ" --owner "$OWNER" --name "Rama" --data-type TEXT > /dev/null
  echo "  creado"
fi

echo "3/4 Actualizando tareas existentes"
# editar NUMERO "Inicio del título actual (para comprobar)" "Título nuevo" "Descripción nueva"
editar() {
  local n="$1" viejo="$2" titulo="$3" cuerpo="$4" actual
  actual=$(gh issue view "$n" --repo "$REPO" --json title --jq .title)
  if [[ "$actual" != "$viejo"* && "$actual" != "$titulo" ]]; then
    echo "  AVISO #$n se llama \"$actual\": no la toco"; return
  fi
  gh issue edit "$n" --repo "$REPO" --title "$titulo" --body "$cuerpo" > /dev/null
  echo "  #$n $titulo"
}

REGLAS='Reglas: docs/BASE_DE_DATOS.md (wp_unslash, $wpdb->prepare, permisos en el servidor, htmlspecialchars).'

# ---------- A ----------
editar 1 "Controlador portal.php" "Controlador portal.php, cargar_wp.php y estructura de partials" \
"Base para el resto: hacerlo primero.
- [ ] src/cargar_wp.php: incluye config.php y WP_PATH/wp-load.php; funciones es_admin() y esta_logueado() (ver docs/BASE_DE_DATOS.md)
- [ ] src/portal.php: controlador con ?action= que incluye cargar_wp.php
- [ ] partials header, menu y footer
- [ ] Mensajes de error entre el menú y el contenido"

editar 2 "Formulario de registro" "Formulario de registro de usuarios (WordPress)" \
"FORMULARIO de A.
- [ ] Campos: usuario, email, contraseña y repetir contraseña
- [ ] Validar en el servidor (vacíos, email, contraseñas iguales)
- [ ] Crear con wp_insert_user() y rol subscriber (la contraseña la cifra WordPress)
- [ ] Si devuelve WP_Error, mostrar get_error_message()
$REGLAS"

editar 3 "Login, logout" "Login y logout con WordPress" \
"- [ ] Formulario de login
- [ ] wp_signon() ANTES de imprimir HTML; después wp_set_current_user()
- [ ] wp_logout() en ?action=exit
- [ ] Sin \$_SESSION para el login: la sesión la gestiona WordPress con sus cookies"

editar 4 "Roles y menú" "Roles y menú según el rol (WordPress)" \
"Anónimo = sin sesión; registrado = subscriber; administrador = administrator.
- [ ] Menú distinto para cada rol
- [ ] Comprobar permisos también en portal.php con es_admin() / esta_logueado(), no solo ocultar el menú
- [ ] Probar los 3 roles con el admin de WordPress y un usuario Suscriptor"

editar 5 "Personalizar el entorno" "Personalizar el entorno del usuario registrado (user_meta)" \
"- [ ] Elegir una preferencia (p. ej. tema claro/oscuro o tamaño de letra)
- [ ] Guardarla con update_user_meta() y leerla con get_user_meta()
- [ ] Aplicarla al cargar cada página"

editar 6 "Listado de usuarios" "Listado de usuarios (solo admin, get_users)" \
"LISTADO de A.
- [ ] Solo si es_admin(); si no, mensaje de error
- [ ] Tabla con usuario, email y rol usando get_users()"

# ---------- B ----------
editar 9 "Datos de ejemplo de actos" "Crear tablas propias en la BD de WordPress (instalar.php)" \
"Bloquea a C y D: hacerlo en la semana 1.
- [ ] src/instalar.php: solo admin; crea wp_portal_actos, wp_portal_entradas y wp_portal_puntuaciones con \$wpdb (esquema en docs/BASE_DE_DATOS.md)
- [ ] Insertar 5-10 actos de ejemplo si la tabla está vacía
- [ ] Ejecutarlo dos veces no debe dar error ni duplicar
- [ ] Avisar al grupo cuando esté en main"

editar 10 "Formulario de alta y modificación" "Formulario de alta y modificación de actos (\$wpdb)" \
"FORMULARIO de B. Solo administrador (es_admin()).
- [ ] Campos: título, descripción, fecha, lugar, precio, aforo, imagen
- [ ] Validar en el servidor (fecha válida, precio >= 0, aforo > 0)
- [ ] \$wpdb->insert() / \$wpdb->update() en wp_portal_actos; comprobar \$wpdb->last_error
$REGLAS"

editar 11 "Borrado de actos" "Borrado de actos (\$wpdb)" \
"- [ ] Solo administrador
- [ ] Confirmación en el navegador antes de borrar (evento del DOM)
- [ ] \$wpdb->delete() en wp_portal_actos"

editar 12 "Listado de actos" "Listado de actos" \
"LISTADO de B. Visible para todos los roles.
- [ ] \$wpdb->get_results() de wp_portal_actos ordenado por fecha
- [ ] Si es admin: enlaces Modificar y Borrar
- [ ] Si está logueado: enlace Comprar (lo usa C)"

editar 13 "Búsqueda de actos" "Búsqueda de actos por fecha y lugar (asíncrona)" \
"- [ ] src/api/buscar_actos.php: recibe fecha y lugar, consulta con \$wpdb->prepare y devuelve JSON
- [ ] JS: fetch al escribir/enviar y actualizar el listado con el DOM sin recargar
- [ ] Mostrar mensaje si no hay resultados o si falla la petición"

# ---------- C ----------
editar 18 "Formulario de compra" "Formulario de compra de entradas" \
"FORMULARIO de C. Solo usuarios con sesión (esta_logueado()). Depende de la tarea de tablas de B.
- [ ] Elegir acto y cantidad
- [ ] Comprobar en el servidor que no se supera el aforo (sumar cantidades de wp_portal_entradas)
- [ ] \$wpdb->insert() en wp_portal_entradas con get_current_user_id()
$REGLAS"

editar 19 "Listado Mis entradas" "Listado Mis entradas" \
"LISTADO de C.
- [ ] Entradas de get_current_user_id() unidas con wp_portal_actos (título, fecha, lugar, cantidad)
- [ ] Solo con sesión iniciada"

editar 20 "Suscripción a recibir" "Suscripción a recibir información por correo (user_meta)" \
"- [ ] Alta y baja de la suscripción del usuario registrado
- [ ] Guardar con update_user_meta(\$id, 'portal_suscrito', 1 / 0)"

# ---------- D ----------
editar 26 "Formulario para guardar puntuación" "Formulario para guardar la puntuación del juego" \
"FORMULARIO de D. Depende de la tarea de tablas de B.
- [ ] Al acabar la partida, formulario con nombre y puntuación
- [ ] Envío con fetch (POST) a src/api/guardar_puntuacion.php
- [ ] Ese fichero valida y hace \$wpdb->insert() en wp_portal_puntuaciones (user_id = get_current_user_id() o NULL)
$REGLAS"

editar 27 "Ranking de puntuaciones" "Ranking de puntuaciones (asíncrono)" \
"LISTADO de D.
- [ ] src/api/ranking.php: 10 mejores de wp_portal_puntuaciones en JSON
- [ ] JS: cargar con fetch y pintar la tabla con el DOM; refrescar tras guardar una puntuación"

echo "4/4 Tareas nuevas"
FIELD_ID=$(gh project field-list "$PROJ" --owner "$OWNER" --format json \
   --jq '.fields[] | select(.name=="Persona") | .id')
opcion() {
  gh project field-list "$PROJ" --owner "$OWNER" --format json \
     --jq ".fields[] | select(.name==\"Persona\") | .options[] | select(.name==\"${GRUPO[$1]}\") | .id"
}
crear() {
  local p="$1" titulo="$2" cuerpo="$3" url item
  if gh issue list --repo "$REPO" --state all --search "\"$titulo\" in:title" --json title \
       --jq '.[].title' | grep -qxF "$titulo"; then
    echo "  ya existe: $titulo"; return
  fi
  url=$(gh issue create --repo "$REPO" --title "$titulo" --body "$cuerpo" --label "persona-$p")
  item=$(gh project item-add "$PROJ" --owner "$OWNER" --url "$url" --format json --jq .id)
  gh project item-edit --id "$item" --project-id "$PROJ_ID" \
     --field-id "$FIELD_ID" --single-select-option-id "$(opcion "$p")" > /dev/null
  echo "  [$p] $titulo"
}

crear A "Entorno XAMPP + WordPress funcionando en los 4 ordenadores" \
"Semana 1.
- [ ] Cada persona sigue docs/INSTALACION.md (XAMPP, WordPress, clonar en htdocs, config.php)
- [ ] Cada uno abre portal.php sin errores
- [ ] Cada uno tiene un usuario administrador y uno Suscriptor de prueba"

crear B "Confirmar con la profesora dónde guardar actos, entradas y puntuaciones" \
"Ella indicó usuarios y contraseñas en la BD de WordPress. Confirmar que las tablas propias
(wp_portal_actos, wp_portal_entradas, wp_portal_puntuaciones) también pueden ir en esa BD.
Apuntar la respuesta en docs/BASE_DE_DATOS.md."

echo
echo "Hecho. Tablero: https://github.com/users/$OWNER/projects/$PROJ"

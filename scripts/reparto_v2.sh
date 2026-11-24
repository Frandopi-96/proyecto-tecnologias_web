#!/usr/bin/env bash
# Reparto v2: todos practican toda la asignatura (formulario + listado asíncrono + Canvas + página).
# Actualiza títulos y descripciones de las tareas, mueve el aside de D a C y crea las tareas nuevas.
# Funciona tanto si se ejecutó antes actualizar_tareas.sh como si no.
# Se puede ejecutar más de una vez sin duplicar nada.
# Uso (desde la raíz del repo):  bash scripts/reparto_v2.sh

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

echo "1/5 Tablero"
PROJ=$(gh project list --owner "$OWNER" --format json \
   --jq ".projects[] | select(.title==\"$TITULO\") | .number")
[ -n "$PROJ" ] || { echo "No encuentro el tablero \"$TITULO\". Mira: gh project list --owner $OWNER"; exit 1; }
PROJ_ID=$(gh project view "$PROJ" --owner "$OWNER" --format json --jq .id)
FIELD_ID=$(gh project field-list "$PROJ" --owner "$OWNER" --format json \
   --jq '.fields[] | select(.name=="Persona") | .id')
declare -A OPT
for p in A B C D; do
  OPT[$p]=$(gh project field-list "$PROJ" --owner "$OWNER" --format json \
     --jq ".fields[] | select(.name==\"Persona\") | .options[] | select(.name==\"${GRUPO[$p]}\") | .id")
done
if ! gh project field-list "$PROJ" --owner "$OWNER" --format json --jq '.fields[].name' | grep -qx "Rama"; then
  gh project field-create "$PROJ" --owner "$OWNER" --name "Rama" --data-type TEXT > /dev/null
  echo "  campo Rama creado"
fi

# Cambia el campo Persona de una issue del tablero
poner_persona() {
  local n="$1" p="$2" item
  item=$(gh project item-list "$PROJ" --owner "$OWNER" -L 200 --format json \
     --jq ".items[] | select(.content.number==$n) | .id")
  if [ -z "$item" ]; then
    item=$(gh project item-add "$PROJ" --owner "$OWNER" \
       --url "https://github.com/$REPO/issues/$n" --format json --jq .id)
  fi
  gh project item-edit --id "$item" --project-id "$PROJ_ID" \
     --field-id "$FIELD_ID" --single-select-option-id "${OPT[$p]}" > /dev/null
}

# editar NUMERO "inicio título 1|inicio título 2|..." "Título nuevo" "Descripción"
editar() {
  local n="$1" viejos="$2" titulo="$3" cuerpo="$4" actual ok=0 v
  actual=$(gh issue view "$n" --repo "$REPO" --json title --jq .title)
  [ "$actual" == "$titulo" ] && ok=1
  IFS='|' read -ra LISTA <<< "$viejos"
  for v in "${LISTA[@]}"; do [[ "$actual" == "$v"* ]] && ok=1; done
  if [ $ok -eq 0 ]; then echo "  AVISO #$n se llama \"$actual\": no la toco"; return; fi
  gh issue edit "$n" --repo "$REPO" --title "$titulo" --body "$cuerpo" > /dev/null
  echo "  #$n $titulo"
}

# crear PERSONA "Título" "Descripción"  (no duplica)
crear() {
  local p="$1" titulo="$2" cuerpo="$3" url item
  if gh issue list --repo "$REPO" --state all -L 200 --json title --jq '.[].title' | grep -qxF "$titulo"; then
    echo "  ya existe: $titulo"; return
  fi
  url=$(gh issue create --repo "$REPO" --title "$titulo" --body "$cuerpo" --label "persona-$p")
  item=$(gh project item-add "$PROJ" --owner "$OWNER" --url "$url" --format json --jq .id)
  gh project item-edit --id "$item" --project-id "$PROJ_ID" \
     --field-id "$FIELD_ID" --single-select-option-id "${OPT[$p]}" > /dev/null
  echo "  [$p] $titulo"
}

REGLAS='Reglas: docs/BASE_DE_DATOS.md (wp_unslash, $wpdb->prepare, permisos en el servidor, htmlspecialchars).'
VAL_JS='- [ ] Validación en el navegador con eventos del DOM (input / submit) y mensajes junto a cada campo'
ASYNC='- [ ] JS: fetch, pintar con el DOM, mensaje si la lista está vacía o si falla la petición'

echo "2/5 Persona A"
editar 1 "Controlador portal.php" "Controlador portal.php, cargar_wp.php y estructura de partials" \
"Base para el resto: hacerlo en la semana 1.
- [ ] src/cargar_wp.php: incluye config.php y WP_PATH/wp-load.php; funciones es_admin() y esta_logueado() (docs/BASE_DE_DATOS.md)
- [ ] src/portal.php: controlador con ?action= que incluye cargar_wp.php
- [ ] partials header, menu y footer
- [ ] Mensajes de error entre el menú y el contenido"

editar 2 "Formulario de registro" "Formulario de registro de usuarios (WordPress)" \
"FORMULARIO de A.
- [ ] Campos: usuario, email, contraseña y repetir contraseña
$VAL_JS
- [ ] Validación en PHP (vacíos, email, contraseñas iguales)
- [ ] Crear con wp_insert_user() y rol subscriber; si devuelve WP_Error, mostrar get_error_message()
$REGLAS"

editar 3 "Login, logout|Login y logout" "Login y logout con WordPress" \
"- [ ] Formulario de login
- [ ] wp_signon() ANTES de imprimir HTML; después wp_set_current_user()
- [ ] wp_logout() en ?action=exit
- [ ] Sin \$_SESSION para el login: la sesión la gestiona WordPress"

editar 4 "Roles y menú" "Roles y menú según el rol (WordPress)" \
"Anónimo = sin sesión; registrado = subscriber; administrador = administrator.
- [ ] Menú distinto para cada rol
- [ ] Comprobar permisos también en portal.php y en src/api/ con es_admin() / esta_logueado()
- [ ] Probar los 3 roles"

editar 5 "Personalizar el entorno|Perfil:" "Perfil: personalización y avatar dibujado en Canvas" \
"CANVAS de A (como canvasCara del boletín B4).
- [ ] Preferencia del usuario (p. ej. tema claro/oscuro) con update_user_meta() / get_user_meta()
- [ ] Canvas para dibujar el avatar con eventos de ratón (y botón limpiar)
- [ ] Guardar con canvas.toDataURL('image/png') en user_meta 'portal_avatar' y mostrarlo en el menú"

editar 6 "Listado de usuarios" "Listado de usuarios asíncrono (solo admin)" \
"LISTADO de A.
- [ ] src/api/usuarios.php: solo si es_admin() (si no, 403 + error en JSON); usuario, email y rol con get_users()
$ASYNC"

crear A "Página principal" \
"PÁGINA ESTÁTICA de A.
- [ ] Presentación del portal y próximos actos destacados
- [ ] HTML semántico (header, main, section) y CSS con las variables comunes
- [ ] Accesible: textos alternativos, jerarquía de títulos"

echo "3/5 Persona B"
editar 9 "Datos de ejemplo de actos|Crear tablas propias" "Crear tablas propias en la BD de WordPress (instalar.php)" \
"Bloquea a C y D: hacerlo en la semana 1.
- [ ] src/instalar.php: solo admin; crea wp_portal_actos, wp_portal_entradas (con campo firma) y wp_portal_puntuaciones (esquema en docs/BASE_DE_DATOS.md)
- [ ] Insertar 5-10 actos de ejemplo si la tabla está vacía
- [ ] Ejecutarlo dos veces no debe dar error ni duplicar
- [ ] Avisar al grupo cuando esté en main"

editar 10 "Formulario de alta" "Formulario de alta y modificación de actos" \
"FORMULARIO de B. Solo administrador (es_admin()).
- [ ] Campos: título, descripción, fecha, lugar, precio, aforo, imagen
$VAL_JS
- [ ] Validación en PHP (fecha válida, precio >= 0, aforo > 0)
- [ ] \$wpdb->insert() / \$wpdb->update() en wp_portal_actos; comprobar \$wpdb->last_error
$REGLAS"

editar 11 "Borrado de actos" "Borrado de actos" \
"- [ ] Solo administrador
- [ ] Confirmación en el navegador antes de borrar (evento del DOM)
- [ ] \$wpdb->delete() en wp_portal_actos"

editar 12 "Listado de actos" "Listado de actos asíncrono" \
"LISTADO de B. Visible para todos los roles.
- [ ] src/api/actos.php: actos ordenados por fecha en JSON
$ASYNC
- [ ] Si es admin: enlaces Modificar y Borrar. Si tiene sesión: enlace Comprar (lo usa C)"

editar 13 "Búsqueda de actos" "Búsqueda de actos por fecha y lugar (asíncrona)" \
"- [ ] src/api/actos.php acepta fecha y lugar y filtra con \$wpdb->prepare
- [ ] Eventos del DOM en los filtros: actualizar el listado sin recargar"

crear B "Gráfico en Canvas de entradas vendidas por acto (admin)" \
"CANVAS de B (como los gráficos del boletín B4). Solo administrador.
- [ ] src/api/ventas.php: suma de cantidad por acto en JSON (403 si no es admin)
- [ ] Gráfico de barras dibujado en Canvas con los datos recibidos por fetch, con títulos y ejes
- [ ] Texto alternativo o tabla con los mismos datos (accesibilidad)"

crear B "Página Qui som" \
"PÁGINA ESTÁTICA de B.
- [ ] Presentación del grupo y del portal
- [ ] HTML semántico y CSS con las variables comunes; accesible"

echo "4/5 Persona C"
editar 16 "CSS global" "Variables CSS comunes (colores, fuentes) y estilo base" \
"Semana 1: todos las usan.
- [ ] css/comun.css con variables (--color-principal, --fuente-texto...) en :root
- [ ] Estilo base: tipografía, enlaces, formularios, tablas, mensajes de error
- [ ] Contraste suficiente (accesibilidad)
- [ ] Avisar al grupo de los nombres de las variables"

editar 17 "Páginas principal|Página Política legal" "Página Política legal" \
"PÁGINA ESTÁTICA de C. (La principal es de A, Qui som de B y Cookies de D.)
- [ ] Aviso legal y protección de datos
- [ ] HTML semántico y CSS con las variables comunes; accesible"

editar 18 "Formulario de compra" "Formulario de compra de entradas" \
"FORMULARIO de C. Solo usuarios con sesión (esta_logueado()). Depende de la tarea de tablas de B.
- [ ] Elegir acto y cantidad
$VAL_JS
- [ ] Comprobar en PHP que no se supera el aforo (sumar cantidades de wp_portal_entradas)
- [ ] \$wpdb->insert() en wp_portal_entradas con get_current_user_id()
$REGLAS"

editar 19 "Listado Mis entradas" "Listado Mis entradas asíncrono" \
"LISTADO de C.
- [ ] src/api/mis_entradas.php: entradas de get_current_user_id() con título, fecha y lugar del acto (403 si no hay sesión)
$ASYNC"

editar 20 "Suscripción a recibir" "Suscripción a recibir información por correo (user_meta)" \
"- [ ] Alta y baja de la suscripción del usuario registrado
- [ ] Guardar con update_user_meta(\$id, 'portal_suscrito', 1 / 0)"

editar 23 "Aside con información" "Aside con información asíncrona (tiempo, noticias...)" \
"Pasa de D a C en el reparto v2.
- [ ] Datos de una API externa gratuita (tiempo de Castelló, noticias culturales...) con fetch
- [ ] Mostrar 'Cargando...' y un mensaje si falla la petición
- [ ] Aparece en todas las páginas (partial)"
poner_persona 23 C
gh issue edit 23 --repo "$REPO" --remove-label persona-D --add-label persona-C > /dev/null
echo "  #23 movida a C"

crear C "Firma del comprador en Canvas" \
"CANVAS de C (como canvas_firma del boletín B4).
- [ ] Canvas en el formulario de compra para firmar con el ratón (y botón limpiar)
- [ ] Validar en el navegador que hay firma antes de enviar
- [ ] canvas.toDataURL() en un campo oculto; PHP guarda el PNG en src/media/firmas/<id>.png y la ruta en wp_portal_entradas.firma"

echo "5/5 Persona D"
editar 24 "Galería" "Galería con imágenes que cambian asíncronamente" \
"- [ ] Lista de imágenes pedida con fetch (JSON) y cambio automático cada pocos segundos
- [ ] Botones anterior / siguiente / pausa (eventos del DOM)
- [ ] Textos alternativos en todas las imágenes"

editar 25 "Juego inventado" "Juego inventado en Canvas con puntuación" \
"CANVAS de D.
- [ ] Juego propio con animación (requestAnimationFrame) y eventos de teclado y ratón
- [ ] Puntuación visible y fin de partida
- [ ] Instrucciones de cómo se juega"

editar 26 "Formulario para guardar" "Formulario para guardar la puntuación del juego" \
"FORMULARIO de D. Depende de la tarea de tablas de B.
- [ ] Al acabar la partida, formulario con nombre y puntuación
$VAL_JS
- [ ] Envío con fetch (POST) a src/api/guardar_puntuacion.php, que valida y hace \$wpdb->insert() en wp_portal_puntuaciones (user_id = get_current_user_id() o NULL)
$REGLAS"

editar 27 "Ranking de puntuaciones" "Ranking de puntuaciones asíncrono" \
"LISTADO de D.
- [ ] src/api/ranking.php: 10 mejores de wp_portal_puntuaciones en JSON
$ASYNC
- [ ] Refrescar el ranking tras guardar una puntuación"

crear D "Página Cookies" \
"PÁGINA ESTÁTICA de D.
- [ ] Política de cookies (qué cookies usa WordPress y el portal)
- [ ] Aviso de cookies con botón Aceptar (evento del DOM) que no vuelve a salir tras aceptar
- [ ] HTML semántico y CSS con las variables comunes; accesible"

echo
echo "Hecho. Tablero: https://github.com/users/$OWNER/projects/$PROJ"

#!/usr/bin/env bash
# Crea en GitHub las tareas del reparto (issues) y un tablero (Project)
# con un campo "Persona" para agruparlas en 4 grupos: A, B, C y D.
# Requisitos: gh instalado y autenticado con permiso de proyectos:
#   gh auth login
#   gh auth refresh -s project
# Uso (desde la raíz del repo):  bash scripts/crear_tablero.sh
# Ejecutar UNA sola vez: si se repite, duplica las tareas.

set -euo pipefail

OWNER="Frandopi-96"
REPO="$OWNER/proyecto-tecnologias_web"
TITULO="Portal cultural - reparto"

# Nombre de cada grupo (cambiad los nombres entre paréntesis si queréis)
declare -A GRUPO=(
  [A]="A - Usuarios y sesiones"
  [B]="B - Actos (admin)"
  [C]="C - Entradas y diseño"
  [D]="D - Asincronía y Canvas"
)
declare -A COLOR=([A]="1f77b4" [B]="2ca02c" [C]="ff7f0e" [D]="9467bd")

echo "1/4 Etiquetas persona-A..D"
for p in A B C D; do
  gh label create "persona-$p" --repo "$REPO" --color "${COLOR[$p]}" \
     --description "${GRUPO[$p]}" --force
done

echo "2/4 Tablero"
PROJ=$(gh project create --owner "$OWNER" --title "$TITULO" --format json --jq .number)
PROJ_ID=$(gh project view "$PROJ" --owner "$OWNER" --format json --jq .id)
gh project link "$PROJ" --owner "$OWNER" --repo "$REPO" || echo "  (no se pudo enlazar al repo; hacedlo desde la web)"

echo "3/4 Campo Persona"
OPCIONES="${GRUPO[A]},${GRUPO[B]},${GRUPO[C]},${GRUPO[D]}"
gh project field-create "$PROJ" --owner "$OWNER" --name "Persona" \
   --data-type SINGLE_SELECT --single-select-options "$OPCIONES" > /dev/null
FIELD_ID=$(gh project field-list "$PROJ" --owner "$OWNER" --format json \
   --jq '.fields[] | select(.name=="Persona") | .id')
declare -A OPT
for p in A B C D; do
  OPT[$p]=$(gh project field-list "$PROJ" --owner "$OWNER" --format json \
     --jq ".fields[] | select(.name==\"Persona\") | .options[] | select(.name==\"${GRUPO[$p]}\") | .id")
done

# crear PERSONA "Título" "Descripción"
crear() {
  local p="$1" titulo="$2" cuerpo="$3" url item
  url=$(gh issue create --repo "$REPO" --title "$titulo" --body "$cuerpo" --label "persona-$p")
  item=$(gh project item-add "$PROJ" --owner "$OWNER" --url "$url" --format json --jq .id)
  gh project item-edit --id "$item" --project-id "$PROJ_ID" \
     --field-id "$FIELD_ID" --single-select-option-id "${OPT[$p]}" > /dev/null
  echo "  [$p] $titulo"
}

echo "4/4 Tareas"
# ---------- A: usuarios y sesiones ----------
crear A "Controlador portal.php y estructura de partials" "Controlador con ?action=, header, menu y footer. Base para el resto: hacerlo primero."
crear A "Formulario de registro de usuarios" "FORMULARIO de A. Validación en servidor y mensajes de error."
crear A "Login, logout y sesiones" "Autenticación, caducidad de sesión y cierre de sesión."
crear A "Roles y menú según el rol" "Anónimo, registrado y administrador. Comprobar permisos también en el controlador, no solo ocultar el menú."
crear A "Personalizar el entorno del usuario registrado" "Preferencias del usuario (p. ej. tema o actos favoritos)."
crear A "Listado de usuarios (solo admin)" "LISTADO de A."
crear A "Memoria: prototipado e implementación de A" "Capítulo 3 (arquitectura de la información y boceto) y su parte del capítulo 4."
crear A "Pruebas y accesibilidad de la parte A" "Probar con los 3 roles y anotar errores corregidos en docs/memoria/INDICE.md."

# ---------- B: actos ----------
crear B "Datos de ejemplo de actos" "src/recursos/actos_ejemplo.json con fecha, lugar, precio, aforo e imagen."
crear B "Formulario de alta y modificación de actos" "FORMULARIO de B. Solo administrador."
crear B "Borrado de actos" "Solo administrador, con confirmación."
crear B "Listado de actos" "LISTADO de B. Visible para todos los roles."
crear B "Búsqueda de actos por fecha y lugar (asíncrona)" "fetch al servidor y actualización del listado con el DOM sin recargar."
crear B "Memoria: evaluación (coordinar) e implementación de B" "Capítulo 5 con las pruebas de todos y su parte del capítulo 4."
crear B "Pruebas y accesibilidad de la parte B" "Probar con los 3 roles y anotar errores corregidos."

# ---------- C: entradas y diseño ----------
crear C "CSS global, paleta y fuentes" "Estilos comunes del portal, sin librerías externas."
crear C "Páginas principal, qui som, política legal y cookies" "Páginas obligatorias del enunciado."
crear C "Formulario de compra de entradas" "FORMULARIO de C. Solo usuarios registrados; controlar el aforo."
crear C "Listado Mis entradas" "LISTADO de C. Entradas compradas por el usuario."
crear C "Suscripción a recibir información por correo" "Alta y baja de la suscripción del usuario registrado."
crear C "Memoria: diseño e implementación de C" "Capítulo 2 (requisitos, casos de uso, colores, fuentes, software) y su parte del capítulo 4."
crear C "Pruebas y accesibilidad de la parte C" "Probar con los 3 roles y anotar errores corregidos."

# ---------- D: asincronía y Canvas ----------
crear D "Aside con información asíncrona" "Tiempo, tráfico o noticias cargados con fetch, con gestión de errores."
crear D "Galería con imágenes que cambian asíncronamente" "Imágenes cargadas y rotadas sin recargar la página."
crear D "Juego inventado en Canvas con puntuación" "Uso de eventos del DOM (teclado y ratón)."
crear D "Formulario para guardar puntuación o valoración" "FORMULARIO de D. Envío asíncrono al servidor."
crear D "Ranking de puntuaciones (asíncrono)" "LISTADO de D."
crear D "Memoria: implementación de D" "Su parte del capítulo 4: asincronía, Canvas y eventos."
crear D "Pruebas y accesibilidad de la parte D" "Probar con los 3 roles y anotar errores corregidos."

echo
echo "Hecho. Tablero: https://github.com/users/$OWNER/projects/$PROJ"

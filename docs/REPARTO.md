# Reparto del trabajo

Cada persona tiene **1 formulario + 1 listado** (requisito del enunciado) y una parte "transversal".
En la defensa, cada uno presenta su parte.

| Persona | Área | Formulario | Listado | Además |
|---|---|---|---|---|
| A — _nombre_ | Usuarios y sesiones | Registro / perfil (personalizar entorno) | Usuarios (solo admin) | Controlador `portal.php`, login/logout, sesiones, control de roles, menú según rol |
| B — _nombre_ | Actos (admin) | Alta / modificación de acto | Actos con búsqueda por fecha y lugar (asíncrona con `fetch`) | Borrado de actos, validación de datos |
| C — _nombre_ | Entradas y suscripción | Compra de entradas | "Mis entradas" del usuario | Suscripción al correo, CSS global y paleta, páginas qui som / legal / cookies |
| D — _nombre_ | Asincronía y Canvas | Guardar puntuación / valoración de un acto | Ranking de puntuaciones (asíncrono) | Aside asíncrono (tiempo, noticias…), galería asíncrona, juego en Canvas |

## Trabajo común (todos)
- Gestión de errores y comentarios en el código de su parte.
- Accesibilidad de sus páginas (etiquetas `label`, `alt`, contraste, navegación con teclado).
- Pruebas de su parte (roles y eventos) para el capítulo de Evaluación.
- Su sección del capítulo de Implementación en la memoria.

## Orden recomendado
1. **Semana 1:** A deja el controlador, `header`/`menu`/`footer` y el login funcionando; C fija CSS y paleta. B y D empiezan con datos de prueba en `src/recursos/`.
2. **Semana 2:** cada uno su formulario + listado; D aside, galería y juego.
3. **Semana 3:** integración, pruebas cruzadas de roles, accesibilidad y memoria.

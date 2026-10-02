# Frontend Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Bitacora 2026-09-29 Las Tablas]], [[Bitacora 2026-09-30 Las Tablas]], [[Inicio Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Contacto Las Tablas]], [[Assets Las Tablas]]

Estado: pulido visual avanzado.

## Archivos principales

- `assets/app.js`
- `assets/styles/app.css`
- `templates/base.html.twig`

## Avances

- Carrusel automatico del hero.
- Busqueda en listados.
- Marcado de novedades vistas con `localStorage`.
- Cuenta regresiva para etiquetas.
- Filtros de menu.
- Cards accesibles por teclado.
- Copiado de telefono.
- Confirmacion de eliminacion.
- Estilos generales para home, admin, cards, menu, blog, contacto y autenticacion.
- Scroll del home ajustado para usar desplazamiento nativo suave y evitar bloqueos visuales.
- Vistas internas de menu, blog y novedades sin transicion tipo scroll por secciones.
- Cards de blog/novedades con comportamiento responsive diferenciado.
- En computadora, Blog/Novedades muestran solo tres cards estaticas, alineadas y sin arrastre.
- En movil, Blog/Novedades conservan abanico con arrastre tactil.
- Card `Ver mas` eliminada del abanico y convertida en CTA de seccion.
- Carrusel `Lo mas pedido` redisenado como deck de cards con profundidad.
- Modales de blog/novedades como panel lateral animado desde la derecha.
- Botones de contrasena en login/registro para mostrar u ocultar el valor.
- Menu publico con filtros por menu y categoria sin contaminar estado entre manana/tarde.
- Categorias del menu plegables/desplegables.
- Se eliminaron controles inferiores de siguiente/anterior en publicaciones y deck de platillos.
- Menu movil corregido para abrir y cerrar.

## Pendiente

- Prueba visual final con el cliente en escritorio y movil.
- Prueba en celular fisico del abanico tactil.
- Revisar que no haya textos desbordados con contenido real nuevo.
- Revisar dependencias externas Bootstrap/CDN si se desea trabajar 100% local.


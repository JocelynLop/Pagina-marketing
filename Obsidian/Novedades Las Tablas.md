# Novedades Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Bitacora 2026-09-29 Las Tablas]], [[Bitacora 2026-09-30 Las Tablas]], [[Administracion Las Tablas]], [[Datos Las Tablas]], [[Assets Las Tablas]], [[Frontend Las Tablas]], [[Legacy Publicaciones Las Tablas]]

Estado: modulo construido y pulido visualmente.

## Archivos principales

- `src/Controller/NoveltyController.php`
- `templates/novelties/index.html.twig`
- `templates/novelties/manage.html.twig`
- `templates/novelties/edit.html.twig`
- `templates/novelties/_form_fields.html.twig`
- `templates/novelties/_card.html.twig`

## Avances

- Ruta publica `/novedades`.
- Panel admin `/usuario/novedades`.
- Crear, editar y eliminar novedades.
- Campos de titulo, categoria, resumen, contenido, imagen, etiqueta personalizada, vigencia de etiqueta y caducidad.
- Las novedades vencidas se ocultan al cliente, pero siguen disponibles para admin.
- Compatibilidad con rutas viejas de publicaciones mediante redireccion.
- Indicador de novedad vista usando `localStorage`.
- Cuenta regresiva para etiquetas activas.
- Comentarios en novedades usando la misma interfaz visual de comentarios.
- En home escritorio se muestran tres cards estaticas, alineadas y sin arrastre.
- En home movil se conserva el abanico con arrastre tactil.
- Se quitaron los controles inferiores y el texto de ayuda del abanico.
- CTA `Ver mas novedades` queda fuera del abanico.
- Modal lateral que aparece desde derecha hacia izquierda.
- Modal con imagen arriba, informacion debajo, reacciones y comentarios.
- Boton de salida del modal reducido a una `X` arriba a la derecha.
- Vista publica alineada con colores y estilo del home.
- Boton `Ver novedad` abre modal correctamente desde el home.

## Pendiente

- Confirmar si los comentarios en novedades son realmente deseados o si deben limitarse solo al blog.
- Validar flujo completo de novedad expirada en cliente y admin.
- Validar abanico tactil en celular fisico.


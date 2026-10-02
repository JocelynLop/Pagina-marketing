# Blog Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Bitacora 2026-09-29 Las Tablas]], [[Bitacora 2026-09-30 Las Tablas]], [[Inicio Las Tablas]], [[Administracion Las Tablas]], [[Autenticacion Las Tablas]], [[Datos Las Tablas]], [[Assets Las Tablas]], [[Frontend Las Tablas]]

Estado: modulo construido y pulido visualmente.

## Archivos principales

- `src/Controller/BlogController.php`
- `templates/blog/index.html.twig`
- `templates/blog/show.html.twig`
- `templates/blog/manage.html.twig`
- `templates/blog/edit.html.twig`
- `templates/blog/_form_fields.html.twig`
- `templates/blog/_comments.html.twig`
- `templates/blog/_card_modal.html.twig`
- `var/data/blog_reactions.json`

## Avances

- Ruta publica `/blog`.
- Vista de entrada individual `/blog/{id}`.
- Panel admin `/usuario/blog`.
- Crear, editar y eliminar entradas.
- Corazones para entradas.
- Comentarios disponibles solo para usuarios registrados.
- Admin puede ver y eliminar comentarios.
- Entradas iniciales con imagenes de `public/img/imagenes/blog`.
- En home escritorio se muestran tres cards estaticas, alineadas y sin arrastre.
- En home movil se conserva el abanico con arrastre tactil.
- Se quitaron los controles inferiores y el texto de ayuda del abanico.
- CTA `Ver mas historias` queda fuera del abanico.
- Modal lateral que aparece desde derecha hacia izquierda.
- Modal con imagen arriba, informacion debajo, reacciones y comentarios.
- Boton de salida del modal reducido a una `X` arriba a la derecha.
- Vista publica alineada con colores y estilo del home.
- Boton `Ver historia` abre modal correctamente desde el home.

## Pendiente

- Validar que el comportamiento de comentar redirija correctamente a login/registro cuando el usuario no esta autenticado.
- Revisar si el blog debe abrir en modal, pagina individual o ambas experiencias.
- Validar abanico tactil en celular fisico.


# Inicio Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Quienes Somos Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Contacto Las Tablas]], [[Frontend Las Tablas]]

Estado: pulido visual avanzado.

## Archivos principales

- `src/Controller/RestaurantController.php`
- `templates/restaurant/index.html.twig`
- `templates/restaurant/_contact.html.twig`
- `assets/styles/app.css`
- `assets/app.js`

## Avances

- Home reorientada a Las Tablas.
- Hero con platillos destacados.
- Carrusel conectado a items destacados del menu.
- Navegacion publica con secciones: Quienes somos, Novedades, Blog, Menu y Contactanos.
- Enlace admin visible para usuarios con rol administrador.
- Botones principales del hero retirados segun solicitud; se conserva `Descubrir` como guia hacia el contenido.
- Novedades y Blog muestran tres cards estaticas en computadora.
- Novedades y Blog conservan abanico tactil en movil.
- Cards de publicaciones alineadas al mismo nivel en escritorio.
- Botones `Ver mas novedades` y `Ver mas historias` ubicados como CTA de seccion.
- Botones `Ver novedad` y `Ver historia` abren modal correctamente.
- Se corrigio el fondo de `Quienes somos` para no mezclarse con la seccion anterior.
- Menu movil corregido para abrir y cerrar.

## Pendiente

- Validar responsive completo en celular fisico.
- Confirmar que los platillos destacados sean los mas estrellados cuando existan reacciones.
- Pulir textos finales si el restaurante quiere un tono mas formal o mas familiar.


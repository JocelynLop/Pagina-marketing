# Hecho Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Checklist Las Tablas]], [[Pendientes Las Tablas]], [[Inicio Las Tablas]], [[Administracion Las Tablas]], [[Datos Las Tablas]]

Este nodo marca lo que ya esta construido y tiene una estructura clara dentro del proyecto. No significa que todo este probado en navegador; significa que el modulo ya existe, esta separado por responsabilidad y tiene archivos principales identificables.

## Publico

- [x] [[Inicio Las Tablas]]: home reorientada a Las Tablas con hero, navegacion y secciones principales.
- [x] [[Quienes Somos Las Tablas]]: descripcion del restaurante, CTA a Maps, CTA a menu y tarjetas de platillos.
- [x] [[Novedades Las Tablas]]: vista publica de novedades, tarjetas, etiqueta personalizada, countdown y estado de novedad vista.
- [x] [[Menu Las Tablas]]: vista publica con platillos/bebidas, tipos manana/tarde, filtros y reacciones.
- [x] [[Blog Las Tablas]]: listado, entrada individual, corazones y comentarios.
- [x] [[Contacto Las Tablas]]: datos visibles, botones de accion y telefono copiable.
- [x] Home pulido con degradado vino/rojo hacia negro y transiciones de scroll mas limpias.
- [x] Carrusel de `Lo mas pedido` redisenado como deck visual de platillos destacados.
- [x] Cards de blog/novedades y `Ver mas` integradas con estilo mas elegante y animacion contenida.
- [x] Modales publicos de blog y novedades como panel lateral desde la derecha con imagen, informacion y comentarios ordenados.
- [x] Home con Novedades y Blog en tres cards estaticas para computadora.
- [x] Home con Novedades y Blog en abanico deslizable para movil.
- [x] Cards de publicaciones alineadas al mismo nivel en escritorio.
- [x] Botones `Ver novedad` y `Ver historia` abren sus modales desde el home.
- [x] Menu movil corregido para abrir y cerrar correctamente.
- [x] Botones principales del hero retirados segun solicitud; queda la navegacion por `Descubrir`.

## Administracion

- [x] [[Administracion Las Tablas]]: sidebar reutilizable y mensajes administrativos.
- [x] Admin de novedades: crear, editar, eliminar y listar.
- [x] Admin de menu: crear, editar, eliminar, listar y ver conteo de estrellas.
- [x] Admin de menu: asignar tipo de menu y categoria/filtro al crear o editar items.
- [x] Admin de blog: crear, editar, eliminar entradas y eliminar comentarios.
- [x] Admin de contacto: editar telefono, direccion, Maps, Facebook, correo, mensaje y WhatsApp pendiente.

## Datos y seguridad

- [x] [[Datos Las Tablas]]: `RestaurantDataStore` centraliza novedades, menu, blog, contacto, comentarios y reacciones.
- [x] [[Autenticacion Las Tablas]]: login, logout, registro y proteccion de rutas `/usuario`.
- [x] Registro con fecha de nacimiento en formato `DD/MM/AAAA`.
- [x] Reacciones anonimas para menu y blog mediante identificador de visitante.
- [x] Login y registro redisenados con estilo consistente del sitio y botones para mostrar/ocultar contrasena.

## Frontend y contenido

- [x] [[Frontend Las Tablas]]: carrusel, busqueda, filtros, countdown, cards accesibles, copiar telefono y confirmacion de eliminacion.
- [x] Menu publico con tabs de manana/tarde, filtros por categoria y secciones plegables.
- [x] Filtros de menu independientes por tipo para evitar que un filtro de manana afecte tarde o viceversa.
- [x] [[Assets Las Tablas]]: imagenes locales separadas para menu, novedades y blog.
- [x] [[Legacy Publicaciones Las Tablas]]: publicaciones viejas conservadas como compatibilidad y redirigidas hacia novedades.
- [x] Verificaciones recientes: lint Twig, revision JS, diff-check CSS/JS, compilacion AssetMapper y revision visual desktop/movil.


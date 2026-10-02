# Avances Las Tablas

Fecha de corte: 2026-09-30

## Mapa de notas

- [[Inicio Las Tablas]]
- [[Quienes Somos Las Tablas]]
- [[Novedades Las Tablas]]
- [[Menu Las Tablas]]
- [[Blog Las Tablas]]
- [[Contacto Las Tablas]]
- [[Administracion Las Tablas]]
- [[Autenticacion Las Tablas]]
- [[Datos Las Tablas]]
- [[Assets Las Tablas]]
- [[Frontend Las Tablas]]
- [[Legacy Publicaciones Las Tablas]]
- [[Checklist Las Tablas]]
- [[Hecho Las Tablas]]
- [[Pendientes Las Tablas]]
- [[Bitacora 2026-09-29 Las Tablas]]
- [[Bitacora 2026-09-30 Las Tablas]]

## Resumen del proyecto

Sitio Symfony/Twig para el restaurante Las Tablas en Cosamaloapan. El objetivo es tener una experiencia publica atractiva para clientes y un panel administrativo sencillo para mantener novedades, menu, blog y datos de contacto sin depender de una base de datos tradicional por ahora.

El proyecto esta usando almacenamiento JSON en `var/data` mediante `src/Service/RestaurantDataStore.php`.

## Mapa general

- Publico: inicio, quienes somos, novedades, blog, menu y contactanos.
- Administracion: novedades, blog, menu, contacto, vista cliente y cierre de sesion.
- Autenticacion: login, logout y registro con fecha de nacimiento.
- Datos: JSON bajo `var/data`.
- Assets: imagenes locales en `public/img/imagenes`.
- Interacciones frontend: carrusel, busqueda, filtros, reacciones, copiar telefono y confirmacion de eliminacion.

Notas relacionadas: [[Inicio Las Tablas]], [[Administracion Las Tablas]], [[Datos Las Tablas]], [[Frontend Las Tablas]].

## Hecho y bien estructurado

Ver detalle en [[Hecho Las Tablas]].

- [x] Estructura publica principal del sitio: [[Inicio Las Tablas]], [[Quienes Somos Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]] y [[Contacto Las Tablas]].
- [x] Sidebar administrativo reutilizable: [[Administracion Las Tablas]].
- [x] Modulo de novedades con CRUD, caducidad, etiquetas y compatibilidad con publicaciones viejas: [[Novedades Las Tablas]].
- [x] Modulo de menu con CRUD, tipos manana/tarde, cards y reacciones de estrella: [[Menu Las Tablas]].
- [x] Modulo de blog con CRUD, comentarios y corazones: [[Blog Las Tablas]].
- [x] Contacto editable desde admin con telefono, direccion, Facebook, Google Maps y WhatsApp preparado: [[Contacto Las Tablas]].
- [x] Autenticacion base con login, logout y registro con fecha de nacimiento: [[Autenticacion Las Tablas]].
- [x] Servicio central de datos JSON para contenido del restaurante: [[Datos Las Tablas]].
- [x] Assets locales organizados por menu, novedades y blog: [[Assets Las Tablas]].
- [x] Interacciones frontend base: carrusel, filtros, busqueda, countdown, copiar telefono y confirmaciones: [[Frontend Las Tablas]].
- [x] Documentacion en Obsidian dividida por nodos conectados.
- [x] Pulido visual 2026-09-29 documentado en [[Bitacora 2026-09-29 Las Tablas]].
- [x] Pulido responsive y ajustes de publicaciones/menu 2026-09-30 documentados en [[Bitacora 2026-09-30 Las Tablas]].
- [x] Carrusel de `Lo mas pedido` redisenado como deck de platillos destacados.
- [x] Modales laterales de blog y novedades con imagen arriba, contenido debajo, comentarios y cierre con `X`.
- [x] Scroll del home limpiado para que no bloquee ni salte al seguir navegando.
- [x] Login y registro alineados visualmente con la identidad del sitio.
- [x] Novedades y Blog en home: tres cards estaticas en computadora y abanico tactil solo en movil.
- [x] Menu publico reorganizado por menu manana/tarde, categorias filtrables y secciones desplegables.
- [x] Admin de menu actualizado para asignar tipo de menu y categoria/filtro al crear o editar platillos.

## Pendientes principales

Ver detalle en [[Pendientes Las Tablas]].

- [x] Probar en navegador home, modal, cards, login y registro en escritorio/movil simulado.
- [x] Probar que `Ver novedad` abre el modal desde las cards del home.
- [ ] Probar administracion completa: crear, editar y eliminar en menu, novedades, blog y contacto.
- [ ] Confirmar contenido real del menu: nombres, precios, descripciones e imagenes.
- [ ] Decidir si novedades tendra comentarios o solo mostrara promociones/eventos.
- [ ] Decidir si el blog usara modal, pagina individual o ambos.
- [ ] Confirmar WhatsApp oficial antes de activar esa accion.
- [ ] Probar login de admin y permisos de todas las rutas `/usuario`.
- [ ] Definir si los JSON de `var/data` se versionan o quedan como datos locales.
- [ ] Decidir si se elimina el modulo legacy de publicaciones despues de validar novedades.
- [ ] Hacer revision final de textos, acentos y pulido visual.

## Secciones publicas

### Inicio

Estado: pulido visual avanzado.

Archivos principales:

- `src/Controller/RestaurantController.php`
- `templates/restaurant/index.html.twig`
- `templates/restaurant/_contact.html.twig`
- `assets/styles/app.css`
- `assets/app.js`

Avances:

- Home reorientada a Las Tablas.
- Hero con platillos destacados.
- Carrusel conectado a items destacados del menu.
- Navegacion publica con secciones: Quienes somos, Novedades, Blog, Menu y Contactanos.
- Enlace admin visible para usuarios con rol administrador.
- Scroll entre secciones suavizado con comportamiento nativo y sin bloqueo visual.
- Degradado general desde vino/rojo hasta negro en la progresion de secciones.
- Cards de blog/novedades y card `Ver mas` ajustadas a un estilo mas elegante.
- En escritorio, Novedades y Blog muestran solo tres cards estaticas, alineadas al mismo nivel.
- En movil, Novedades y Blog conservan el abanico deslizable con el dedo.
- Se quitaron los textos de ayuda y controles inferiores del abanico.
- Botones `Ver mas novedades` y `Ver mas historias` quedan como CTA de seccion, no como card.

Pendiente por validar:

- Revisar responsive final con el cliente en dispositivo real.
- Confirmar que los platillos destacados sean los mas estrellados cuando existan reacciones.
- Pulir textos finales si el restaurante quiere un tono mas formal o mas familiar.
- Validar el abanico en celular fisico.

### Quienes Somos

Estado: implementado en home.

Avances:

- Descripcion calida del restaurante/asador.
- Boton `Encuentranos aqui` hacia Google Maps.
- Boton `Ver menu`.
- Carrusel/deck visual con imagenes de los platillos mas pedidos.
- Botones principales con estilo mas cuidado, iconos y estados interactivos.

Pendiente:

- Confirmar imagenes definitivas para representar mejor desayuno, parrilla y bebidas.

### Novedades

Estado: modulo nuevo construido.

Archivos principales:

- `src/Controller/NoveltyController.php`
- `templates/novelties/index.html.twig`
- `templates/novelties/manage.html.twig`
- `templates/novelties/edit.html.twig`
- `templates/novelties/_form_fields.html.twig`
- `templates/novelties/_card.html.twig`

Avances:

- Ruta publica `/novedades`.
- Panel admin `/usuario/novedades`.
- Crear, editar y eliminar novedades.
- Campos de titulo, categoria, resumen, contenido, imagen, etiqueta personalizada, vigencia de etiqueta y caducidad.
- Las novedades vencidas se ocultan al cliente, pero siguen disponibles para admin.
- Compatibilidad con rutas viejas de publicaciones mediante redireccion.
- Indicador de novedad vista usando `localStorage`.
- Cuenta regresiva para etiquetas activas.
- Comentarios en novedades usando la misma interfaz visual de comentarios.
- Modal lateral desde derecha hacia izquierda con imagen arriba, informacion debajo y comentarios al final.
- Vista publica con estilo visual alineado a la pagina principal.

Pendiente por revisar:

- Confirmar si los comentarios en novedades son realmente deseados o si deben limitarse solo al blog.
- Validar flujo completo de novedad expirada en cliente y admin.

### Menu

Estado: modulo nuevo construido.

Archivos principales:

- `src/Controller/MenuController.php`
- `templates/menu/index.html.twig`
- `templates/menu/manage.html.twig`
- `templates/menu/edit.html.twig`
- `templates/menu/_form_fields.html.twig`
- `var/data/menu_reactions.json`

Avances:

- Ruta publica `/menu`.
- Panel admin `/usuario/menu`.
- Crear, editar y eliminar platillos o bebidas.
- Tipo de menu: manana o tarde.
- Cards con imagen, nombre, descripcion, precio, tipo y estrellas.
- Reacciones de estrella para visitantes no registrados mediante identificador anonimo.
- Filtros frontend por tipo de menu.
- Filtros por categoria debajo de los tabs de menu.
- Categorias plegables/desplegables.
- Estado de filtro separado por menu para que manana y tarde no se contaminen entre si.
- Cards mantienen el mismo ancho aunque una categoria tenga un solo platillo.
- Admin permite asignar categoria/filtro segun el menu seleccionado.
- Datos iniciales cargados desde imagenes locales de desayunos, comida/tarde y bebidas.
- Vista publica con tratamiento visual mas elegante, oscuro y consistente con el home.

Pendiente:

- Completar todos los platillos detectados en imagenes si faltan.
- Verificar precios finales contra el menu real.
- Probar CRUD admin completo con las nuevas categorias.
- Revisar horario automatico manana/tarde si queremos que el filtro inicial dependa de la hora.

### Blog

Estado: modulo nuevo construido.

Archivos principales:

- `src/Controller/BlogController.php`
- `templates/blog/index.html.twig`
- `templates/blog/show.html.twig`
- `templates/blog/manage.html.twig`
- `templates/blog/edit.html.twig`
- `templates/blog/_form_fields.html.twig`
- `templates/blog/_comments.html.twig`
- `templates/blog/_card_modal.html.twig`
- `var/data/blog_reactions.json`

Avances:

- Ruta publica `/blog`.
- Vista de entrada individual `/blog/{id}`.
- Panel admin `/usuario/blog`.
- Crear, editar y eliminar entradas.
- Corazones para entradas.
- Comentarios disponibles solo para usuarios registrados.
- Admin puede ver y eliminar comentarios.
- Entradas iniciales con imagenes de `public/img/imagenes/blog`.
- Modal lateral desde derecha hacia izquierda con imagen arriba, contenido debajo, reacciones y comentarios.
- Vista publica con estilo visual alineado a la pagina principal.

Pendiente:

- Validar que el comportamiento de comentar redirija correctamente a login/registro cuando el usuario no esta autenticado.
- Revisar si el blog debe abrir en modal, pagina individual o ambas experiencias.

### Contactanos

Estado: modulo editable construido.

Archivos principales:

- `src/Controller/RestaurantController.php`
- `templates/admin/contact.html.twig`
- `templates/restaurant/_contact.html.twig`

Avances:

- Datos editables desde `/usuario/contacto`.
- Telefono, direccion, Google Maps, Facebook, correo, mensaje y WhatsApp preparado.
- WhatsApp queda pendiente y no debe mostrarse activo hasta tener numero.
- Icono de telefono copia el numero al portapapeles y muestra retroalimentacion.
- Iconos de Facebook y ubicacion abren enlaces correspondientes.

Pendiente:

- Confirmar si se agregara correo publico.
- Confirmar numero de WhatsApp antes de activar esa accion.

## Administracion

Estado: estructura base lista.

Archivos principales:

- `templates/admin/_sidebar.html.twig`
- `templates/admin/_messages.html.twig`
- `templates/admin/contact.html.twig`
- Controladores admin dentro de `NoveltyController`, `MenuController`, `BlogController` y `RestaurantController`.

Avances:

- Sidebar reutilizable con modulos: Novedades, Blog, Menu, Contacto, Vista cliente y Cerrar sesion.
- Formularios con CSRF.
- Confirmacion antes de eliminar elementos.
- Mensajes flash reutilizables.

Pendiente:

- Revisar permisos admin en todos los endpoints `/usuario`.
- Confirmar si el admin necesita carga de archivos real o por ahora basta capturar rutas de imagen.

## Autenticacion y usuarios

Estado: login/registro en avance.

Archivos principales:

- `src/Controller/SecurityController.php`
- `src/Security/AppUser.php`
- `src/Security/UserStore.php`
- `src/Security/JsonUserProvider.php`
- `templates/security/login.html.twig`
- `templates/security/register.html.twig`
- `config/packages/security.yaml`

Avances:

- Login con provider JSON.
- Logout POST con CSRF.
- Registro publico en `/registro`.
- Registro pide fecha de nacimiento en formato `DD/MM/AAAA`.
- Validaciones: requerido, formato real, no futuro, contrasena minima y confirmacion.
- Usuarios guardados en JSON mediante `UserStore`.
- Rutas `/usuario` protegidas con `ROLE_ADMIN`.
- Login y registro redisenados con fondo, panel y controles consistentes con la pagina publica.
- Campos de contrasena con boton para mostrar/ocultar.

Pendiente:

- Probar login de admin configurado desde `.env`.
- Confirmar compatibilidad con usuarios antiguos sin fecha de nacimiento.
- Revisar textos con acentos antes del pulido final.

## Datos y almacenamiento

Estado: servicio central listo.

Archivo principal:

- `src/Service/RestaurantDataStore.php`

JSON previstos o ya usados:

- `var/data/novelties.json`
- `var/data/menu_items.json`
- `var/data/menu_reactions.json`
- `var/data/blog_posts.json`
- `var/data/blog_comments.json`
- `var/data/blog_reactions.json`
- `var/data/novelty_comments.json`
- `var/data/site_contact.json`
- `var/data/users.json`

Avances:

- Fallbacks iniciales cuando los JSON no existen.
- Escritura con `JSON_PRETTY_PRINT` y `JSON_UNESCAPED_UNICODE`.
- Conteo de reacciones calculado desde archivos separados.

Pendiente:

- Confirmar si estos JSON deben agregarse al repositorio o tratarse como datos locales.
- Validar concurrencia basica si varias personas administran al mismo tiempo.

## Assets

Estado: imagenes locales agregadas.

Ruta principal:

- `public/img/imagenes`

Contenido detectado:

- Novedades: promociones/eventos como cafe con pan, cerveza 2x1, michelada y noche mexicana.
- Blog: imagenes de festejo, felicitacion y visitas.
- Menu manana: enchiladas, huevos rancheros, hotcakes, picadas, desayuno norteno y otros.
- Menu tarde: cortes, hamburguesa, pasta, tacos, pulpo, salmon, bebidas y mas.
- Menu tarde digital: tres imagenes fuente del menu.

Pendiente:

- Normalizar nombres si luego queremos evitar espacios, parentesis o caracteres especiales en rutas.
- Confirmar imagen principal definitiva para cada seccion.

## Frontend e interacciones

Estado: base funcional.

Archivos principales:

- `assets/app.js`
- `assets/styles/app.css`
- `templates/base.html.twig`

Avances:

- Carrusel automatico del hero.
- Busqueda en listados.
- Marcado de novedades vistas con `localStorage`.
- Cuenta regresiva para etiquetas.
- Filtros de menu.
- Cards accesibles por teclado.
- Copiado de telefono.
- Confirmacion de eliminacion.
- Estilos generales para home, admin, cards, menu, blog, contacto y autenticacion.
- Modal lateral para blog/novedades con transicion desde la derecha.
- Transiciones del home simplificadas para evitar saltos y bloqueos al hacer scroll.
- Vistas internas sin scroll-snapping por secciones.
- Publicaciones con comportamiento diferenciado: layout estatico en computadora y abanico tactil en movil.
- Menu responsive con filtros por menu/categoria y categorias plegables.

Pendiente:

- Prueba visual final con el cliente en escritorio y movil.
- Revisar que no haya textos desbordados con contenido real nuevo.
- Revisar dependencias externas Bootstrap/CDN si se desea trabajar 100% local.

## Legado de publicaciones

Estado: conservado como compatibilidad.

Archivos principales:

- `src/Controller/PublicationController.php`
- `templates/publications/index.html.twig`
- `templates/publications/all.html.twig`
- `templates/publications/manage.html.twig`

Avances:

- Rutas antiguas movidas a versiones `-anterior`.
- Rutas viejas principales redirigen hacia novedades.

Pendiente:

- Decidir si se elimina por completo el modulo legado despues de validar que novedades lo reemplaza sin perdida.

## Orden sugerido de trabajo siguiente

1. Ejecutar pruebas basicas del sitio completo: home, menu, novedades, blog, contacto, login y admin.
2. Probar administracion completa y permisos de rutas `/usuario`.
3. Validar contenido real del menu contra imagenes originales.
4. Revisar si se mantienen o eliminan publicaciones legacy.
5. Decidir versionado de JSON generados en `var/data`.
6. Hacer commit limpio cuando el flujo principal este verificado.

## Preguntas abiertas

- El blog debe usar pagina individual, modal en home/listado o ambas?
- Novedades debe permitir comentarios o solo mostrar promociones/eventos?
- Habra WhatsApp oficial del restaurante?
- El admin subira imagenes desde formulario o seguiremos capturando rutas locales?
- Los datos JSON se consideran contenido inicial del repo o datos runtime locales?

## Checklist de verificacion

- [ ] Home carga sin errores.
- [ ] Navegacion lleva a todas las secciones.
- [x] Menu filtra manana/tarde y categorias visibles segun el menu.
- [ ] Estrella de menu suma y quita reaccion.
- [ ] Novedad muestra etiqueta, countdown y estado nueva.
- [ ] Novedad expirada no aparece al cliente.
- [ ] Blog muestra entradas.
- [ ] Corazon de blog suma y quita reaccion.
- [ ] Usuario registrado puede comentar.
- [ ] Visitante anonimo no puede comentar y va a login/registro.
- [ ] Admin puede crear, editar y eliminar novedades.
- [ ] Admin puede crear, editar y eliminar items de menu.
- [ ] Admin puede crear, editar y eliminar entradas de blog.
- [ ] Admin puede eliminar comentarios.
- [ ] Admin puede editar contacto.
- [ ] Telefono se copia desde contacto.
- [ ] Facebook y Google Maps abren correctamente.


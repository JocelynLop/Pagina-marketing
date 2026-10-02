# Checklist Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Hecho Las Tablas]], [[Pendientes Las Tablas]], [[Inicio Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Contacto Las Tablas]], [[Administracion Las Tablas]], [[Autenticacion Las Tablas]]

## Hecho y estructurado

- [x] Home principal creada y conectada a datos del restaurante.
- [x] Navegacion publica definida: quienes somos, novedades, blog, menu y contactanos.
- [x] Modulo de novedades creado con vistas publica/admin.
- [x] Modulo de menu creado con vistas publica/admin.
- [x] Modulo de blog creado con vistas publica/admin.
- [x] Modulo de contacto editable creado.
- [x] Sidebar admin reutilizable creado.
- [x] Login, logout y registro creados.
- [x] Registro con fecha de nacimiento `DD/MM/AAAA`.
- [x] Servicio JSON central creado para datos del restaurante.
- [x] Assets locales organizados por seccion.
- [x] Notas Obsidian separadas y conectadas en nodos.
- [x] Home pulido con transicion de scroll limpia.
- [x] Carrusel de `Lo mas pedido` redisenado como deck de cards.
- [x] Cards de blog/novedades y `Ver mas` ajustadas visualmente.
- [x] Modales laterales de blog/novedades con informacion debajo de la imagen.
- [x] Login y registro con estilo consistente del sitio.
- [x] Home con publicaciones en tres cards estaticas para computadora.
- [x] Home con publicaciones en abanico tactil para movil.
- [x] Cards de publicaciones alineadas al mismo nivel en escritorio.
- [x] Menu publico con tabs manana/tarde, filtros por categoria y categorias plegables.
- [x] Admin de menu con seleccion de menu y categoria/filtro.
- [x] Menu movil abre y cierra correctamente.
- [x] Botones `Ver novedad` y `Ver historia` abren modal.

## Pendiente de validar

- [x] Home carga sin errores visuales principales en revision de navegador.
- [x] Navegacion publica del home desplaza sin bloqueo.
- [x] Modal de blog/novedades abre desde la derecha y se cierra con `X`.
- [x] Login y registro renderizan correctamente en escritorio/movil.
- [x] Menu filtra manana/tarde.
- [x] Menu filtra por categorias segun el menu seleccionado.
- [ ] Estrella de menu suma y quita reaccion.
- [ ] Novedad muestra etiqueta, countdown y estado nueva.
- [ ] Novedad expirada no aparece al cliente.
- [ ] Blog muestra entradas.
- [ ] Corazon de blog suma y quita reaccion.
- [ ] Usuario registrado puede comentar.
- [ ] Visitante anonimo no puede comentar y va a login/registro.
- [ ] Admin puede crear, editar y eliminar novedades.
- [ ] Admin puede crear, editar y eliminar items de menu.
- [ ] Admin puede crear item de menu y guardar categoria correcta.
- [ ] Admin puede crear, editar y eliminar entradas de blog.
- [ ] Admin puede eliminar comentarios.
- [ ] Admin puede editar contacto.
- [ ] Telefono se copia desde contacto.
- [ ] Facebook y Google Maps abren correctamente.
- [ ] Validar flujo completo en dispositivo real.
- [ ] Validar abanico movil en celular fisico.

## Pendiente de decidir

- [ ] Blog en modal, pagina individual o ambos.
- [ ] Comentarios en novedades: si o no.
- [ ] WhatsApp oficial.
- [ ] Carga de imagenes desde admin o rutas locales.
- [ ] Versionado de JSON en `var/data`.
- [ ] Eliminar o conservar publicaciones legacy.

## Orden sugerido

1. Probar administracion completa: login admin, crear, editar y eliminar contenido.
2. Validar contenido real del menu contra imagenes originales.
3. Revisar si se mantienen o eliminan publicaciones legacy.
4. Decidir versionado de JSON generados en `var/data`.
5. Hacer commit limpio cuando el flujo principal este verificado.

## Preguntas abiertas

- El blog debe usar pagina individual, modal en home/listado o ambas?
- Novedades debe permitir comentarios o solo mostrar promociones/eventos?
- Habra WhatsApp oficial del restaurante?
- El admin subira imagenes desde formulario o seguiremos capturando rutas locales?
- Los datos JSON se consideran contenido inicial del repo o datos runtime locales?


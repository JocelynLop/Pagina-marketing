# Pendientes Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Checklist Las Tablas]], [[Hecho Las Tablas]], [[Inicio Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Novedades Las Tablas]], [[Contacto Las Tablas]], [[Administracion Las Tablas]]

Este nodo concentra lo que falta decidir, probar o pulir antes de considerar el proyecto listo.

## Validacion tecnica

- [x] Probar home, modales, login y registro en navegador escritorio/movil.
- [x] Revisar Twig en plantillas publicas principales y autenticacion.
- [x] Revisar sintaxis de `assets/app.js`.
- [x] Compilar assets con AssetMapper.
- [x] Probar home y novedades en navegador despues del redisenio de cards en escritorio.
- [ ] Probar menu, novedades, blog y contacto con contenido real final en navegador.
- [ ] Revisar errores Symfony/Twig en rutas admin.
- [ ] Probar permisos de todas las rutas `/usuario`.
- [ ] Probar login de admin configurado desde `.env`.
- [ ] Confirmar compatibilidad con usuarios antiguos sin fecha de nacimiento.
- [ ] Verificar que las reacciones de menu y blog sumen, quiten y persistan correctamente.
- [ ] Validar que los comentarios del blog funcionen solo con usuario registrado.
- [ ] Probar CRUD admin completo del menu con las nuevas categorias y filtros dependientes del menu seleccionado.

## Visual y experiencia

- [x] Ajustar scroll principal para evitar transiciones bloqueadas o bugeadas.
- [x] Redisenar carrusel de `Lo mas pedido` como deck visual.
- [x] Redisenar modal lateral de blog/novedades.
- [x] Aplicar estilo del sitio a login y registro.
- [ ] Revisar responsive final en movil, tablet y escritorio con contenido definitivo.
- [ ] Validar en celular fisico que Novedades y Blog mantengan abanico tactil y arrastre fluido.
- [ ] Validar Blog en escritorio con las tres cards estaticas y alineadas igual que Novedades.
- [ ] Revisar que no haya textos desbordados en botones, cards, hero, formularios o modales con datos reales nuevos.
- [ ] Pulir textos finales con acentos y tono consistente.
- [ ] Confirmar imagen principal definitiva de cada seccion.
- [ ] Revisar si conviene trabajar Bootstrap/CDN 100% local.

## Contenido y negocio

- [ ] Confirmar precios reales del menu.
- [ ] Completar platillos faltantes detectados en imagenes originales.
- [ ] Confirmar descripciones definitivas de platillos y bebidas.
- [ ] Confirmar clasificacion final de cada platillo/bebida/postre en las categorias del menu.
- [ ] Confirmar si se agregara correo publico.
- [ ] Confirmar numero oficial de WhatsApp antes de activar esa accion.

## Decisiones de producto

- [ ] Decidir si [[Novedades Las Tablas]] tendra comentarios o solo promociones/eventos.
- [ ] Decidir si [[Blog Las Tablas]] abre en modal, pagina individual o ambos.
- [ ] Decidir si el filtro inicial del [[Menu Las Tablas]] depende de la hora real.
- [ ] Decidir si el admin subira imagenes desde formulario o seguira usando rutas locales.
- [ ] Decidir si los JSON de `var/data` se versionan o quedan como runtime local.
- [ ] Decidir si se elimina [[Legacy Publicaciones Las Tablas]] despues de validar novedades.

## Cierre

- [ ] Ejecutar pruebas basicas completas incluyendo admin.
- [ ] Corregir errores encontrados.
- [ ] Hacer commit limpio cuando el flujo principal este verificado.


# Bitacora 2026-09-30 Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Hecho Las Tablas]], [[Pendientes Las Tablas]], [[Checklist Las Tablas]], [[Frontend Las Tablas]], [[Inicio Las Tablas]], [[Novedades Las Tablas]], [[Blog Las Tablas]], [[Menu Las Tablas]], [[Administracion Las Tablas]]

Esta nota registra el corte despues de las correcciones responsivas, el redisenio de las cards de novedades/blog, los ajustes del menu publico y los cambios de clasificacion del menu desde administracion.

## Resumen del avance

Se trabajo principalmente sobre la experiencia publica en home y menu. El sitio ya separa mejor la experiencia entre escritorio y movil: en computadora las publicaciones destacadas se muestran como tres cards estaticas, elegantes y alineadas; en movil se conserva el abanico deslizable con el dedo. Tambien se corrigieron problemas de modales, filtros de menu, botones innecesarios y comportamiento responsive.

## Terminado en este corte

- [x] Novedades y Blog en home dejan de usar abanico en computadora.
- [x] En computadora se muestran solo las primeras tres cards de Novedades y Blog.
- [x] Las tres cards de escritorio quedan alineadas al mismo nivel.
- [x] En movil se conserva el abanico y el cambio por arrastre tactil.
- [x] Se quitaron los textos de ayuda del abanico.
- [x] Los botones `Ver mas novedades` y `Ver mas historias` se movieron fuera del abanico y se suavizo su diseno.
- [x] Se corrigio que `Ver novedad` y `Ver historia` abran el modal desde las cards.
- [x] Se eliminaron botones inferiores de siguiente/anterior del abanico y del deck de platillos.
- [x] Se corrigio el menu movil para que pueda abrir y volver a cerrar.
- [x] Se corrigio el fondo de `Quienes somos` para que no se mezcle visualmente con la seccion anterior.
- [x] Se quitaron los botones del hero principal solicitados por el cliente, dejando el acceso por `Descubrir`.
- [x] En el bloque de menu de la home, `Parrilla` quedo enfocado en cortes/tarde y `Desayunos` en platillos de manana, sin bebidas.
- [x] La imagen duplicada entre parrilla y desayuno fue corregida.
- [x] El menu publico ya no muestra el boton `Todo` como tipo de menu principal.
- [x] El menu publico usa tabs `Menu de manana` y `Menu de tarde`.
- [x] Los filtros de categorias aparecen debajo de los botones de menu y no dentro de cada categoria.
- [x] El filtro activo de un menu ya no contamina al otro menu al cambiar entre manana y tarde.
- [x] Las secciones/categorias del menu son desplegables y plegables.
- [x] Si una categoria tiene un solo platillo, la card mantiene el mismo ancho que las demas.
- [x] Se agrego clasificacion de items de menu para organizar platillos por categorias.
- [x] El admin de menu permite indicar el menu al que pertenece el platillo.
- [x] El admin de menu permite indicar la categoria/filtro del platillo segun el menu seleccionado.
- [x] Los filtros visibles en el menu publico dependen del menu seleccionado.
- [x] Se ajusto responsive general para movil: home, publicaciones, menu y cards.
- [x] Se compilaron assets con AssetMapper despues de los cambios.

## Categorias actuales del menu

Platillos:

- Entradas
- Ensaladas
- Sopas, cremas y pastas
- Platillos del mar
- Aves
- Tacos con y sin queso
- Burritos
- Cortes americanos
- Cortes importados
- Hamburguesas y baguette

Bebidas:

- Cafes
- Jugos
- Cocteleria
- Licores
- Cervezas
- Sodas italianas
- Aguas y refrescos

Postres:

- Postres

## Archivos tocados en este bloque

- `assets/app.js`
- `assets/styles/app.css`
- `templates/restaurant/index.html.twig`
- `templates/menu/index.html.twig`
- `templates/menu/_form_fields.html.twig`
- `templates/novelties/_card.html.twig`
- `templates/blog/_card_modal.html.twig`
- `src/Controller/MenuController.php`
- `src/Service/RestaurantDataStore.php`
- `var/data/menu_items.json`

## Verificaciones realizadas

- [x] Revision visual en navegador de la home.
- [x] Revision visual de Novedades en escritorio.
- [x] Verificacion de modal al hacer clic en `Ver novedad`.
- [x] Revision visual del menu publico.
- [x] `node --check assets\app.js`.
- [x] `php bin\console asset-map:compile`.

## Pendiente real despues de este corte

- [ ] Validar en un celular fisico que el abanico de Novedades y Blog siga funcionando con arrastre tactil.
- [ ] Validar Blog en escritorio con las tres cards estaticas igual que Novedades.
- [ ] Probar CRUD completo del admin de menu con las nuevas categorias.
- [ ] Probar crear un platillo de manana y confirmar que solo aparecen categorias de manana.
- [ ] Probar crear un platillo de tarde y confirmar que solo aparecen categorias de tarde.
- [ ] Revisar contenido real final: nombres, precios, imagenes y categorias.
- [ ] Probar login admin real y permisos de rutas `/usuario`.
- [ ] Probar comentarios y reacciones completas con usuario registrado y visitante anonimo.
- [ ] Decidir si comentarios en novedades se quedan o se eliminan.
- [ ] Decidir si los JSON de `var/data` se versionan o quedan como runtime local.
- [ ] Hacer commit limpio cuando el flujo principal y admin esten validados.

## Notas de cuidado

- No revertir cambios del worktree sin revisar, porque el proyecto tiene cambios acumulados de varios modulos.
- En modo debug, despues de cambios en CSS/JS puede ser necesario limpiar assets compilados en `public/assets` y volver a ejecutar `php bin\console asset-map:compile`.
- WhatsApp sigue pendiente: no activar accion publica hasta tener numero oficial.

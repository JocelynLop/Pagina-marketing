# Menu Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Bitacora 2026-09-29 Las Tablas]], [[Bitacora 2026-09-30 Las Tablas]], [[Inicio Las Tablas]], [[Quienes Somos Las Tablas]], [[Administracion Las Tablas]], [[Datos Las Tablas]], [[Assets Las Tablas]], [[Frontend Las Tablas]]

Estado: modulo construido con pulido visual.

## Archivos principales

- `src/Controller/MenuController.php`
- `templates/menu/index.html.twig`
- `templates/menu/manage.html.twig`
- `templates/menu/edit.html.twig`
- `templates/menu/_form_fields.html.twig`
- `var/data/menu_reactions.json`

## Avances

- Ruta publica `/menu`.
- Panel admin `/usuario/menu`.
- Crear, editar y eliminar platillos o bebidas.
- Tipo de menu: manana o tarde.
- Categoria/filtro de menu asignable por item.
- Cards con imagen, nombre, descripcion, precio, tipo y estrellas.
- Reacciones de estrella para visitantes no registrados mediante identificador anonimo.
- Filtros frontend por tipo de menu y categoria.
- Tabs principales: `Menu de manana` y `Menu de tarde`.
- El boton `Todo` fue retirado de los tabs principales.
- Los filtros de categorias aparecen debajo de los tabs de menu.
- Cada menu muestra solo las categorias/filtros que le corresponden.
- El filtro activo de manana y tarde se maneja por separado.
- Secciones de categorias plegables/desplegables.
- Si una categoria tiene un solo item, su card mantiene el mismo ancho que las demas.
- Datos iniciales cargados desde imagenes locales de desayunos, comida/tarde y bebidas.
- Vista publica con estilo oscuro/elegante alineado a la pagina principal.
- Imagenes de cards ajustadas para reducir cortes visuales incomodos.
- Vista interna sin transicion tipo scroll por secciones.
- `Lo mas pedido` del home usa los platillos destacados en carrusel/deck.

## Categorias

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

## Pendiente

- Completar todos los platillos detectados en imagenes si faltan.
- Verificar precios finales contra el menu real.
- Confirmar clasificacion final de cada item.
- Probar CRUD admin completo con categorias.
- Revisar horario automatico manana/tarde si queremos que el filtro inicial dependa de la hora.


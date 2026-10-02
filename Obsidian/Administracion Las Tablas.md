# Administracion Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Contacto Las Tablas]], [[Autenticacion Las Tablas]], [[Datos Las Tablas]]

Estado: estructura base lista.

## Archivos principales

- `templates/admin/_sidebar.html.twig`
- `templates/admin/_messages.html.twig`
- `templates/admin/contact.html.twig`
- Controladores admin dentro de `NoveltyController`, `MenuController`, `BlogController` y `RestaurantController`.

## Avances

- Sidebar reutilizable con modulos: Novedades, Blog, Menu, Contacto, Vista cliente y Cerrar sesion.
- Formularios con CSRF.
- Confirmacion antes de eliminar elementos.
- Mensajes flash reutilizables.
- Admin de menu permite elegir `Menu de manana` o `Menu de tarde`.
- Admin de menu permite elegir categoria/filtro del item.
- Las categorias disponibles dependen del menu seleccionado al crear o editar.

## Pendiente

- Revisar permisos admin en todos los endpoints `/usuario`.
- Confirmar si el admin necesita carga de archivos real o por ahora basta capturar rutas de imagen.
- Probar CRUD completo con las nuevas categorias del menu.


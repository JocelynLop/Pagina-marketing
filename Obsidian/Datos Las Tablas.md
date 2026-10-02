# Datos Las Tablas

Relacionado con: [[Avances Las Tablas]], [[Novedades Las Tablas]], [[Menu Las Tablas]], [[Blog Las Tablas]], [[Contacto Las Tablas]], [[Autenticacion Las Tablas]]

Estado: servicio central listo.

## Archivo principal

- `src/Service/RestaurantDataStore.php`

## JSON previstos o ya usados

- `var/data/novelties.json`
- `var/data/menu_items.json`
- `var/data/menu_reactions.json`
- `var/data/blog_posts.json`
- `var/data/blog_comments.json`
- `var/data/blog_reactions.json`
- `var/data/novelty_comments.json`
- `var/data/site_contact.json`
- `var/data/users.json`

## Avances

- Fallbacks iniciales cuando los JSON no existen.
- Escritura con `JSON_PRETTY_PRINT` y `JSON_UNESCAPED_UNICODE`.
- Conteo de reacciones calculado desde archivos separados.

## Pendiente

- Confirmar si estos JSON deben agregarse al repositorio o tratarse como datos locales.
- Validar concurrencia basica si varias personas administran al mismo tiempo.

